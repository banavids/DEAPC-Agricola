import paho.mqtt.client as mqtt
import time
import json
import requests
import sqlite3 # <--- ADICIONADO PARA LER A BD

# ==========================================
# 1. CONFIGURAÇÕES
# ==========================================
BROKER = "100.125.153.75"
PORTA = 1883

# ATENÇÃO: Define aqui onde está o teu ficheiro FarmOS.db em relação a este script Python.
# Dependendo de onde corres o simulador, pode ser "bd/FarmOS.db" ou "../bd/FarmOS.db"
CAMINHO_BD = "../bd/FarmOS.db" 

TOPICO_PUB_TEMP = "farmsmart/estufa1/temperatura"
TOPICO_PUB_HUM = "farmsmart/estufa1/humidade"
TOPICO_PUB_SOLO = "farmsmart/estufa1/humidade_solo"
TOPICO_PUB_TEMP_EXT = "farmsmart/estufa1/temperatura_exterior" # Recuperado do passo anterior
TOPICO_PUB_HUM_EXT = "farmsmart/estufa1/humidade_exterior"     # Recuperado do passo anterior
TOPICO_PUB_ESTADO = "farmsmart/estufa1/estado_porta"
TOPICO_PUB_REGA = "farmsmart/estufa1/estado_rega"
TOPICO_SUB_COMANDOS = "farmsmart/estufa1/comandos"

# ==========================================
# 2. VARIÁVEIS GLOBAIS DE ESTADO
# ==========================================
porta_aberta = False
rega_ligada = False

temp_exterior = 15.0
hum_exterior = 60.0

temp_interior = 15.0
hum_interior = 60.0
hum_solo = 40.0 

# ==========================================
# 3. LÓGICA DE BASE DE DADOS (NOVO)
# ==========================================
def carregar_estado_inicial_da_bd():
    global porta_aberta, rega_ligada
    try:
        conn = sqlite3.connect(CAMINHO_BD)
        cursor = conn.cursor()
        # Vai buscar o estado de todos os atuadores
        cursor.execute("SELECT ATU_tipo, ATU_estado FROM tblAtuador")
        registos = cursor.fetchall()
        
        for tipo, estado in registos:
            tipo_limpo = str(tipo).strip().lower()
            estado_limpo = str(estado).strip().lower()
            
            if tipo_limpo == 'rega':
                rega_ligada = (estado_limpo == 'ligado')
            elif tipo_limpo == 'porta':
                porta_aberta = (estado_limpo == 'ligado')
                
        conn.close()
        print(f"📥 [BASE DE DADOS] Sincronizado: Rega está {'LIGADA' if rega_ligada else 'DESLIGADA'} | Porta está {'ABERTA' if porta_aberta else 'FECHADA'}")
    except Exception as e:
        print(f"⚠️ [AVISO] Falha ao ler a BD no caminho '{CAMINHO_BD}'. A usar defaults (OFF). Erro: {e}")

# ==========================================
# 4. LÓGICA DE METEOROLOGIA E MQTT
# ==========================================
def obter_clima_exterior():
    try:
        url = "https://api.open-meteo.com/v1/forecast?latitude=41.1496&longitude=-8.611&current=temperature_2m,relative_humidity_2m"
        resposta = requests.get(url).json()
        
        t_ext = resposta['current']['temperature_2m']
        h_ext = resposta['current']['relative_humidity_2m']
        
        print(f"\n🌍 [METEOROLOGIA ATUALIZADA] Rua: {t_ext}ºC | {h_ext}%\n")
        return t_ext, h_ext
    except Exception as e:
        print(f"⚠️ Erro na API: {e}. A usar valores de backup.")
        return 15.0, 60.0

def on_connect(client, userdata, flags, reason_code, properties):
    print(f"✅ Ligado ao MQTT Broker! À escuta em: {TOPICO_SUB_COMANDOS}")
    client.subscribe(TOPICO_SUB_COMANDOS)

def on_message(client, userdata, msg):
    global porta_aberta, rega_ligada
    comando = msg.payload.decode('utf-8').upper()
    
    if "ABRIR" in comando:
        porta_aberta = True
        print("\n🚪 COMANDO RECEBIDO: A abrir as portas da estufa!\n")
    elif "FECHAR" in comando:
        porta_aberta = False
        print("\n🚪 COMANDO RECEBIDO: A fechar as portas da estufa!\n")
    elif "LIGAR_REGA" in comando:
        rega_ligada = True
        print("\n💧 COMANDO RECEBIDO: Bomba de água LIGADA!\n")
    elif "DESLIGAR_REGA" in comando:
        rega_ligada = False
        print("\n💧 COMANDO RECEBIDO: Bomba de água DESLIGADA!\n")

# ==========================================
# 5. INICIAÇÃO DO SIMULADOR
# ==========================================
print("A iniciar o Simulador Inteligente...")

# 1º Passo: Saber como as coisas estavam antes de ir abaixo
carregar_estado_inicial_da_bd()

# 2º Passo: Ir buscar o clima da rua
temp_exterior, hum_exterior = obter_clima_exterior()
temp_interior = temp_exterior 
hum_interior = hum_exterior

client = mqtt.Client(mqtt.CallbackAPIVersion.VERSION2, "Simulador_Estufa1")
client.on_connect = on_connect
client.on_message = on_message
client.connect(BROKER, PORTA)

client.loop_start() 
print("Simulador a correr. Pressiona CTRL+C para parar.\n")

try:
    ciclo = 0
    while True:
        # --- A. Simulação da Temperatura/Ar ---
        if porta_aberta:
            if temp_interior > temp_exterior: temp_interior -= 0.5 
            if temp_interior < temp_exterior: temp_interior += 0.5 
            
            if hum_interior > hum_exterior: hum_interior -= 0.5
            if hum_interior < hum_exterior: hum_interior += 0.5
        else:
            if temp_interior < (temp_exterior + 10.0): temp_interior += 0.2 
            
            if rega_ligada:
                if hum_interior < 95.0: hum_interior += 0.8
            else:
                if hum_interior > hum_exterior: hum_interior -= 0.1
                
        # --- B. Simulação do Solo (Rega) ---
        if rega_ligada:
            if hum_solo < 100.0: hum_solo += 2.0 
        else:
            taxa_secagem = 0.1 + (temp_interior * 0.01)
            if hum_solo > 10.0: hum_solo -= taxa_secagem
                
        # Formatação para apresentação
        t_int = round(temp_interior, 1)
        h_int = round(hum_interior, 1)
        h_solo = round(hum_solo, 1)
        t_ext = round(temp_exterior, 1)
        h_ext = round(hum_exterior, 1)
        
        # --- C. Publicar via MQTT ---
        client.publish(TOPICO_PUB_TEMP, json.dumps({"valor": t_int, "unidade": "C"}))
        client.publish(TOPICO_PUB_HUM, json.dumps({"valor": h_int, "unidade": "%"}))
        client.publish(TOPICO_PUB_SOLO, json.dumps({"valor": h_solo, "unidade": "%"}))
        
        # (Restaurado)
        client.publish(TOPICO_PUB_TEMP_EXT, json.dumps({"valor": t_ext, "unidade": "C"}))
        client.publish(TOPICO_PUB_HUM_EXT, json.dumps({"valor": h_ext, "unidade": "%"}))
        
        client.publish(TOPICO_PUB_ESTADO, json.dumps({"porta": porta_aberta}))
        client.publish(TOPICO_PUB_REGA, json.dumps({"rega": rega_ligada}))
        
        # --- D. Log Claro no Terminal ---
        str_porta = "Estufa ABERTA " if porta_aberta else "Estufa FECHADA"
        str_rega = "ON " if rega_ligada else "OFF"
        
        print(f"[{str_porta} | Rega {str_rega}] Interior: {t_int}ºC/{h_int}%  |  Exterior: {t_ext}ºC/{h_ext}%  |  Solo: {h_solo}%")
        
        # Atualiza a API do clima a cada ~5 minutos
        ciclo += 1
        if ciclo >= 100: 
            temp_exterior, hum_exterior = obter_clima_exterior()
            ciclo = 0
        
        time.sleep(3)
        
except KeyboardInterrupt:
    print("\nSimulador desligado.")
    client.loop_stop()
    client.disconnect()
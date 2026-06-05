import paho.mqtt.client as mqtt
import sqlite3
import json
import datetime
import os

# 1. TRUQUE DO CAMINHO
PASTA_DO_SCRIPT = os.path.dirname(os.path.abspath(__file__))
PASTA_RAIZ = os.path.dirname(PASTA_DO_SCRIPT)

try:
    with open(os.path.join(PASTA_DO_SCRIPT, 'config.json'), 'r') as f:
        config = json.load(f)
        
    BROKER = config["mqtt_broker"]
    PORTA = config["mqtt_port"]
    
    caminho_bd_config = os.path.normpath(config["bd_caminho"])
    BD_CAMINHO = os.path.join(PASTA_RAIZ, caminho_bd_config)
    
except Exception as e:
    print(f"Erro a ler config.json ou encontrar base de dados: {e}")
    exit()

# 2. O QUE FAZER QUANDO CHEGA UM DADO NOVO
def on_message(client, userdata, msg):
    topico = msg.topic
    payload_str = msg.payload.decode("utf-8")
    agora = datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S")

    try:
        dados_json = json.loads(payload_str)
        valor_final = float(dados_json["valor"])
    except Exception as e:
        print(f"[{agora}] Ignorado: {topico} -> Formato JSON inválido: {payload_str}")
        return

    print(f"[{agora}] A Guardar na BD: {topico} -> {valor_final}")

    partes = topico.split('/')
    if len(partes) >= 3:
        topico_base = f"{partes[0]}/{partes[1]}"
        tipo_sensor = partes[2] 

        try:
            conn = sqlite3.connect(BD_CAMINHO)
            cursor = conn.cursor()

            cursor.execute("SELECT ZON_id FROM tblZona WHERE ZON_topico_base = ?", (topico_base,))
            zona = cursor.fetchone()

            if zona:
                zona_id = zona[0]

                # Histórico
                cursor.execute("""
                    INSERT INTO tblLeituras (LEI_zona_id, LEI_tipo_sensor, LEI_valor, LEI_data_hora)
                    VALUES (?, ?, ?, ?)
                """, (zona_id, tipo_sensor, valor_final, agora))

                # Atualizar estado atual
                cursor.execute("SELECT SNR_id FROM tblSensor WHERE SNR_zona_id = ? AND SNR_tipo = ?", (zona_id, tipo_sensor))
                sensor = cursor.fetchone()

                if sensor:
                    cursor.execute("""
                        UPDATE tblSensor 
                        SET SNR_ultima_leitura = ?, SNR_data_leitura = ?, SNR_estado = 'Online' 
                        WHERE SNR_id = ?
                    """, (valor_final, agora, sensor[0]))
                else:
                    nome_sensor = f"Sensor de {tipo_sensor.capitalize()}"
                    cursor.execute("""
                        INSERT INTO tblSensor (SNR_zona_id, SNR_nome, SNR_tipo, SNR_estado, SNR_ultima_leitura, SNR_data_leitura)
                        VALUES (?, ?, ?, 'Online', ?, ?)
                    """, (zona_id, nome_sensor, tipo_sensor, valor_final, agora))

                conn.commit()
            conn.close()
        except Exception as e:
            print(f"Erro na BD: {e}")

# 3. CONFIGURAR MQTT E SUBSCREVER
client = mqtt.Client(client_id="FarmSmart_Gravador")
client.on_message = on_message

print(f"A ligar ao Broker MQTT em {BROKER}:{PORTA}...")
try:
    client.connect(BROKER, PORTA, 60)
except Exception as e:
    print(f"Falha ao ligar ao MQTT: {e}")
    exit()

# Subscreve os tópicos (ADICIONEI A LINHA DA TEMPERATURA EXTERIOR)
client.subscribe("farmsmart/+/temperatura")
client.subscribe("farmsmart/+/humidade")
client.subscribe("farmsmart/+/humidade_solo")
client.subscribe("farmsmart/+/temperatura_exterior") 

print(f"Gravação Ativa! BD ligada em: {BD_CAMINHO}")
print("À escuta de dados... (Pressiona Ctrl+C para parar)")
client.loop_forever()
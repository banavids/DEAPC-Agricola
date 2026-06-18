import paho.mqtt.client as mqtt
import time
import random
import json

# 1. Configurações
BROKER = "100.125.153.75"
PORTA = 1883

# 2. Iniciar o Cliente MQTT
client = mqtt.Client(mqtt.CallbackAPIVersion.VERSION2, "Simulador_Estufa1")
client.connect(BROKER, PORTA)

print("Simulador da Estufa 1 iniciado. A enviar dados de 3 em 3 segundos...")
print("Pressiona CTRL+C para parar.")

try:
    while True:

        temp = round(random.uniform(20.0, 28.0), 1)
        hum = round(random.uniform(55.0, 75.0), 1)
        

        payload_temp = json.dumps({"valor": temp, "unidade": "C"})
        payload_hum = json.dumps({"valor": hum, "unidade": "%"})
        

        client.publish("farmsmart/estufa1/temperatura", payload_temp)
        client.publish("farmsmart/estufa1/humidade", payload_hum)
        
        print(f"Enviado -> Temp: {temp}ºC | Hum: {hum}%")
        
        time.sleep(3)
        
except KeyboardInterrupt:
    print("\nSimulador desligado.")
    client.disconnect()
<?php
require_once 'database.php';
// Busca a última temperatura da Estufa 1
$res = db_select("SELECT LEI_valor FROM tblLeituras WHERE LEI_tipo_sensor = 'temperatura' AND LEI_zona_id = 1 ORDER BY LEI_data_hora DESC LIMIT 1");
$temp = $res ? $res[0]['LEI_valor'] : 0;
echo json_encode(['temperatura' => $temp]);
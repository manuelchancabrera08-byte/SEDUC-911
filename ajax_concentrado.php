<?php

include "includes/conexion.php";

/* SEGURIDAD */
$ciclo = isset($_GET['ciclo']) ? $conexion->real_escape_string($_GET['ciclo']) : "";
$entidad = isset($_GET['entidad']) ? $conexion->real_escape_string($_GET['entidad']) : "";

/* WHERE DINÁMICO */
$where = [];

if($ciclo != ""){
    $where[] = "CICLO = '$ciclo'";
}

if($entidad != ""){
    $where[] = "ENTIDAD = '$entidad'";
}

$whereSQL = "";

if(count($where)){
    $whereSQL = "WHERE " . implode(" AND ", $where);
}

/* =========================
   CONSULTA PRINCIPAL
========================= */

$sql = "
SELECT *
FROM concentrado_general
$whereSQL
LIMIT 1000
";

$res = $conexion->query($sql);

if(!$res){
    echo "<tr><td colspan='10'>❌ Error SQL: ".$conexion->error."</td></tr>";
    exit;
}

/* =========================
   FILAS
========================= */

if($res->num_rows > 0){

while($row = $res->fetch_assoc()){

echo "<tr>";

foreach($row as $key=>$val){

if($key == "CARGA_ID") continue;

/* Evitar null */
$valor = ($val === null || $val === "") ? "-" : $val;

echo "<td>".htmlspecialchars($valor)."</td>";

}

echo "</tr>";

}

}else{

echo "<tr><td colspan='10'>Sin resultados</td></tr>";

}

/* =========================
   KPIs
========================= */

$sqlKPIs = "
SELECT 
COALESCE(SUM(EXISTENCIA_TOTAL_TOTAL),0) as alumnos,
COALESCE(SUM(EXISTENCIA_TOTAL_HOMBRES),0) as hombres,
COALESCE(SUM(EXISTENCIA_TOTAL_MUJERES),0) as mujeres,
COALESCE(SUM(ESCUELAS_FIN),0) as escuelas,
COALESCE(SUM(DOCENTES_TOTAL),0) as docentes
FROM concentrado_general
$whereSQL
";

$kpis = $conexion->query($sqlKPIs);

$kpiData = [
    "alumnos" => 0,
    "hombres" => 0,
    "mujeres" => 0,
    "escuelas" => 0,
    "docentes" => 0
];

if($kpis && $kpis->num_rows > 0){
    $kpiData = $kpis->fetch_assoc();
}

/* =========================
   RESPUESTA FINAL
========================= */

echo "||KPIS||";
echo json_encode($kpiData);
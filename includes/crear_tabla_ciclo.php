<?php

function crearTablaCiclo($conexion,$ciclo){

$ciclo_tabla = str_replace("-","_",$ciclo);
$tabla = "datos_crudos_".$ciclo_tabla;

$sql = "

CREATE TABLE IF NOT EXISTS `$tabla` (

id BIGINT AUTO_INCREMENT PRIMARY KEY,

entidad VARCHAR(50),
ciclo VARCHAR(9),
formato VARCHAR(50),
cv_cct VARCHAR(20),
variable VARCHAR(80),
valor TEXT,
cuestionario_id INT,
fila_id INT,

INDEX idx_formato (formato),
INDEX idx_ciclo (ciclo),
INDEX idx_cct (cv_cct),
INDEX idx_variable (variable),
INDEX idx_formato_variable (formato,variable),
INDEX idx_formato_cct (formato,cv_cct)

) ENGINE=InnoDB;

";

$conexion->query($sql);

return $tabla;

}

?>
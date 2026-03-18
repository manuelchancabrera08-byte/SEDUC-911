<?php

function eliminarCarga($conexion,$ciclo,$formato,$entidad){

set_time_limit(0);

/* VALIDACIÓN */
if(empty($ciclo) || empty($formato) || empty($entidad)){
    return;
}

/* TABLA SEGÚN CICLO */
$ciclo_tabla = str_replace("-","_",$ciclo);
$tabla = "datos_crudos_".$ciclo_tabla;

/* ELIMINAR DATOS CRUDOS */
$sql = "DELETE FROM `$tabla`
WHERE ciclo=? 
AND formato=? 
AND entidad=?";

$stmt = $conexion->prepare($sql);

if($stmt){
    $stmt->bind_param("sss",$ciclo,$formato,$entidad);
    $stmt->execute();
    $stmt->close();
}

/* ELIMINAR REGISTRO EN CARGAS */
$sql2 = "DELETE FROM cargas
WHERE ciclo=? 
AND formato=? 
AND entidad=?";

$stmt2 = $conexion->prepare($sql2);

if($stmt2){
    $stmt2->bind_param("sss",$ciclo,$formato,$entidad);
    $stmt2->execute();
    $stmt2->close();
}

/* ELIMINAR VISTA */
$formato_vista = strtolower($formato);
$formato_vista = str_replace(" ","_",$formato_vista);

$vista = "vista_".$formato_vista."_".$ciclo_tabla;

$conexion->query("DROP VIEW IF EXISTS `$vista`");

}

?>
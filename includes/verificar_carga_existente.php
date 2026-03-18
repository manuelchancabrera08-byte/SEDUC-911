<?php

function existeCarga($conexion,$ciclo,$formato,$entidad){

/* VALIDACIÓN */
if(empty($ciclo) || empty($formato) || empty($entidad)){
    return false;
}

$sql="SELECT id,formato,ciclo,usuario,fecha_carga,entidad
FROM cargas 
WHERE ciclo=? 
AND formato=? 
AND entidad=? 
AND estado='completo'
LIMIT 1";

$stmt=$conexion->prepare($sql);

if(!$stmt){
    return false;
}

$stmt->bind_param("sss",$ciclo,$formato,$entidad);

if(!$stmt->execute()){
    return false;
}

$result=$stmt->get_result();

if($result->num_rows>0){

$data=$result->fetch_assoc();

$stmt->close();

return $data;

}

$stmt->close();

return false;

}

?>
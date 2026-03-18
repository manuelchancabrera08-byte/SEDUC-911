<?php

function finalizarCarga($conexion,$carga_id,$escuelas,$variables,$registros){

    /* VALIDACION BASICA */
    $carga_id = (int)$carga_id;
    $escuelas = (int)$escuelas;
    $variables = (int)$variables;
    $registros = (int)$registros;

    $sql = "UPDATE cargas
    SET estado='completo',
        progreso=100,
        escuelas=$escuelas,
        variables=$variables,
        registros=$registros,
        fecha_fin=NOW()
    WHERE id=$carga_id";

    $resultado = $conexion->query($sql);

    if(!$resultado){
        die("Error al finalizar carga: ".$conexion->error);
    }

}

?>
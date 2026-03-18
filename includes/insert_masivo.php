<?php

function insertarBloque($conexion,$tabla,$valores,$carga_id){

    if(empty($valores)) return;

    $cantidad = count($valores);
    $valores = array_values($valores);

    $sql = "INSERT INTO `$tabla`
    (entidad,ciclo,formato,cv_cct,variable,valor,cuestionario_id,fila_id)
    VALUES " . implode(",", $valores);

    $resultado = $conexion->query($sql);

    if(!$resultado){
        error_log("ERROR SQL: " . $conexion->error);
        error_log("QUERY: " . substr($sql,0,1000));
        die("Error al insertar bloque. Revisa el log.");
    }

    /* 🔥 ACTUALIZAR PROGRESO */
    $conexion->query("
        UPDATE cargas 
        SET registros = registros + $cantidad 
        WHERE id = $carga_id
    ");

}
?>
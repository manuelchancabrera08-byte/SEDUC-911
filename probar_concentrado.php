<?php

	ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "🔥 INICIO<br>";

function generarConcentrado($conexion, $carga_id){

    echo "🚀 Generando concentrado PRIMARIA GENERAL...<br>";
    flush();

    $carga_id = intval($carga_id);

    /* 🔹 OBTENER CARGA */
    $resCarga = $conexion->query("SELECT * FROM cargas WHERE id = $carga_id");

    if(!$resCarga || $resCarga->num_rows == 0){
        echo "❌ Carga no encontrada<br>";
        return;
    }

    $carga = $resCarga->fetch_assoc();

    $ciclo = str_replace('-', '_', $carga['ciclo']);
    $tabla = "datos_crudos_" . $ciclo;

    echo "📦 Tabla: $tabla<br>";

    /* 🔹 LIMPIAR SOLO PRIMARIA GENERAL */
    $conexion->query("
        DELETE FROM concentrado_general 
        WHERE CARGA_ID = $carga_id 
        AND FORMATO = 'PRIMARIA_GENERAL'
    ");

    /* 🔹 OBTENER MAPEO SOLO DE ESTE FORMATO */
    $mapeo = [];

    $resMap = $conexion->query("
        SELECT variable, descripcion
        FROM mapa_variables_concentrado
        WHERE nombre_concentrado = 'CONCENTRADO_GENERAL'
        AND formato = 'PRIMARIA_GENERAL'
    ");

    while($row = $resMap->fetch_assoc()){
        $mapeo[$row['descripcion']][] = $row['variable'];
    }

    /* 🔹 CONSTRUIR SELECT DINÁMICO */
    $selectVariables = "";

    foreach($mapeo as $desc => $vars){

        $sumParts = [];

        foreach($vars as $v){
            $sumParts[] = "SUM(CASE WHEN d.variable = '$v' THEN IFNULL(d.valor_num,0) ELSE 0 END)";
        }

        $selectVariables .= ", (" . implode(" + ", $sumParts) . ") AS `$desc`";
    }

    $inicio = microtime(true);

    /* 🔥 INSERT FINAL */
    $sql = "
    INSERT INTO concentrado_general (
        CARGA_ID,
        FILA_ID,
        CICLO,
        ENTIDAD,
        FORMATO,
        CV_CCT,
        TURNO,
        NOMBRECT,
        MUNICIPIO,
        LOCALIDAD,
        CONTROL,
        SUBCONTROL,
        TIPO,
        NIVEL,
        SUBNIVEL,
        MODALIDAD,
        ZONA,
        ESCUELAS_INICIO,
        ESCUELAS_FIN
        ".implode(",", array_map(fn($d)=>"`$d`", array_keys($mapeo)))."
    )

    SELECT
        $carga_id,
        d.fila_id,
        d.ciclo,
        d.entidad,
        'PRIMARIA_GENERAL',
        d.cv_cct,

        MAX(CASE WHEN d.variable = 'C_TURNO' THEN d.valor END),
        MAX(CASE WHEN d.variable = 'NOMBRECT' THEN d.valor END),
        MAX(CASE WHEN d.variable = 'C_NOM_MUN' THEN d.valor END),
        MAX(CASE WHEN d.variable = 'C_NOM_LOC' THEN d.valor END),
        MAX(CASE WHEN d.variable = 'CONTROL' THEN d.valor END),
        MAX(CASE WHEN d.variable = 'SUBCONTROL' THEN d.valor END),
        MAX(CASE WHEN d.variable = 'TIPO' THEN d.valor END),
        MAX(CASE WHEN d.variable = 'NIVEL' THEN d.valor END),
        MAX(CASE WHEN d.variable = 'SUBNIVEL' THEN d.valor END),
        MAX(CASE WHEN d.variable = 'C_MODALIDAD' THEN d.valor END),
        MAX(CASE WHEN d.variable = 'ZONA' THEN d.valor END),

        /* ESCUELAS */
        CASE 
            WHEN SUM(CASE WHEN d.variable IN (
                SELECT variable FROM mapa_variables_concentrado 
                WHERE descripcion LIKE 'EXISTENCIA_%'
                AND formato = 'PRIMARIA_GENERAL'
            ) THEN IFNULL(d.valor_num,0) ELSE 0 END) > 0 
            THEN 1 ELSE 0 
        END,

        CASE 
            WHEN SUM(CASE WHEN d.variable IN (
                SELECT variable FROM mapa_variables_concentrado 
                WHERE descripcion LIKE 'EXISTENCIA_%'
                AND formato = 'PRIMARIA_GENERAL'
            ) THEN IFNULL(d.valor_num,0) ELSE 0 END) > 0 
            THEN 1 ELSE 0 
        END

        $selectVariables

    FROM $tabla d

    WHERE d.carga_id = $carga_id
    AND d.formato = 'PRIMARIA_GENERAL'

    GROUP BY 
        d.fila_id,
        d.ciclo,
        d.entidad,
        d.cv_cct
    ";

    if(!$conexion->query($sql)){
        echo "❌ Error: " . $conexion->error . "<br>";
        return;
    }

    $tiempo = round(microtime(true) - $inicio,2);

    echo "✅ PRIMARIA GENERAL generado<br>";
    echo "⏱ Tiempo: {$tiempo} seg<br>";
}
<?php

set_time_limit(0);
ini_set('memory_limit','1024M');

include "includes/conexion.php";

include "includes/catalogo_formatos.php";
include "includes/validar_archivo.php";
include "includes/extraer_zip.php";

include "includes/crear_tabla_ciclo.php";
include "includes/procesar_txt.php";
include "includes/registrar_carga.php";
include "includes/verificar_carga_existente.php";
include "includes/eliminar_carga.php";
include "includes/finalizar_carga.php";
include "includes/generar_vista_horizontal.php";

/* 🔥 VALIDAR INPUTS */
$ciclo   = $_POST['ciclo']   ?? "";
$formato = $_POST['formato'] ?? "";
$archivo = $_POST['archivo'] ?? "";
$entidad = $_POST['entidad'] ?? "";

$usuario = $_SESSION['usuario'] ?? "admin";

/* 🔥 VALIDACIÓN BÁSICA */
if($ciclo=="" || $formato=="" || $archivo==""){

echo json_encode([
"estado"=>"error",
"mensaje"=>"Faltan datos obligatorios"
]);
exit;

}

/* 🔥 RUTA SEGURA */
$rutaArchivo = "uploads/" . basename($archivo);

/* =========================
VALIDAR DUPLICADO
========================= */

$existe = existeCarga($conexion,$ciclo,$formato,$entidad);

if($existe){

echo json_encode([
"estado"=>"duplicado"
]);

exit;

}

/* =========================
REGISTRAR CARGA
========================= */

$carga_id = registrarCarga($conexion,$ciclo,$formato,$archivo,$usuario,$entidad);

/* =========================
CREAR TABLA CICLO
========================= */

$tabla = crearTablaCiclo($conexion,$ciclo);

/* =========================
PROCESAR TXT
========================= */

procesarTXT($conexion,$tabla,$rutaArchivo,$ciclo,$formato,$carga_id);

/* =========================
FINALIZAR CARGA
========================= */

finalizarCarga($conexion,$carga_id,0,0,0);

/* =========================
GENERAR VISTA
========================= */

generarVistaHorizontal($conexion,$tabla,$formato,$ciclo);

/* =========================
RESPUESTA
========================= */

echo json_encode([
"estado"=>"ok"
]);

?>
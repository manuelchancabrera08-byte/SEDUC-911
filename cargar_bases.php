<?php

include "includes/auth.php";
session_write_close();
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

$mensaje="";

/* =========================
   ELIMINAR REGISTRO
========================= */

if(isset($_POST['eliminar'])){

$ciclo=$_POST['ciclo'] ?? "";
$formato=$_POST['formato'] ?? "";
$entidad = $_POST['entidad'] ?? "";

eliminarCarga($conexion,$ciclo,$formato,$entidad);

$mensaje="ELIMINADO";

goto FIN;

}

/* =========================
   CARGAR ARCHIVO
========================= */

if(isset($_POST['cargar'])){

$ciclo = $_POST['ciclo'] ?? "";
$formato = $_POST['formato'] ?? "";
$entidad = "";

$usuario=$_SESSION['usuario'] ?? "admin";

if(!isset($_FILES['archivo']) || $_FILES['archivo']['error']!=0){

$mensaje="ERROR_ARCHIVO";

}else{

$archivo=$_FILES['archivo']['name'];
$tmp=$_FILES['archivo']['tmp_name'];

$extension=strtolower(pathinfo($archivo,PATHINFO_EXTENSION));

if(!validarArchivoFormato($archivo,$formato,$catalogoFormatos)){
$mensaje="FORMATO_INVALIDO";
}
else{

if(!is_dir("uploads")){
mkdir("uploads",0777,true);
}

move_uploaded_file($tmp,"uploads/".$archivo);

$rutaArchivo="uploads/".$archivo;

if($extension=="zip"){

$txt_extraido=extraerZip($rutaArchivo);

if(!$txt_extraido){
die("No se encontró TXT dentro del ZIP");
}

$rutaArchivo=$txt_extraido;

}

/* =========================
   DETECTAR ENTIDAD DESDE TXT
========================= */

$handle=fopen($rutaArchivo,"r");

$encabezado=fgetcsv($handle,0,"|");

$col_entidad=-1;

for($i=0;$i<count($encabezado);$i++){

if(trim($encabezado[$i])=="ENT_ADMINISTRATIVA"){
$col_entidad=$i;
break;
}

}

$fila=fgetcsv($handle,0,"|");

if($col_entidad!=-1 && isset($fila[$col_entidad])){
$entidad=trim($fila[$col_entidad]);
}

/* =========================
   VALIDAR CICLO DEL ARCHIVO
========================= */

$col_fecha=-1;

for($i=0;$i<count($encabezado);$i++){

if(trim($encabezado[$i])=="FECHA_ENTREGA"){
$col_fecha=$i;
break;
}

}

if($col_fecha!=-1 && isset($fila[$col_fecha])){

$fecha_txt=trim($fila[$col_fecha]);

$partes=explode("/",$fecha_txt);

if(count($partes)==3){

$anio_archivo=$partes[2];

$ciclo_archivo=$anio_archivo."-".($anio_archivo+1);

if($ciclo_archivo!=$ciclo){

$mensaje="CICLO_INVALIDO";

fclose($handle);

goto FIN;

}

}

}

/* =========================
   VALIDAR DUPLICADO
========================= */

$existe = existeCarga($conexion,$ciclo,$formato,$entidad);

if($existe){

$mensaje="EXISTE";
$registro_existente=$existe;

}else{

$carga_id=registrarCarga($conexion,$ciclo,$formato,$archivo,$usuario,$entidad);

$tabla=crearTablaCiclo($conexion,$ciclo);

procesarTXT($conexion,$tabla,$rutaArchivo,$ciclo,$formato,$carga_id);

finalizarCarga($conexion,$carga_id,0,0,0);

generarVistaHorizontal($conexion,$tabla,$formato,$ciclo);

$mensaje="OK";

}

}

}

}

FIN:

?>

<!DOCTYPE html>
<html>

<head>

<title>Cargar bases</title>

<link rel="stylesheet" href="assets/css/estilos.css">

<style>

.page-content{
display:flex;
justify-content:center;
align-items:flex-start;
padding:100px 50px;
}

.modal{
position:fixed;
top:0;
left:0;
width:100%;
height:100%;
background:rgba(0,0,0,.55);
display:none;
justify-content:center;
align-items:center;
z-index:9999;
}

.modal-content{
background:white;
padding:35px;
border-radius:12px;
width:420px;
box-shadow:0 20px 60px rgba(0,0,0,.25);
text-align:center;
position:relative;
}

.modal-info{
text-align:left;
margin-top:15px;
line-height:1.7;
font-size:14px;
}

.modal-buttons{
margin-top:20px;
display:flex;
gap:10px;
justify-content:center;
}

.barra{
background:#eee;
height:20px;
border-radius:6px;
overflow:hidden;
margin-top:20px;
position:relative;
}

.barra-interna{
height:20px;
width:0%;
background:#6f1024;
transition:width .4s;
}

#porcentajeProgreso{
position:absolute;
left:50%;
top:0;
transform:translateX(-50%);
font-size:13px;
font-weight:bold;
line-height:20px;
color:#fff;
text-shadow:0 0 3px rgba(0,0,0,.9);
}

/* =========================
   ANIMACION PAPELERA PRO
========================= */

.papelera{
font-size:70px;
margin-top:10px;
}

.papel{
font-size:28px;
position:absolute;
top:-10px;
left:50%;
transform:translateX(-50%);
animation:caerPapel 1.2s linear infinite;
}

@keyframes caerPapel{

0%{
top:-20px;
opacity:0;
}

30%{
opacity:1;
}

100%{
top:60px;
opacity:0;
}

}

/* desaparecer modal duplicado */

.ocultarModal{
animation:fadeOut .3s forwards;
}

@keyframes fadeOut{
to{
opacity:0;
transform:scale(.9);
}
}

</style>

</head>

<body>

<?php include "includes/header.php"; ?>

<section class="page-content">

<div class="card card-large">

<div class="card-header">

<h2>Cargar Bases de datos</h2>
<p>Seleccione el ciclo escolar y el formato correspondiente</p>

</div>

<form method="post" enctype="multipart/form-data" id="formCarga">

<div class="form-group">

<label>Ciclo Escolar</label>

<select name="ciclo" id="ciclo" required>

<?php

$anio=date("Y");
$mes=date("n");

$inicio=($mes>=8)?$anio:$anio-1;
$fin=$inicio+1;

$ciclo_actual="$inicio-$fin";

for($a=2018;$a<=date("Y")+1;$a++){

$b=$a+1;
$c="$a-$b";

$selected=($c==$ciclo_actual)?"selected":"";

echo "<option value='$c' $selected>$c</option>";

}

?>

</select>

</div>

<div class="form-group">

<label>Formato</label>

<select name="formato" id="formato" required>

<option value="">Seleccione formato</option>

<?php
foreach($catalogoFormatos as $clave=>$nombre){
echo "<option value='$clave'>$clave - $nombre</option>";
}
?>

</select>

</div>

<div class="form-group">

<label>Archivo (.txt o .zip)</label>

<input type="file" name="archivo" required>

</div>

<div class="form-submit">

<button class="btn btn-primary" name="cargar">
Subir
</button>

</div>

</form>

</div>

</section>

<!-- PROGRESO -->

<div id="modalProgreso" class="modal">

<div class="modal-content">

<h3>Cargando base de datos</h3>

<p>El sistema está procesando el archivo...</p>

<div class="barra">

<div class="barra-interna" id="barraProgreso"></div>

<span id="porcentajeProgreso">0%</span>

</div>

</div>

</div>

<!-- ELIMINANDO -->

<div id="modalEliminando" class="modal">

<div class="modal-content">

<div class="papel">📄</div>

<div class="papelera">🗑️</div>

<h3>Eliminando registro</h3>

<p>El sistema está eliminando la base de datos...</p>

</div>

</div>

<?php if($mensaje=="FORMATO_INVALIDO"){ ?>

<div class="modal" style="display:flex">

<div class="modal-content">

<h3>Archivo incorrecto</h3>

<p>El archivo no corresponde al formato seleccionado.</p>

<div class="modal-buttons">
<a href="cargar_bases.php" class="btn btn-primary">Aceptar</a>
</div>

</div>

</div>

<?php } ?>

<?php if($mensaje=="CICLO_INVALIDO"){ ?>

<div class="modal" style="display:flex">

<div class="modal-content">

<h3>Ciclo incorrecto</h3>

<p>El archivo no corresponde al ciclo escolar seleccionado.</p>

<div class="modal-buttons">
<a href="cargar_bases.php" class="btn btn-primary">Aceptar</a>
</div>

</div>

</div>

<?php } ?>

<?php if($mensaje=="EXISTE"){ ?>

<div class="modal" style="display:flex;" id="modalDuplicado">

<div class="modal-content">

<h3>Formato duplicado</h3>

<div class="modal-info">

<p><strong>Formato:</strong> <?php echo $registro_existente['formato']; ?></p>
<p><strong>Ciclo:</strong> <?php echo $registro_existente['ciclo']; ?></p>
<p><strong>Fecha carga:</strong> <?php echo $registro_existente['fecha_carga']; ?></p>

</div>

<div class="modal-buttons">

<button onclick="location.href='cargar_bases.php'" class="btn">Cancelar</button>

<form method="post" onsubmit="return iniciarEliminacion(this)">

<input type="hidden" name="eliminar" value="1">
<input type="hidden" name="ciclo" value="<?php echo $registro_existente['ciclo']; ?>">
<input type="hidden" name="formato" value="<?php echo $registro_existente['formato']; ?>">
<input type="hidden" name="entidad" value="<?php echo $registro_existente['entidad']; ?>">

<button class="btn btn-danger">Eliminar</button>

</form>
</div>

</div>

</div>

<?php } ?>

<?php if($mensaje=="ELIMINADO"){ ?>

<div class="modal" style="display:flex">

<div class="modal-content">

<h3>Registro eliminado</h3>

<p>La base fue eliminada correctamente.</p>

<div class="modal-buttons">
<a href="cargar_bases.php" class="btn btn-primary">Aceptar</a>
</div>

</div>

</div>

<?php } ?>

<?php if($mensaje=="OK"){ ?>

<div class="modal" style="display:flex">

<div class="modal-content">

<h3>Carga finalizada correctamente</h3>

<p>La base se cargó correctamente.</p>

<div class="modal-buttons">
<a href="historial.php?ciclo=<?php echo $ciclo; ?>&entidad=<?php echo $entidad; ?>" class="btn btn-primary">
Ver historial
</a>
<a href="cargar_bases.php" class="btn">Cargar otra base</a>
</div>

</div>

</div>

<?php } ?>

<script>

/* PROGRESO REAL */

document.getElementById("formCarga").addEventListener("submit", function(){

    mostrarProgreso();

    setTimeout(() => {

        let barra = document.getElementById("barraProgreso");
        let texto = document.getElementById("porcentajeProgreso");

        progreso = 100;

        barra.style.width = "100%";
        barra.style.background = "#28a745"; // verde

        texto.innerText = "100%";

    }, 5000); // ajusta a tu tiempo real

});

/* ANIMACION ELIMINACION */

function iniciarEliminacion(form){

let modalDuplicado=document.getElementById("modalDuplicado");

if(modalDuplicado){
modalDuplicado.classList.add("ocultarModal");
}

setTimeout(function(){

let modal=document.getElementById("modalEliminando");

modal.style.display="flex";

},250);

setTimeout(function(){
form.submit();
},600);

return false;

}

</script>

</body>
</html>
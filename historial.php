<?php

include "includes/auth.php";
include "includes/conexion.php";
include "includes/eliminar_carga.php";
include "includes/catalogo_formatos.php";

$ciclo = $_GET['ciclo'] ?? "";
$entidad = $_GET['entidad'] ?? "";

if($ciclo==""){

$anio=date("Y");
$mes=date("n");

if($mes>=8){
$ciclo=$anio."-".($anio+1);
}else{
$ciclo=($anio-1)."-".$anio;
}

}

/* =========================
   ENTIDADES DISPONIBLES
========================= */

$entidades=[];

$sqlEnt="SELECT DISTINCT entidad 
FROM cargas
WHERE entidad IS NOT NULL
AND estado='completo'
ORDER BY entidad";

$resEnt=$conexion->query($sqlEnt);

if($resEnt){
while($row=$resEnt->fetch_assoc()){
$entidades[]=$row['entidad'];
}
}

/* =========================
   SI NO SELECCIONA ENTIDAD
   USAR LA PRIMERA
========================= */

if($entidad=="" && count($entidades)>0){
$entidad=$entidades[0];
}

// SI LA ENTIDAD NO EXISTE (ej: se eliminó COLIMA)
if($entidad!="" && !in_array($entidad,$entidades)){
$entidad = count($entidades)>0 ? $entidades[0] : "";
}

/* =========================
   ELIMINAR REGISTRO
========================= */

if(isset($_POST['eliminar'])){

$formato=$_POST['formato'] ?? "";
$ciclo=$_POST['ciclo'] ?? "";
$entidad=$_POST['entidad'] ?? "";

eliminarCarga($conexion,$ciclo,$formato,$entidad);

header("Location: historial.php?ciclo=".$ciclo."&entidad=".$entidad);
exit;

}

/* =========================
   CARGAS EXISTENTES
========================= */

$where="WHERE ciclo='$ciclo' AND estado='completo'";

if($entidad!=""){
$where.=" AND entidad='$entidad'";
}

$sql="SELECT formato,fecha_carga FROM cargas $where";
$res=$conexion->query($sql);

$cargados=[];

if($res){
while($row=$res->fetch_assoc()){
$cargados[$row['formato']]=$row['fecha_carga'];
}
}

$formato_total=count($catalogoFormatos);

$total_cargados=count($cargados);
$pendientes=$formato_total-$total_cargados;

$avance=0;

if($formato_total>0){
$avance=round(($total_cargados/$formato_total)*100);
}

?>

<!DOCTYPE html>
<html>

<head>

<title>Historial de cargas</title>

<link rel="stylesheet" href="assets/css/estilos.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>

body{
background:#e9edf2;
font-family:system-ui;
margin:0;
}

.hero{
background:#7a1a2a;
padding:35px 40px 70px 40px;
color:white;
}

.hero-title{
text-align:center;
font-size:28px;
font-weight:700;
margin-bottom:25px;
}

.hero-row{
display:flex;
align-items:center;
gap:20px;
justify-content:center;
}

.hero-divider{
height:50px;
width:1px;
background:rgba(255,255,255,.3);
}

.stat{
background:#f2f3f5;
padding:12px 25px;
border-radius:8px;
text-align:center;
min-width:120px;
}

.stat small{
display:block;
font-size:12px;
color:#555;
}

.stat strong{
font-size:22px;
color:#222;
}

.ciclo-box{
background:#f2f3f5;
padding:9px 25px;
border-radius:8px;
min-width:120px;
text-align:center;
display:flex;
flex-direction:column;
justify-content:center;
}

.ciclo-box label{
font-size:12px;
color:#444;
margin-bottom:4px;
}

.ciclo-box select{
width:100%;
padding:6px;
border-radius:5px;
border:1px solid #ccc;
}

.panel{
max-width:1200px;
margin:auto;
margin-top:-40px;
background:white;
border-radius:12px;
padding:25px;
box-shadow:0 8px 25px rgba(0,0,0,.08);
}

.grid{
display:grid;
grid-template-columns:1.6fr 1fr;
gap:10px;
align-items:start;
}

.panel h3{
color:#7a1a2a;
margin-bottom:15px;
}

table{
width:100%;
border-collapse:collapse;
table-layout:fixed;
}

thead th{
background:#d9dce1;
padding:8px 10px;
text-align:left;
font-size:12px;
}

tbody td{
padding:6px 10px;
border-bottom:1px solid #eee;
font-size:13px;
}

table th:nth-child(1),
table td:nth-child(1){ width:70%; }

table th:nth-child(2),
table td:nth-child(2){ width:18%; }

table th:nth-child(3),
table td:nth-child(3){ width:30%; }

table th:nth-child(4),
table td:nth-child(4){
width:10%;
white-space:nowrap;
}

.badge-ok{
background:#e6f4ea;
color:#2e7d32;
padding:4px 8px;
border-radius:6px;
font-size:12px;
font-weight:600;
}

.badge-pend{
background:#fdecea;
color:#c62828;
padding:4px 8px;
border-radius:6px;
font-size:12px;
font-weight:600;
}

.btn-delete{
color:#c62828;
font-weight:600;
cursor:pointer;
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
width:380px;
box-shadow:0 20px 60px rgba(0,0,0,.25);
text-align:center;
position:relative;
}

.modal-buttons{
margin-top:20px;
display:flex;
gap:10px;
justify-content:center;
}

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

0%{ top:-20px; opacity:0; }
30%{ opacity:1; }
100%{ top:60px; opacity:0; }

}

.ocultarModal{
animation:fadeOut .3s forwards;
}

@keyframes fadeOut{
to{
opacity:0;
transform:scale(.9);
}
}

.chart{
display:flex;
flex-direction:column;
align-items:center;
justify-content:center;
}

.chart canvas{
max-width:420px;
max-height:420px;
}

</style>

</head>

<body>

<?php include "includes/header.php"; ?>

<section class="hero">

<div class="hero-title">
Historial de Cargas
</div>

<div class="hero-row">

<div class="hero-divider"></div>

<div class="ciclo-box">

<label>Entidad</label>

<select onchange="cambiarEntidad(this.value)">

<?php

foreach($entidades as $e){

$sel=$e==$entidad?"selected":"";

echo "<option value='$e' $sel>$e</option>";

}

?>

</select>

</div>

<div class="ciclo-box">

<label>Ciclo escolar</label>

<select onchange="cambiarCiclo(this.value)">

<?php

for($a=2018;$a<=date("Y")+1;$a++){

$b=$a+1;
$c="$a-$b";

$sel=$c==$ciclo?"selected":"";

echo "<option value='$c' $sel>$c</option>";

}

?>

</select>

</div>

<div class="stat">
<small>Total formatos</small>
<strong><?php echo $formato_total ?></strong>
</div>

<div class="stat">
<small>Cargados</small>
<strong><?php echo $total_cargados ?></strong>
</div>

<div class="stat">
<small>Pendientes</small>
<strong><?php echo $pendientes ?></strong>
</div>

<div class="stat">
<small>Avance</small>
<strong><?php echo $avance ?>%</strong>
</div>

</div>

</section>

<div class="panel">

<div class="grid">

<div>

<h3>Estado de formatos</h3>

<table>

<thead>
<tr>
<th>Formato</th>
<th>Estado</th>
<th>Fecha</th>
<th>Acción</th>
</tr>
</thead>

<tbody>

<?php

foreach($catalogoFormatos as $formato => $nombre){

$nombreBonito = ucwords(strtolower(str_replace("_"," ",$nombre)));

if(isset($cargados[$formato])){

$estado="<span class='badge-ok'>Cargado</span>";
$fecha=$cargados[$formato];

echo "<tr>
<td>$nombreBonito</td>
<td>$estado</td>
<td>$fecha</td>
<td>
<span class='btn-delete' onclick=\"confirmarEliminar('$formato')\">🗑 Eliminar</span>
</td>
</tr>";

}else{

echo "<tr>
<td>$nombreBonito</td>
<td><span class='badge-pend'>Pendiente</span></td>
<td>-</td>
<td>-</td>
</tr>";

}

}

?>

</tbody>

</table>

</div>

<div class="chart">

<h3>Progreso</h3>
<canvas id="grafica"></canvas>

</div>

</div>

</div>

<!-- MODAL CONFIRMAR -->

<div id="modalEliminar" class="modal">

<div class="modal-content">

<h3>Eliminar formato</h3>

<p>¿Deseas eliminar esta carga?</p>

<form method="post">

<input type="hidden" name="eliminar" value="1">
<input type="hidden" name="ciclo" value="<?php echo $ciclo ?>">
<input type="hidden" name="entidad" value="<?php echo $entidad ?>">
<input type="hidden" id="formatoEliminar" name="formato">

<div class="modal-buttons">

<button type="button" onclick="cerrarModal()">Cancelar</button>

<button class="btn-delete" onclick="return iniciarEliminacion(this.form)">
Eliminar
</button>

</div>

</form>

</div>

</div>

<!-- MODAL ELIMINANDO -->

<div id="modalEliminando" class="modal">

<div class="modal-content">

<div class="papel">📄</div>
<div class="papelera">🗑️</div>

<h3>Eliminando registro</h3>
<p>El sistema está eliminando la base de datos...</p>

</div>

</div>

<script>

function cambiarCiclo(ciclo){

let entidad="<?php echo $entidad ?>";

window.location="historial.php?ciclo="+ciclo+"&entidad="+entidad;

}

function cambiarEntidad(entidad){

let ciclo="<?php echo $ciclo ?>";

window.location="historial.php?ciclo="+ciclo+"&entidad="+entidad;

}

function confirmarEliminar(formato){

document.getElementById("modalEliminar").style.display="flex";
document.getElementById("formatoEliminar").value=formato;

}

function cerrarModal(){
document.getElementById("modalEliminar").style.display="none";
}

function iniciarEliminacion(form){

let modalConfirmacion=document.getElementById("modalEliminar");

modalConfirmacion.classList.add("ocultarModal");

setTimeout(function(){

let modal=document.getElementById("modalEliminando");

modal.style.display="flex";

},250);

setTimeout(function(){

form.submit();

},600);

return false;

}

const ctx=document.getElementById('grafica');

new Chart(ctx,{
type:'doughnut',
data:{
labels:['Cargados','Pendientes'],
datasets:[{
data:[<?php echo $total_cargados ?>,<?php echo $pendientes ?>],
backgroundColor:['#2e7d32','#d32f2f'],
borderWidth:0
}]
},
options:{
cutout:'65%',
plugins:{
legend:{position:'bottom'}
}
}
});

</script>

</body>
</html>
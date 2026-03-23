<?php
include "includes/auth.php";
include "includes/conexion.php";

/* CICLOS */
$ciclos = $conexion->query("SELECT DISTINCT CICLO FROM concentrado_general ORDER BY CICLO DESC");

/* ENTIDADES */
$entidades = $conexion->query("SELECT DISTINCT ENTIDAD FROM concentrado_general ORDER BY ENTIDAD ASC");

/* COLUMNAS */
$resCols = $conexion->query("SHOW COLUMNS FROM concentrado_general");

$columnas = [];

while($col = $resCols->fetch_assoc()){
    if($col['Field'] == 'CARGA_ID') continue;
    $columnas[] = $col['Field'];
}

$totalColumnas = count($columnas);
?>

<!DOCTYPE html>
<html>

<head>
<title>Concentrado General</title>

<link rel="stylesheet" href="assets/css/estilos.css">

<style>

/* CSS */

body{
background:#eef2f6;
font-family:system-ui;
margin:0;
}

.banner-reportes{
background:#6f1024;
color:white;
padding:18px 30px;
font-size:20px;
font-weight:600;
}

.barra-superior{
display:flex;
justify-content:space-between;
align-items:center;
max-width:95%;
margin:20px auto;
gap:20px;
flex-wrap:wrap;
}

.busqueda input{
padding:10px;
border-radius:8px;
border:1px solid #ccc;
width:260px;
}

.filtros{
display:flex;
gap:10px;
align-items:center;
}

.filtros select{
padding:10px;
border-radius:8px;
border:1px solid #ccc;
min-width:200px;
}

.btn-consultar{
background:#e0e0e0;
color:#222;
border:none;
padding:10px 22px;
border-radius:8px;
cursor:pointer;
font-weight:600;
height:42px;
}

.btn-consultar:hover{
background:#6f1024;
color:white;
}

.kpis{
display:flex;
gap:10px;
max-width:95%;
margin:10px auto;
}

.kpi{
background:white;
padding:10px;
border-radius:8px;
flex:1;
text-align:center;
}

.kpi span{
font-size:18px;
font-weight:bold;
color:#6f1024;
}

.contenedor-tabla{
max-width:95%;
margin:auto;
overflow:auto;
max-height:70vh;
background:white;
}

.tabla{
border-collapse:collapse;
font-size:11px;
width:100%;
}

.tabla th, .tabla td{
border:1px solid #ddd;
padding:5px;
text-align:center;
}

.tabla th{
background:#6f1024;
color:white;
position:sticky;
top:0;
}

.tabla th:first-child,
.tabla td:first-child{
position:sticky;
left:0;
background:white;
z-index:2;
}

.tabla th:first-child{
background:#6f1024;
color:white;
z-index:3;
}

.acciones{
display:flex;
justify-content:center;
gap:15px;
margin:40px 0 30px;
}

.acciones button{
padding:12px 25px;
font-size:14px;
border:none;
border-radius:8px;
cursor:pointer;
background:#dcdcdc;
color:#333;
transition:.2s;
min-width:140px;
}

.acciones button:hover{
background:#6f1024;
color:white;
transform:scale(1.05);
}

.contenedor-tabla{
margin-bottom:30px;
}

/* FILTROS */
.th-contenido{
display:flex;
justify-content:space-between;
align-items:center;
}

.filtro-icono{
cursor:pointer;
font-size:10px;
}

.filtro-box{
position:absolute;
background:#fff;
border:1px solid #a6a6a6;
width:240px;
z-index:9999;
font-size:12px;
box-shadow:0 6px 15px rgba(0,0,0,.25);
}

/* buscador tipo excel */
.filtro-buscar{
width:100%;
padding:6px 28px 6px 8px;
border:none;
border-bottom:1px solid #ddd;
outline:none;
font-size:12px;
background:url('https://cdn-icons-png.flaticon.com/512/622/622669.png') no-repeat right 6px center;
background-size:14px;
}

/* lista */
.filtro-lista{
max-height:220px;
overflow:auto;
padding:4px;
}

/* items compactos */
.filtro-lista label{
display:flex;
align-items:center;
gap:6px;
padding:2px 4px;
cursor:pointer;
font-size:12px;
}

.filtro-lista input{
width:14px;
height:14px;
}

/* footer */
.filtro-footer{
display:flex;
justify-content:flex-end;
gap:6px;
padding:6px;
border-top:1px solid #ddd;
background:#f3f3f3;
}

.filtro-footer button{
padding:4px 10px;
font-size:12px;
border:1px solid #aaa;
background:#e6e6e6;
cursor:pointer;
}

.filtro-footer button:hover{
background:#6f1024;
color:#fff;
}

</style>

</head>

<body>

<?php include "includes/header.php"; ?>

<div class="banner-reportes">Concentrado general</div>

<div class="barra-superior">

<div class="busqueda">
<input type="text" id="buscarTabla" placeholder="Buscar..." onkeyup="filtrarTabla()">
</div>

<div class="filtros">

<select id="ciclo">
<option value="">Ciclo</option>
<?php while($c = $ciclos->fetch_assoc()){ ?>
<option value="<?php echo $c['CICLO']; ?>"><?php echo $c['CICLO']; ?></option>
<?php } ?>
</select>

<select id="entidad">
<option value="">Entidad</option>
<?php while($e = $entidades->fetch_assoc()){ ?>
<option value="<?php echo $e['ENTIDAD']; ?>"><?php echo $e['ENTIDAD']; ?></option>
<?php } ?>
</select>

<button class="btn-consultar" onclick="cargarTabla()">Consultar</button>

</div>
</div>

<div class="kpis">
<div class="kpi"><span id="kpi_alumnos">0</span><p>Alumnos</p></div>
<div class="kpi"><span id="kpi_hombres">0</span><p>Hombres</p></div>
<div class="kpi"><span id="kpi_mujeres">0</span><p>Mujeres</p></div>
<div class="kpi"><span id="kpi_escuelas">0</span><p>Escuelas</p></div>
<div class="kpi"><span id="kpi_docentes">0</span><p>Docentes</p></div>
</div>

<div class="contenedor-tabla">
<table class="tabla" id="tablaDatos">
<thead>
<tr>
<?php foreach($columnas as $i => $col){ ?>
<th>
<div class="th-contenido">
<span><?php echo $col; ?></span>
<span class="filtro-icono" onclick="abrirFiltro(event, <?php echo $i; ?>)">▼</span>
</div>
</th>
<?php } ?>
</tr>
</thead>

<tbody id="bodyTabla">
<tr><td colspan="<?php echo $totalColumnas; ?>">Seleccione filtros</td></tr>
</tbody>
</table>
</div>

<div class="acciones">
<button onclick="window.print()">Imprimir</button>
<button onclick="exportarExcel()">Excel</button>
<button onclick="pantallaCompleta()">Pantalla completa</button>
</div>

<script>

let filtroActivo = null;
let filtrosAplicados = {};

/* 🔥 FILTRO EXCEL REAL */
function abrirFiltro(e, col){

e.stopPropagation();

if(filtroActivo) filtroActivo.remove();

let box = document.createElement("div");
box.className = "filtro-box";

/* BUSCADOR */
let input = document.createElement("input");
input.className = "filtro-buscar";
input.placeholder = "Buscar...";
box.appendChild(input);

/* LISTA */
let lista = document.createElement("div");
lista.className = "filtro-lista";

/* VALORES */
let valores = new Set();

document.querySelectorAll("#bodyTabla tr").forEach(tr=>{
let td = tr.children[col];
if(td) valores.add(td.innerText.trim());
});

/* SELECT ALL */
let selectAll = document.createElement("label");
selectAll.innerHTML = `<input type="checkbox" checked><span><b>(Seleccionar todo)</b></span>`;
lista.appendChild(selectAll);

/* ITEMS ORDENADOS */
[...valores].sort().forEach(v=>{
let item = document.createElement("label");
item.innerHTML = `<input type="checkbox" value="${v}" checked><span>${v}</span>`;
lista.appendChild(item);
});

/* FOOTER */
let footer = document.createElement("div");
footer.className = "filtro-footer";

let btnOk = document.createElement("button");
btnOk.innerText = "Aceptar";

let btnCancel = document.createElement("button");
btnCancel.innerText = "Cancelar";

footer.appendChild(btnOk);
footer.appendChild(btnCancel);

box.appendChild(lista);
box.appendChild(footer);

box.style.top = e.pageY+"px";
box.style.left = e.pageX+"px";

document.body.appendChild(box);
filtroActivo = box;

/* EVITAR CIERRE */
box.onclick = (e)=>e.stopPropagation();

/* SELECT ALL */
selectAll.querySelector("input").onchange = function(){
lista.querySelectorAll("input").forEach(ch=>{
ch.checked = this.checked;
});
};

/* BUSCAR */
input.onkeyup = ()=>{
let t = input.value.toLowerCase();
lista.querySelectorAll("label").forEach(l=>{
l.style.display = l.innerText.toLowerCase().includes(t) ? "" : "none";
});
};

/* ACEPTAR */
btnOk.onclick = ()=>{

let seleccionados = [];

lista.querySelectorAll("input").forEach(ch=>{
if(ch.checked && ch.value){
seleccionados.push(ch.value);
}
});

filtrosAplicados[col] = seleccionados;
aplicarFiltros();
box.remove();

};

/* CANCELAR */
btnCancel.onclick = ()=> box.remove();

}

/* 🔥 APLICAR FILTROS CORREGIDO */
function aplicarFiltros(){

document.querySelectorAll("#bodyTabla tr").forEach(tr=>{

let visible = true;

for(let col in filtrosAplicados){

let td = tr.children[col];

if(td && filtrosAplicados[col].length && !filtrosAplicados[col].includes(td.innerText.trim())){
visible = false;
break;
}

}

tr.style.display = visible ? "" : "none";

});

}

/* CERRAR */
document.addEventListener("click",()=>{
if(filtroActivo) filtroActivo.remove();
});

function cargarTabla(){

filtrosAplicados = {}; // 🔥 reset

let ciclo = document.getElementById("ciclo").value;
let entidad = document.getElementById("entidad").value;

let body = document.getElementById("bodyTabla");

body.innerHTML = "<tr><td colspan='<?php echo $totalColumnas; ?>'>Cargando...</td></tr>";

fetch("ajax_concentrado.php?ciclo="+ciclo+"&entidad="+entidad)
.then(res => res.text())
.then(data => {

let partes = data.split("||KPIS||");

body.innerHTML = partes[0];

if(partes[1]){
let k = JSON.parse(partes[1]);

kpi_alumnos.innerText = k.alumnos ?? 0;
kpi_hombres.innerText = k.hombres ?? 0;
kpi_mujeres.innerText = k.mujeres ?? 0;
kpi_escuelas.innerText = k.escuelas ?? 0;
kpi_docentes.innerText = k.docentes ?? 0;
}

});

}

function filtrarTabla(){
let f = buscarTabla.value.toLowerCase();
document.querySelectorAll("#bodyTabla tr").forEach(tr=>{
tr.style.display = tr.innerText.toLowerCase().includes(f) ? "" : "none";
});
}

function exportarExcel(){
let tabla = tablaDatos.outerHTML;
let url = 'data:application/vnd.ms-excel,' + encodeURIComponent(tabla);
let a = document.createElement("a");
a.href = url;
a.download = "concentrado.xls";
a.click();
}

function pantallaCompleta(){
document.documentElement.requestFullscreen();
}

</script>

</body>
</html>
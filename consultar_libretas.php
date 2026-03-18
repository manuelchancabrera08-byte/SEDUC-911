<?php
include "includes/auth.php";

/* CICLO ESCOLAR AUTOMÁTICO */

$ciclo = $_GET['ciclo'] ?? "";

if($ciclo==""){

$anio=date("Y");
$mes=date("n");

if($mes>=8){
$ciclo=$anio."-".($anio+1);
}else{
$ciclo=($anio-1)."-".$anio;
}

}


/* NIVELES */

$niveles = [
"CAM",
"CEBAS",
"FORMACION PARA EL TRABAJO",
"USAER",
"INICIAL",
"PREESCOLAR",
"PRIMARIA",
"SECUNDARIA",
"MEDIA SUPERIOR",
"SUPERIOR"
];


/* SUBNIVELES */

$subniveles = [

"CAM"=>["CAM"],
"CEBAS"=>["CEBAS"],
"FORMACION PARA EL TRABAJO"=>["FORMACION PARA EL TRABAJO"],
"USAER"=>["USAER"],

"INICIAL"=>[
"GENERAL",
"INDIGENA",
"NO ESCOLARIZADA",
"COMUNITARIA",
"PRIMERA INFANCIA"
],

"PREESCOLAR"=>[
"GENERAL",
"INDIGENA",
"NO ESCOLARIZADA"
],

"PRIMARIA"=>[
"GENERAL",
"INDIGENA",
"COMUNITARIA"
],

"SECUNDARIA"=>[
"GENERAL",
"COMUNITARIA"
],

"MEDIA SUPERIOR"=>[
"GENERAL",
"TECNOLOGICO",
"ESCUELAS"
],

"SUPERIOR"=>[
"LICENTIATURA",
"POSGRADO",
"INSTITUCION"
]

];

?>

<!DOCTYPE html>
<html>

<head>

<title>Libretas estadísticas</title>

<link rel="stylesheet" href="assets/css/estilos.css">

<style>

body{
background:#eef2f6;
font-family:system-ui;
}


/* TITULO */

.titulo{

max-width:1200px;
margin:30px auto 10px;
font-size:28px;
font-weight:600;

}


/* FILTROS */

.filtros{

max-width:1200px;
margin:auto;
margin-bottom:20px;

display:flex;
gap:15px;
align-items:center;

}

.filtros input,
.filtros select{

padding:9px;
border-radius:6px;
border:1px solid #ccc;
font-size:14px;

}


/* GRID */

.grid-libretas{

max-width:1200px;
margin:auto;

display:grid;
grid-template-columns:repeat(5,1fr);
gap:12px;

}


/* TARJETA */

.libreta{

background:white;

padding:14px;

border-radius:7px;

border:1px solid #e3e3e3;

cursor:pointer;

transition:all .2s;

height:80px;

display:flex;
flex-direction:column;
justify-content:center;

}


/* HOVER */

.libreta:hover{

transform:translateY(-3px);
box-shadow:0 5px 15px rgba(0,0,0,.08);

}


/* TITULO */

.libreta h3{

margin:0;
font-size:14px;
color:#6f1024;
font-weight:600;

}


/* TEXTO */

.libreta p{

margin:3px 0 0 0;
font-size:12px;
color:#555;

}

</style>

</head>

<body>


<?php include "includes/header.php"; ?>


<div class="titulo">

Libretas estadísticas

</div>


<div class="filtros">

<input type="text" id="buscar" placeholder="Buscar libreta...">


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


<div class="grid-libretas" id="grid">

<?php

foreach($niveles as $nivel){

foreach($subniveles[$nivel] as $sub){

/* SI NIVEL = SUBNIVEL -> SOLO NIVEL */

if($nivel==$sub){

$formato=$nivel;

}else{

$formato=$nivel." ".$sub;

}

echo "

<div class='libreta' data-nombre='$formato'
onclick=\"window.location='libreta.php?formato=".urlencode($formato)."&ciclo=$ciclo'\" >

<h3>$formato</h3>

<p>Consultar libreta</p>

</div>

";

}

}

?>

</div>



<script>

/* BUSCADOR */

document.getElementById("buscar").addEventListener("keyup",function(){

let texto=this.value.toLowerCase();

let tarjetas=document.querySelectorAll(".libreta");

tarjetas.forEach(function(t){

let nombre=t.dataset.nombre.toLowerCase();

if(nombre.includes(texto)){
t.style.display="flex";
}else{
t.style.display="none";
}

});

});


/* CAMBIAR CICLO */

function cambiarCiclo(c){

window.location="consultar_libretas.php?ciclo="+c;

}

</script>


</body>

</html>	
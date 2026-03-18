<?php
include "includes/auth.php";
?>

<!DOCTYPE html>
<html>
<head>

<title>Concentrados</title>

<link rel="stylesheet" href="assets/css/estilos.css">

<style>

body{
background:#eef2f6;
font-family:system-ui;
}


/* BANNER */

.banner-reportes{

background:#6f1024;
color:white;
padding:18px 30px;
font-size:20px;
font-weight:600;

}


/* BUSCADOR */

.buscador{

text-align:center;
margin:30px 0;

}

.buscador input{

width:320px;
padding:10px;
border-radius:6px;
border:1px solid #ccc;
font-size:14px;

}


/* CONTENEDOR */

.contenedor-reportes{

max-width:900px;
margin:auto;

}


/* ITEM MENU */

.item-menu{

background:#d9d9d9;

padding:14px 18px;

margin-bottom:10px;

cursor:pointer;

display:flex;
justify-content:space-between;
align-items:center;

font-weight:500;

border-radius:4px;

transition:all .2s;

}


/* HOVER */

.item-menu:hover{

background:#cfcfcf;
transform:translateY(-2px);

}


/* ICONO */

.icono{

font-size:18px;
font-weight:600;

}


/* CONTENIDO */

.contenido-menu{

max-height:0;
overflow:hidden;

transition:max-height .35s ease;

}


/* CAJA INTERNA */

.contenido-box{

background:white;
border:1px solid #ddd;
border-radius:6px;

padding:12px;

margin-bottom:15px;

}


/* LINK REPORTE */

.reporte{

padding:12px;
border:1px solid #e2e2e2;
border-radius:6px;
margin-bottom:10px;

transition:all .15s;

}

.reporte:hover{

background:#f6f6f6;
transform:translateY(-2px);

}


.reporte a{

text-decoration:none;
color:#6f1024;
font-weight:600;
font-size:15px;

}

.reporte p{

margin:2px 0 0 0;
font-size:13px;
color:#555;

}


</style>

</head>


<body>


<?php include "includes/header.php"; ?>


<div class="banner-reportes">

Menú de reportes estadísticos - Planeación SEDUC

</div>


<div class="buscador">

<input type="text" placeholder="Buscar reporte...">

</div>


<div class="contenedor-reportes">


<!-- BASES -->

<div class="item-menu" onclick="toggleMenu(this,'bases')">

Bases de datos

<span class="icono">+</span>

</div>

<div class="contenido-menu" id="bases">

<div class="contenido-box">

<div class="reporte">

<a href="#">F911 por niveles</a>

<p>Base de datos del formato 911 de todos los niveles</p>

</div>


<div class="reporte">

<a href="#">SIC (Sistema de Identificación de Centros de Trabajo)</a>

<p>Base de datos del catálogo de centros de trabajo</p>

</div>

</div>

</div>



<!-- LIBRETAS -->

<div class="item-menu" onclick="toggleMenu(this,'libretas')">

Libretas estadísticas

<span class="icono">+</span>

</div>

<div class="contenido-menu" id="libretas">

<div class="contenido-box">

<div class="reporte">

<a href="consultar_libretas.php">Consultar libretas</a>

<p>Acceso a las libretas estadísticas por formato</p>

</div>

</div>

</div>



<!-- CONCENTRADO -->

<div class="item-menu" onclick="toggleMenu(this,'concentrado')">

Concentrado estadístico

<span class="icono">+</span>

</div>

<div class="contenido-menu" id="concentrado">

<div class="contenido-box">

<div class="reporte">

<a href="#">Consulta concentrada</a>

<p>Consulta estadística consolidada de los formatos</p>

</div>

</div>

</div>



</div>



<script>

function toggleMenu(el,id){

let menu=document.getElementById(id);

let icon=el.querySelector(".icono");

if(menu.style.maxHeight){

menu.style.maxHeight=null;
icon.innerHTML="+";

}else{

menu.style.maxHeight=menu.scrollHeight+"px";
icon.innerHTML="−";

}

}

</script>


</body>
</html>
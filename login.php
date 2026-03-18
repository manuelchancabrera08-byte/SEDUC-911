<?php
session_start();
include("includes/conexion.php");

$error = "";

if($_SERVER["REQUEST_METHOD"]=="POST"){

$usuario = $_POST['usuario'];
$password = md5($_POST['password']);

$sql = "SELECT * FROM usuarios 
        WHERE usuario='$usuario' 
        AND password='$password'";

$resultado = $conexion->query($sql);

if($resultado->num_rows == 1){

    $row = $resultado->fetch_assoc();

    $_SESSION['usuario_id'] = $row['id'];
    $_SESSION['nombre'] = $row['nombre'];

    header("Location: inicio.php");
    exit;

}else{

    $error = "Usuario o contraseña incorrectos";

}

}

/* ciclo escolar vigente */

$anio = date("Y");
$mes = date("n");

if($mes >= 8){
$inicio = $anio;
}else{
$inicio = $anio - 1;
}

$fin = $inicio + 1;

$ciclo_actual = $inicio . " - " . $fin;
?>

<!DOCTYPE html>
<html>
<head>

<meta charset="UTF-8">
<title>Sistema Estadístico 911</title>

<style>

body{

margin:0;
font-family: "Segoe UI", Arial, sans-serif;

background:linear-gradient(
135deg,
#4b0d26 0%,
#611232 40%,
#7a163e 100%
);

height:100vh;
display:flex;
flex-direction:column;
justify-content:center;
align-items:center;

}

/* TITULO SUPERIOR */

.header{

color:white;
text-align:center;
margin-bottom:40px;

}

.header h1{

margin:0;
font-size:42px;
font-weight:700;
letter-spacing:1px;

}

.header p{

margin-top:10px;
font-size:18px;
opacity:0.95;

}

/* CARD LOGIN */

.card{

background:white;
padding:45px;
border-radius:14px;
box-shadow:0 30px 60px rgba(0,0,0,0.35);
width:380px;

}

.card h2{

text-align:center;
color:#611232;
margin-bottom:30px;
font-size:22px;

}

/* INPUTS */

input{

width:100%;
padding:13px;
margin-top:10px;
margin-bottom:20px;
border:1px solid #ddd;
border-radius:6px;
font-size:15px;
transition:all .2s;

}

input:focus{

outline:none;
border-color:#611232;
box-shadow:0 0 0 2px rgba(97,18,50,0.15);

}

/* BOTON */

button{

width:100%;
padding:13px;
background:#611232;
color:white;
border:none;
border-radius:6px;
font-size:16px;
font-weight:600;
cursor:pointer;
transition:all .2s;

}

button:hover{

background:#4b0d26;
transform:scale(1.02);

}

.error{

color:red;
text-align:center;
margin-bottom:15px;

}

.footer{

margin-top:30px;
font-size:13px;
color:white;
opacity:0.85;

}

</style>

</head>

<body>

<div class="header">

<h1>Sistema Estadístico 911</h1>

<p>Secretaría de Educación • Ciclo Escolar <?php echo $ciclo_actual; ?></p>

</div>

<div class="card">

<h2>Acceso al sistema</h2>

<?php if($error!=""){ ?>

<div class="error">
<?php echo $error ?>
</div>

<?php } ?>

<form method="POST">

<input type="text" name="usuario" placeholder="Usuario" required>

<input type="password" name="password" placeholder="Contraseña" required>

<button type="submit">Ingresar</button>

</form>

</div>

<div class="footer">

Gobierno del Estado • Sistema Institucional

</div>

</body>
</html>
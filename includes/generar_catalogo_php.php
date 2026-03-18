<?php

include "includes/conexion.php";

$sql = "SELECT formato,nivel,subnivel FROM formatos WHERE activo=1 ORDER BY nivel,subnivel";
$res = $conexion->query($sql);

$catalogo = [];

while($row = $res->fetch_assoc()){

    $clave = $row['formato'];

    $nombre = strtoupper($row['nivel']);

    if($row['subnivel']!=""){
        $nombre .= " " . strtoupper($row['subnivel']);
    }

    $catalogo[$clave] = $nombre;

}

$contenido = "<?php\n\n\$catalogoFormatos=[\n\n";

foreach($catalogo as $k=>$v){
    $contenido .= "\"$k\"=>\"$v\",\n";
}

$contenido .= "\n];\n";

file_put_contents("includes/catalogo_formatos.php",$contenido);

echo "Catalogo actualizado";

?>
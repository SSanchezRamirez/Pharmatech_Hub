<?php
session_start();

$host = "localhost";
$db_username = "uabcsnet_adminParmatech";
$db_password = "CTln0KN41HJ3";
$dbname = "uabcsnet_ritienet_parmatech";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $db_username, $db_password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("No se pudo conectar a la base de datos: " . $e->getMessage());
}
$error = '';
?>



<?php
/*
//funciona ruta/a/tu/script/de/actualizacion.php
if(isset($_POST['aprobacion']) && is_array($_POST['aprobacion'])){
    foreach($_POST['aprobacion'] as $id_visita){
        $consulta = "UPDATE h_registro_visitas SET aprobacion = 2 WHERE id_visita = ?";
        $sentencia = $pdo->prepare($consulta);
        $sentencia->execute([$id_visita]);
    }
}*/


// Verificar si se envió el formulario y si la acción es 'actualizar'

if(isset($_POST['accion']) && $_POST['accion'] == 'actualizar'){
        // Comprobar si se recibió un array de aprobaciones y procesarlo

    if(isset($_POST['aprobacion']) && is_array($_POST['aprobacion'])){
                // Recorrer cada ID de visita que necesita ser aprobada

        foreach($_POST['aprobacion'] as $id_visita){
            $consulta = "UPDATE h_registro_visitas SET aprobacion = 2 WHERE id_visita = ?";
            $sentencia = $pdo->prepare($consulta);
            $sentencia->execute([$id_visita]);
        }
    }

    // Comprobar si se recibió un array de rechazos y procesarlo
    if(isset($_POST['rechazo']) && is_array($_POST['rechazo'])){
        // Recorrer cada ID de visita que necesita ser rechazada
        foreach($_POST['rechazo'] as $id_visita){
            // Preparar la consulta SQL para actualizar el estado de aprobación a rechazado
            $consulta = "UPDATE h_registro_visitas SET aprobacion = 11 WHERE id_visita = ?";
            $sentencia = $pdo->prepare($consulta);
            // Ejecutar la consulta pasando el ID de la visita como parámetro
            $sentencia->execute([$id_visita]);
            
        }
    }
}
?>
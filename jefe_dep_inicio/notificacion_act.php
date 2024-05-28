<?php
// Inicia una sesión PHP para manejar variables de sesión
session_start();
// Configuración de la conexión a la base de datos MySQL
$host = "localhost";
$db_username = "uabcsnet_adminParmatech";
$db_password = "CTln0KN41HJ3";
$dbname = "uabcsnet_ritienet_parmatech";

try {
      // Intenta establecer una conexión a la base de datos utilizando PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $db_username, $db_password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Establece el modo de error para que PDO lance excepciones en caso de error
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC // Establece el modo de obtención de resultados predeterminado a asociativo
    ]);
} catch (PDOException $e) {
     // Si hay un error durante la conexión, muestra un mensaje de error y detiene la ejecución del script
    die("No se pudo conectar a la base de datos: " . $e->getMessage());
}
// Declarar una variable $error para almacenar posibles mensajes de error (no se utiliza en este fragmento de código)
$error = '';
?>



<?php
// Verifica si se enviaron datos mediante el método POST y si existe un elemento con el nombre "aprobacion" que es un array
if(isset($_POST['aprobacion']) && is_array($_POST['aprobacion'])){
       // Recorre el array "aprobacion" y ejecuta una consulta SQL para actualizar el campo "aprobacion" a 3 en la tabla "h_registro_visitas" para cada ID de visita proporcionado
    foreach($_POST['aprobacion'] as $id_visita){
        $consulta = "UPDATE h_registro_visitas SET aprobacion = 3 WHERE id_visita = ?";
        $sentencia = $pdo->prepare($consulta);
        $sentencia->execute([$id_visita]);
    }
}
// Verifica si se enviaron datos mediante el método POST y si existe un elemento con el nombre "rechazo" que es un array
if(isset($_POST['rechazo']) && is_array($_POST['rechazo'])){
     // Recorre el array "rechazo" y ejecuta una consulta SQL para actualizar el campo "aprobacion" a 12 en la tabla "h_registro_visitas" para cada ID de visita proporcionado
    foreach($_POST['rechazo'] as $id_visita){
        $consulta = "UPDATE h_registro_visitas SET aprobacion = 12 WHERE id_visita = ?";
        $sentencia = $pdo->prepare($consulta);
        $sentencia->execute([$id_visita]);
    }
}
?>
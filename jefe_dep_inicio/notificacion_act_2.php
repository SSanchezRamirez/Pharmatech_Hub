<?php
// Iniciar una sesión PHP para poder usar variables de sesión
session_start();
// Definir los parámetros de conexión a la base de datos MySQL
$host = "localhost";
$db_username = "uabcsnet_adminParmatech";
$db_password = "CTln0KN41HJ3";
$dbname = "uabcsnet_ritienet_parmatech";

try {
        // Intentar establecer una conexión a la base de datos utilizando PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $db_username, $db_password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Establecer el modo de error para que PDO lance excepciones en caso de error
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC // Establecer el modo de obtención de resultados predeterminado a asociativo
    ]);
} catch (PDOException $e) {
        // Si ocurre un error durante la conexión, mostrar un mensaje de error y terminar la ejecución del script
    die("No se pudo conectar a la base de datos: " . $e->getMessage());
}
// Declarar una variable para posibles mensajes de error (no se utiliza en este fragmento de código)
$error = '';
?>


<?php
// Verificar si se enviaron datos mediante el método POST y si existe un elemento con el nombre "aprobacion" que es un array
if(isset($_POST['aprobacion']) && is_array($_POST['aprobacion'])){
     // Recorrer el array "aprobacion" y actualizar el campo "aprobacion" a 5 en la tabla "h_registro_visitas" para cada ID de visita proporcionado
    foreach($_POST['aprobacion'] as $id_visita){
        $consulta = "UPDATE h_registro_visitas SET aprobacion = 5 WHERE id_visita = ?";
        $sentencia = $pdo->prepare($consulta);
        $sentencia->execute([$id_visita]);
    }
}// Verificar si se enviaron datos mediante el método POST y si existe un elemento con el nombre "rechazo" que es un array
if(isset($_POST['rechazo']) && is_array($_POST['rechazo'])){
    // Recorrer el array "rechazo" y actualizar el campo "aprobacion" a 14 en la tabla "h_registro_visitas" para cada ID de visita proporcionado
    foreach($_POST['rechazo'] as $id_visita){
        $consulta = "UPDATE h_registro_visitas SET aprobacion = 14 WHERE id_visita = ?";
        $sentencia = $pdo->prepare($consulta);
        $sentencia->execute([$id_visita]);
    }
}
?>
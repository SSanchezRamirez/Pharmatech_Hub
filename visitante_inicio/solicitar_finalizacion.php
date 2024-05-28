<?php
// Inicia la sesión PHP para poder utilizar variables de sesión
session_start();

// Parámetros de conexión a la base de datos
$host = "localhost";
$db_username = "uabcsnet_adminParmatech";
$db_password = "CTln0KN41HJ3";
$dbname = "uabcsnet_ritienet_parmatech";

try {
    // Intenta establecer la conexión con la base de datos utilizando PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $db_username, $db_password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Configura PDO para lanzar excepciones en errores
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC // Establece el modo de recuperación de datos predeterminado a asociativo
    ]);
} catch (PDOException $e) {
    // En caso de error al conectar, muestra un mensaje de error y termina la ejecución del script
    die("No se pudo conectar a la base de datos: " . $e->getMessage());
}

// Comprueba si la solicitud es de tipo POST y si se ha enviado el parámetro 'id_visita'
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_visita'])) {
    // Obtiene el valor del parámetro 'id_visita' enviado mediante POST
    $id_visita = $_POST['id_visita'];
    
    // Prepara la consulta SQL para actualizar el campo 'aprobacion' en la tabla 'h_registro_visitas'
    $consulta = "UPDATE h_registro_visitas SET aprobacion = 4 WHERE id_visita = ?";
    
    // Prepara la sentencia SQL para ejecutar la consulta
    $sentencia = $pdo->prepare($consulta);
    
    // Ejecuta la sentencia SQL con el valor de 'id_visita' como parámetro
    $sentencia->execute([$id_visita]);
    
    // Redirige después de la operación para evitar reenvíos del formulario
    header('Location: http://uabcs.net/pharmatechub/visitante_inicio/index.php');
    
    // Termina la ejecución del script para evitar que se procese más contenido después de la redirección
    exit();
}
?>

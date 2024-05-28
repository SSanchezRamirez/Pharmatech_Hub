<?php
// Inicia la sesión para poder utilizar variables de sesión
session_start();

// Define las credenciales de la base de datos
$host = "localhost";
$db_username = "uabcsnet_adminParmatech";
$db_password = "CTln0KN41HJ3";
$dbname = "uabcsnet_ritienet_parmatech";

try {
    // Intenta establecer una conexión con la base de datos utilizando PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $db_username, $db_password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Establece el modo de error para que lance excepciones en caso de error
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC // Establece el modo de recuperación predeterminado para que devuelva un array asociativo
    ]);
} catch (PDOException $e) {
    // Si hay un error al conectar a la base de datos, muestra un mensaje de error y termina el script
    die("No se pudo conectar a la base de datos: " . $e->getMessage());
}

// Verifica si se ha enviado una solicitud POST y si se ha proporcionado un ID de visita
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_visita'])) {
    // Obtiene el ID de visita de la solicitud POST
    $id_visita = $_POST['id_visita'];
    
    // Obtiene el estado de aprobación de la visita de la solicitud POST
    $estado_aprobacion = $_POST['estado_aprobacion'];

    // Actualiza el estado de aprobación de la visita en la base de datos dependiendo del estado actual
    if ($estado_aprobacion < 6) {
        // Si el estado de aprobación es menor que 6, lo actualiza a 6
        $consulta = "UPDATE h_registro_visitas SET aprobacion = 6 WHERE id_visita = ?";
        $sentencia = $pdo->prepare($consulta);
        $sentencia->execute([$id_visita]);
    } 
    if ($estado_aprobacion > 6) {
        // Si el estado de aprobación es mayor que 6, lo actualiza a 10
        $consulta = "UPDATE h_registro_visitas SET aprobacion = 10 WHERE id_visita = ?";
        $sentencia = $pdo->prepare($consulta);
        $sentencia->execute([$id_visita]);
    } 

    // Redirige después de la operación para evitar reenvíos del formulario
    header('Location: http://uabcs.net/pharmatechub/visitante_inicio/index.php');
    exit(); // Termina la ejecución del script
}
?>

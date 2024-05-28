<?php
// Inicia la sesión PHP para permitir el acceso a las variables de sesión si es necesario.
session_start();

// Define las credenciales de la base de datos, incluyendo el nombre de host, nombre de usuario, contraseña y nombre de la base de datos.
$host = "localhost";
$db_username = "uabcsnet_adminParmatech";
$db_password = "CTln0KN41HJ3";
$dbname = "uabcsnet_ritienet_parmatech";

try {
    // Intenta conectar con la base de datos mediante PDO (PHP Data Objects).
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $db_username, $db_password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION // Configura PDO para lanzar excepciones en caso de error.
    ]);
} catch (PDOException $e) {
    // Si hay un error en la conexión, muestra un mensaje de error y termina la ejecución del script.
    die("No se pudo conectar a la base de datos: " . $e->getMessage());
}

// Verifica si se ha enviado un ID de visita a través del método POST.
if (isset($_POST['id_visita'])) {
    // Obtiene el ID de la visita del formulario.
    $id_visita = $_POST['id_visita'];
    
    // Prepara una consulta SQL para eliminar la visita correspondiente de la tabla 'h_registro_visitas'.
    $consulta = "DELETE FROM h_registro_visitas WHERE id_visita = ?";
    $stmt = $pdo->prepare($consulta);

    // Ejecuta la consulta preparada, pasando el ID de la visita como parámetro.
    if ($stmt->execute([$id_visita])) {
        // Si la consulta se ejecuta correctamente, muestra una alerta al usuario indicando que la visita se eliminó correctamente.
        // Luego redirige al usuario de nuevo a la página de inicio de visitantes.
        echo "<script type='text/javascript'>
            alert('Visita eliminada correctamente.');
            window.location.href = 'http://uabcs.net/pharmatechub/visitante_inicio/index.php';
          </script>";
    } else {
        // Si ocurre algún error durante la ejecución de la consulta, muestra un mensaje de error.
        echo "Error al eliminar la visita.";
    }
} else {
    // Si no se proporciona un ID de visita, muestra un mensaje indicando que no se proporcionó el ID de la visita.
    echo 'No se proporcionó el ID de la visita.';
}
?>

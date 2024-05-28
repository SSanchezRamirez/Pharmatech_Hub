<?php
// Iniciar sesión
session_start();

// Datos de conexión a la base de datos
$host = "localhost";
$db_username = "uabcsnet_adminParmatech";
$db_password = "CTln0KN41HJ3";
$dbname = "uabcsnet_ritienet_parmatech";

try {
    // Crear una nueva instancia de PDO para la conexión a la base de datos
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $db_username, $db_password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    // Si hay un error de conexión, mostrar el mensaje y detener la ejecución
    die("No se pudo conectar a la base de datos: " . $e->getMessage());
}

// Verificar si se ha proporcionado un ID de doctor a través de GET
if (isset($_GET['id'])) {
    $id_doc = $_GET['id'];

    // Consulta para obtener el nombre de usuario del doctor a eliminar
    $consultaUsername = "SELECT nombre_usuario FROM info_drs WHERE id_doc = ?";
    $stmtUsername = $pdo->prepare($consultaUsername);
    $stmtUsername->execute([$id_doc]);
    $usuario = $stmtUsername->fetchColumn();

    // Si se encuentra el nombre de usuario, proceder con la eliminación del doctor y el usuario
    if ($usuario) {
        // Iniciar una transacción para asegurar que ambas eliminaciones se realicen correctamente
        $pdo->beginTransaction();

        // Consulta para eliminar al doctor de la tabla info_drs
        $consultaEliminarDoctor = "DELETE FROM info_drs WHERE id_doc = ?";
        $stmtEliminarDoctor = $pdo->prepare($consultaEliminarDoctor);
        $eliminadoDoctor = $stmtEliminarDoctor->execute([$id_doc]);

        // Consulta para eliminar al usuario de la tabla users
        $consultaEliminarUsuario = "DELETE FROM users WHERE username = ?";
        $stmtEliminarUsuario = $pdo->prepare($consultaEliminarUsuario);
        $eliminadoUsuario = $stmtEliminarUsuario->execute([$usuario]);

        // Si ambas eliminaciones son exitosas, confirmar la transacción
        if ($eliminadoDoctor && $eliminadoUsuario) {
            $pdo->commit();
            // Redirigir a la página de gestión de personal
            header("Location: http://uabcs.net/pharmatechub/subdirector_inicio/ges_personal.php");
        } else {
            // Si algo falla, revertir la transacción
            $pdo->rollBack();
            // Redirigir a la página de error
            header("Location: index.php?mensaje=error");
            exit;
        }
    } else {
        // Si no se encuentra el usuario, redirigir a la página de error
        header("Location: index.php?mensaje=usuario_no_encontrado");
        exit;
    }
} else {
    // Si no se proporciona un ID de doctor, mostrar un mensaje de error
    die('No se proporcionó el ID del documento.');
}
?>

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

// Verificar si se ha proporcionado un ID de departamento a través de GET
if (isset($_GET['id'])) {
    $id_departamento = $_GET['id'];

    // Consulta para obtener el nombre del departamento a eliminar
    $consultaNombre = "SELECT nombre FROM departamentos WHERE id_departamento = ?";
    $stmtNombre = $pdo->prepare($consultaNombre);
    $stmtNombre->execute([$id_departamento]);
    $nombre_departamento = $stmtNombre->fetchColumn();

    // Si se encuentra el nombre del departamento, proceder con la eliminación
    if ($nombre_departamento) {
        // Iniciar una transacción para asegurar que todas las operaciones se realicen correctamente
        $pdo->beginTransaction();

        // Consulta para eliminar el departamento de la tabla departamentos
        $consultaEliminarDepartamento = "DELETE FROM departamentos WHERE id_departamento = ?";
        $stmtEliminarDepartamento = $pdo->prepare($consultaEliminarDepartamento);
        $eliminadoDepartamento = $stmtEliminarDepartamento->execute([$id_departamento]);

        // Si la eliminación es exitosa, confirmar la transacción
        if ($eliminadoDepartamento) {
            $pdo->commit();
            // Redirigir a la página de gestión de departamentos
            header("Location: http://uabcs.net/pharmatechub/subdirector_inicio/ges_dep.php");
        } else {
            // Si algo falla, revertir la transacción
            $pdo->rollBack();
            // Redirigir a la página de error
            header("Location: index.php?mensaje=error");
            exit;
        }
    } else {
        // Si no se encuentra el departamento, redirigir a la página de error
        header("Location: index.php?mensaje=departamento_no_encontrado");
        exit;
    }
} else {
    // Si no se proporciona un ID de departamento, mostrar un mensaje de error
    die('No se proporcionó el ID del departamento.');
}
?>

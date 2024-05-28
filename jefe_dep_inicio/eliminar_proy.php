<?php
session_start();// Inicia la sesión para mantener la información del usuario entre páginas

$host = "localhost";
$db_username = "uabcsnet_adminParmatech";
$db_password = "CTln0KN41HJ3";
$dbname = "uabcsnet_ritienet_parmatech";

try {
     // Intenta conectar a la base de datos utilizando PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $db_username, $db_password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
     // Intenta conectar a la base de datos utilizando PDO
    die("No se pudo conectar a la base de datos: " . $e->getMessage());
}
// Verifica si se proporcionó un ID de proyecto a través de la URL
if (isset($_GET['id'])) {
    $id_proyecto = $_GET['id'];
     // Consulta para eliminar el proyecto con el ID proporcionado
    $consulta = "DELETE FROM proyectos WHERE id_proyecto = ?";
    $stmt = $pdo->prepare($consulta);
 // Ejecuta la consulta preparada, pasando el ID del proyecto como parámetro
    if ($stmt->execute([$id_proyecto])) {
         // Si la eliminación fue exitosa, redirige a la página de generación de proyectos
        header("Location: http://uabcs.net/pharmatechub/jefe_dep_inicio/gen_proyectos.php");

    } else {
        header("Location: index.php?mensaje=error");
        exit;
    }
} else {
    die('No se proporcionó el ID del proyecto.');
}
?>

<head>
    <!-- ... tus otros enlaces y scripts ... -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>


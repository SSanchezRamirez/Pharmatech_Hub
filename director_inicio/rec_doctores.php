<?php
    session_start();

    // Configuración de conexión a la base de datos
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

    // Asumiendo que ya tienes una sesión iniciada y una variable de usuario
    $var_usuario = $_SESSION['username'];
    
    
    if (isset($_GET['departamento_id'])) {
    $departamentoId = $_GET['departamento_id'];

    // Preparar la consulta SQL para obtener los doctores del departamento seleccionado
    $stmt = $pdo->prepare("SELECT id_doc, CONCAT(nombre_s, ' ', apellido_paterno, ' ', apellido_materno) AS nombre_completo FROM info_drs WHERE dep = :departamento_id");
    $stmt->bindParam(':departamento_id', $departamentoId, PDO::PARAM_INT);
    $stmt->execute();

    // Fetch all rows as an associative array
    $doctores = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Devolver los resultados como JSON
    echo json_encode($doctores);
}
?>

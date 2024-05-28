<?php
    session_start(); // Inicia una sesión de PHP

    // Configuración de conexión a la base de datos
    $host = "localhost";
    $db_username = "uabcsnet_adminParmatech";
    $db_password = "CTln0KN41HJ3";
    $dbname = "uabcsnet_ritienet_parmatech";


   // Intenta establecer una conexión a la base de datos usando PDO
   try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $db_username, $db_password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Establece el modo de error a excepciones
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC // Establece el modo de obtención de datos a asociativo
    ]);
} catch (PDOException $e) {
    // Si la conexión falla, se muestra un mensaje de error y se detiene la ejecución del script
    die("No se pudo conectar a la base de datos: " . $e->getMessage());
}

   // Asumiendo que ya tienes una sesión iniciada y una variable de usuario
   $var_usuario = $_SESSION['username']; // Obtiene el nombre de usuario de la sesión
    
    // Verifica si el parámetro 'departamento_id' está presente en la URL
    if (isset($_GET['departamento_id'])) {
        $departamentoId = $_GET['departamento_id']; // Obtiene el ID del departamento de la URL
    
        // Preparar la consulta SQL para obtener los proyectos del departamento seleccionado
        $stmt = $pdo->prepare("SELECT id_proyecto, nombre FROM proyectos WHERE id_departamento = :departamento_id");
        $stmt->bindParam(':departamento_id', $departamentoId, PDO::PARAM_INT);
        $stmt->execute();
    
       // Obtener todas las filas como un array asociativo
        $proyectos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        // Devolver los resultados como JSON
        echo json_encode($proyectos);
    }
    ?>
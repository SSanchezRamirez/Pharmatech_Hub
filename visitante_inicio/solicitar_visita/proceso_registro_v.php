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

// Verifica si la solicitud es de tipo POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verifica si la sesión contiene un nombre de usuario
    if (!isset($_SESSION['username'])) {
        die("Acceso no autorizado."); // Termina el script si no hay nombre de usuario en la sesión
    }

    $nombre_usuario = $_SESSION['username']; // Obtiene el nombre de usuario de la sesión
    $f_inicio = $_POST['f_inicio']; // Fecha de inicio de la visita
    $f_fin = $_POST['f_fin']; // Fecha de fin de la visita
    $m_visita = $_POST['m_visita']; // Motivo de la visita
    $id_departamento = $_POST['id_departamento']; // ID del departamento
    $id_proyecto = $_POST['id_proyecto']; // ID del proyecto
    $id_doc = $_POST['id_doc']; // ID del doctor
    $aprobacion = 1; // Aprobación por defecto

    // Validación básica de entradas
    if (empty($f_inicio) || empty($f_fin) || empty($m_visita) || empty($id_departamento) || empty($id_proyecto) || empty($id_doc)) {
        die("Por favor, complete todos los campos requeridos."); // Termina el script si algún campo está vacío
    }

    // Obtener el nombre del motivo de visita
    $stmt_motivo = $pdo->prepare("SELECT nombre_motivo FROM motivos_visita WHERE id_motivo = :m_visita");
    $stmt_motivo->bindParam(':m_visita', $m_visita);
    $stmt_motivo->execute();
    $motivo = $stmt_motivo->fetchColumn(); // Obtiene el nombre del motivo de la base de datos

    if ($motivo === false) {
        die("Motivo de visita no encontrado."); // Termina el script si no se encuentra el motivo
    }

    try {
        // Inicia una transacción
        $pdo->beginTransaction();
        
        // Prepara la consulta SQL para insertar los datos de la visita
        $stmt = $pdo->prepare("INSERT INTO h_registro_visitas (nombre_usuario, f_inicio, f_fin, m_visita, id_departamento, id_proyecto, id_doc, aprobacion, motivo_v, imagen, ruta_imagen) 
            VALUES (:nombre_usuario, :f_inicio, :f_fin, :m_visita, :id_departamento, :id_proyecto, :id_doc, :aprobacion, :motivo_v, :imagen, :ruta_imagen)");

        // Vincula los parámetros de la consulta con las variables correspondientes
        $stmt->bindParam(':nombre_usuario', $nombre_usuario);
        $stmt->bindParam(':f_inicio', $f_inicio);
        $stmt->bindParam(':f_fin', $f_fin);
        $stmt->bindParam(':m_visita', $m_visita);
        $stmt->bindParam(':id_departamento', $id_departamento);
        $stmt->bindParam(':id_proyecto', $id_proyecto);
        $stmt->bindParam(':id_doc', $id_doc);
        $stmt->bindParam(':aprobacion', $aprobacion);
        $stmt->bindParam(':motivo_v', $motivo);

        // Verifica si se ha subido una imagen
        if (!empty($_FILES['imagen']['name'])) {
            $imagen_nombre = basename($_FILES['imagen']['name']); // Obtiene el nombre del archivo de imagen
            $imagen_temp = $_FILES['imagen']['tmp_name']; // Ruta temporal del archivo subido
            $ruta_destino = __DIR__ . "/../../recursos/assets/img_perfiles/" . $imagen_nombre; // Define la ruta de destino

            // Mueve el archivo subido a la ruta de destino
            if (move_uploaded_file($imagen_temp, $ruta_destino)) {
                $ruta_imagen = "http://uabcs.net/pharmatechub/recursos/assets/img_perfiles/" . $imagen_nombre; // URL de la imagen
                $stmt->bindParam(':imagen', $imagen_nombre);
                $stmt->bindParam(':ruta_imagen', $ruta_imagen);
            } else {
                // Lanza una excepción si hay un error al mover el archivo
                throw new Exception("Error al subir la imagen: " . $_FILES['imagen']['error']);
            }
        } else {
            // Si no se ha subido una imagen, establece los valores correspondientes a null
            $imagen_nombre = null;
            $ruta_imagen = null;
            $stmt->bindParam(':imagen', $imagen_nombre);
            $stmt->bindParam(':ruta_imagen', $ruta_imagen);
        }

        // Ejecuta la consulta
        if ($stmt->execute()) {
            // Si la consulta se ejecuta correctamente, confirma la transacción
            $pdo->commit();
            echo "<script type='text/javascript'>
            alert('Visita registrada exitosamente.');
            window.location.href = 'http://uabcs.net/pharmatechub/visitante_inicio/index.php';
            </script>"; // Muestra un mensaje de éxito y redirige al usuario
        } else {
            // Si la consulta falla, revierte la transacción
            $pdo->rollBack();
            die("Error al registrar la visita.");
        }
    } catch (Exception $e) {
        // En caso de excepción, revierte la transacción y muestra el mensaje de error
        $pdo->rollBack();
        die("Error al registrar la visita: " . $e->getMessage());
    }
}
?>

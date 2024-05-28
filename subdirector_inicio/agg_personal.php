<?php
//-------------------------------------------------
session_start(); // Iniciar la sesión

// Configuración de conexión a la base de datos
$host = "localhost";
$db_username = "uabcsnet_adminParmatech";
$db_password = "CTln0KN41HJ3";
$dbname = "uabcsnet_ritienet_parmatech";

try {
    // Crear una nueva conexión PDO a la base de datos con configuración de opciones
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $db_username, $db_password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Configurar el modo de error a excepción
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC // Configurar el modo de obtención de datos a asociativo
    ]);
} catch (PDOException $e) {
    // Si hay un error en la conexión, finalizar la ejecución y mostrar el mensaje de error
    die("No se pudo conectar a la base de datos: " . $e->getMessage());
}

// Recuperar los datos del formulario
$nombre_usuario = $_POST['nombre_usuario'] ?? ''; // Nombre de usuario ingresado
$password = $_POST['password'] ?? ''; // Contraseña ingresada
$nombre_s = $_POST['nombre_s'] ?? ''; // Nombre
$apellido_paterno = $_POST['apellido_paterno'] ?? ''; // Apellido paterno
$apellido_materno = $_POST['apellido_materno'] ?? ''; // Apellido materno
$correo = $_POST['correo'] ?? ''; // Correo electrónico
$firma = $_POST['firma'] ?? ''; // Firma (nombre del archivo de firma)
$nivel = 1; // Nivel del usuario (asignado como 1)

// Verificar si el nombre de usuario ya existe
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
$stmt->bindParam(':username', $nombre_usuario); // Enlazar el nombre de usuario
$stmt->execute();
if ($stmt->rowCount() > 0) {
    // El nombre de usuario ya existe, mostrar una alerta y redirigir al usuario
    echo "<script>alert('El nombre de usuario ya existe. Por favor, elige otro.'); window.location.href = 'tu_pagina_de_error.php';</script>";
    exit; // Salir del script
}

// Hashing de la contraseña
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

try {
    // Iniciar la transacción
    $pdo->beginTransaction();

    // Insertar en la tabla users
    $stmt = $pdo->prepare("INSERT INTO users (username, password, nivel) VALUES (:username, :password, :nivel)");
    $stmt->bindParam(':username', $nombre_usuario); // Enlazar el nombre de usuario
    $stmt->bindParam(':password', $hashed_password); // Enlazar la contraseña encriptada
    $stmt->bindParam(':nivel', $nivel); // Enlazar el nivel de usuario
    $stmt->execute();

    // Obtener el ID del usuario recién insertado
    $usuario_id = $pdo->lastInsertId();

    // Asegúrate de recuperar el valor de $dept del formulario
    $dept = $_POST['dept'] ?? null; // Departamento

    // Proceso subir imagen
    if (!empty($_FILES['firma']['name'])) {
        $imagen_nombre = basename($_FILES['firma']['name']); // Obtener el nombre base del archivo de firma
        $imagen_temp = $_FILES['firma']['tmp_name']; // Ruta temporal del archivo subido
        $ruta_destino = __DIR__ . "/../recursos/assets/firmas_dr/" . $imagen_nombre; // Ruta de destino para la imagen
        
        if (move_uploaded_file($imagen_temp, $ruta_destino)) {
            // Si la imagen se sube correctamente, almacenar el nombre y la ruta
            $ruta_imagen = "http://uabcs.net/pharmatechub/recursos/assets/firmas_dr/" . $imagen_nombre;
        } else {
            // Si hay un error al subir la imagen, lanzar una excepción
            throw new Exception("Error al subir la imagen: " . $_FILES['firma']['error']);
        }
    } else {
        // Si no hay imagen, establecer a null
        $imagen_nombre = null;
        $ruta_imagen = null;
    }

    // Insertar en la tabla info_drs
    $stmt = $pdo->prepare("INSERT INTO info_drs (nombre_s, apellido_paterno, apellido_materno, nombre_usuario, correo, usuario, dep, firma, ruta_firma)
     VALUES (:nombre_s, :apellido_paterno, :apellido_materno, :nombre_usuario, :correo, :usuario, :dept, :firma, :ruta_firma)");
    $stmt->bindParam(':nombre_s', $nombre_s); // Enlazar el nombre
    $stmt->bindParam(':apellido_paterno', $apellido_paterno); // Enlazar el apellido paterno
    $stmt->bindParam(':apellido_materno', $apellido_materno); // Enlazar el apellido materno
    $stmt->bindParam(':nombre_usuario', $nombre_usuario); // Enlazar el nombre de usuario
    $stmt->bindParam(':correo', $correo); // Enlazar el correo electrónico
    $stmt->bindParam(':usuario', $usuario_id); // Enlazar el ID del usuario
    $stmt->bindParam(':dept', $dept); // Enlazar el departamento
    $stmt->bindParam(':firma', $imagen_nombre); // Enlazar el nombre del archivo de firma
    $stmt->bindParam(':ruta_firma', $ruta_imagen); // Enlazar la ruta del archivo de firma
    $stmt->execute();

    // Confirmar la transacción
    $pdo->commit();
    echo "<script type='text/javascript'>
        alert('Registro y usuario añadidos exitosamente.');
        window.location.href='http://uabcs.net/pharmatechub/subdirector_inicio/ges_personal.php';
    </script>";
    
} catch (Exception $e) {
    // Revertir la transacción en caso de error
    $pdo->rollBack();
    echo "Transacción fallida: " . $e->getMessage();
}
?>

<?php
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

// Obtener el ID más alto existente de la tabla departamentos
$sql_max_id = "SELECT MAX(id_departamento) AS max_id FROM departamentos";
$stmt_max_id = $pdo->prepare($sql_max_id);
$stmt_max_id->execute();
$max_id_row = $stmt_max_id->fetch();
$max_id = $max_id_row['max_id'];

// Incrementar el ID para la nueva inserción
$new_id = $max_id + 1;

// Obtener datos del formulario
$nombre = $_POST['nombre'];
$descripcion = $_POST['descripcion'];
$nombre_usuario = $_POST['nombre_usuario'] ?? '';
$password = $_POST['password'] ?? '';
$nombre_s = $_POST['nombre_s'] ?? '';
$apellido_paterno = $_POST['apellido_paterno'] ?? '';
$apellido_materno = $_POST['apellido_materno'] ?? '';

// Aqui va un numero dos ya que este nivel fue elegido para el subdirector
$nivel = 2; 

// Verificacion de nombre de usuario
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
$stmt->bindParam(':username', $nombre_usuario);
$stmt->execute();
if ($stmt->rowCount() > 0) {
    // Si el nombre de usuario ya existe, mostrar una alerta y redirigir al usuario
    echo "<script>alert('El nombre de usuario ya existe. Por favor, elige otro.'); window.location.href = 'tu_pagina_de_error.php';</script>";
    exit;
}

// Hashing de la contraseña
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

try {
    // Iniciar la transacción
    $pdo->beginTransaction();

    // Insertar datos en la tabla departamentos con el nuevo ID
    $sql_insert = "INSERT INTO departamentos (id_departamento, nombre, descripcion) VALUES (?, ?, ?)";
    $stmt_insert = $pdo->prepare($sql_insert);
    $stmt_insert->execute([$new_id, $nombre, $descripcion]);

    // Insertar en la tabla users
    $stmt_user = $pdo->prepare("INSERT INTO users (username, password, nivel) VALUES (:username, :password, :nivel)");
    $stmt_user->bindParam(':username', $nombre_usuario);
    $stmt_user->bindParam(':password', $hashed_password);
    $stmt_user->bindParam(':nivel', $nivel);
    $stmt_user->execute();

    // Obtener el último ID insertado para el usuario
    $usuario_id = $pdo->lastInsertId();

    // Subir imagen de firma
    $firma = '';
    $ruta_firma = '';
    if (!empty($_FILES['firma']['name'])) {
        $imagen_nombre = basename($_FILES['firma']['name']);
        $imagen_temp = $_FILES['firma']['tmp_name'];
        $ruta_destino = __DIR__ . "/../recursos/assets/firmas_dr/" . $imagen_nombre;

        if (move_uploaded_file($imagen_temp, $ruta_destino)) {
            // Si la imagen se sube correctamente, almacenar el nombre y la ruta
            $firma = $imagen_nombre;
            $ruta_firma = "http://uabcs.net/pharmatechub/recursos/assets/firmas_dr/" . $imagen_nombre;
        } else {
            // Si hay un error al subir la imagen, lanzar una excepción
            throw new Exception("Error al subir la imagen: " . $_FILES['firma']['error']);
        }
    }

    // Insertar en la tabla info_jef_dep
    $stmt_info = $pdo->prepare("INSERT INTO info_jef_dep (nombre_s, apellido_p, apellido_m, dep, nombre_usuario, firma, ruta_firma) 
    VALUES (:nombre_s, :apellido_p, :apellido_m, :dep, :nombre_usuario, :firma, :ruta_firma)");
    $stmt_info->bindParam(':nombre_s', $nombre_s);
    $stmt_info->bindParam(':apellido_p', $apellido_paterno);
    $stmt_info->bindParam(':apellido_m', $apellido_materno);
    $stmt_info->bindParam(':nombre_usuario', $nombre_usuario);
    $stmt_info->bindParam(':dep', $new_id);
    $stmt_info->bindParam(':firma', $firma);
    $stmt_info->bindParam(':ruta_firma', $ruta_firma);
    $stmt_info->execute();

    // Confirmar la transacción y si no hay excepciones mostrar un mensaje y redirigir a pagina de gestion de departamentos
    $pdo->commit();
    echo "<script type='text/javascript'>
        alert('Registro y usuario añadidos exitosamente.');
        window.location.href='http://uabcs.net/pharmatechub/subdirector_inicio/ges_dep.php';
    </script>";
} catch (Exception $e) {
    // Si hay un error, revertir la transacción
    $pdo->rollBack();
    echo "Transacción fallida: " . $e->getMessage();
}
?>

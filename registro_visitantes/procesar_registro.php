
<?php
//-------------------------------------------------
//Iniciar sesión 
session_start();

// Configuración de conexión a la base de datos
$host = "localhost";
$db_username = "uabcsnet_adminParmatech";
$db_password = "CTln0KN41HJ3";
$dbname = "uabcsnet_ritienet_parmatech";

try {
    // Establecer la conexión PDO con la base de datos
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $db_username, $db_password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Configurar para lanzar excepciones en errores de PDO
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC // Configurar el modo de obtención de resultados predeterminado a array asociativo
    ]);
} catch (PDOException $e) {
    // En caso de error al conectar, mostrar un mensaje de error y terminar la ejecución
    die("No se pudo conectar a la base de datos: " . $e->getMessage());
}



// Recuperar el nombre de usuario del formulario
$nombre_usuario = $_POST['nombre_usuario'] ?? '';
$password = $_POST['password'] ?? '';
$nivel = 0;



// Verificar si el nombre de usuario ya existe
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
$stmt->bindParam(':username', $nombre_usuario);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    // El nombre de usuario ya existe
    echo "<script>alert('El nombre de usuario ya existe. Por favor, elige otro.'); window.history.back();</script>";
    exit;
}

//Hasheo de contraseña
$hashed_password = password_hash($password, PASSWORD_DEFAULT);


// Recuperar los datos del formulario
$nombre = $_POST['nombre'] ?? '';
$apellido_paterno = $_POST['apellido_paterno'] ?? '';
$apellido_materno = $_POST['apellido_materno'] ?? '';
$pais = $_POST['pais'] ?? '';
$sexo = $_POST['sexo'] ?? '';
$correo = $_POST['correo'] ?? '';   
$institucion = $_POST['institucion'] ?? '';
$num_control = $_POST['num_control'] ?? '';
$fecha_nacimiento = $_POST['fecha_nacimiento'] ?? '';
$firma== $_POST['firma'] ?? '';

// Validar y limpiar los datos aquí
try {
    // Iniciar la transacción
    $pdo->beginTransaction();

    // Insertar en la tabla info_visitantes
    $stmt = $pdo->prepare("INSERT INTO info_visitantes (nombre_usuario, nombre, apellido_paterno, apellido_materno, pais, sexo, correo, institucion, num_control, fecha_nacimiento,firma,ruta_firma) 
            VALUES (:nombre_usuario, :nombre, :apellido_paterno, :apellido_materno, :pais, :sexo, :correo, :institucion, :num_control, :fecha_nacimiento,:firma,:ruta_firma)
    ");

    // Vincular los parámetros y ejecutar
    $stmt->bindParam(':nombre_usuario', $nombre_usuario);
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':apellido_paterno', $apellido_paterno);
    $stmt->bindParam(':apellido_materno', $apellido_materno);
    $stmt->bindParam(':pais', $pais);
    $stmt->bindParam(':sexo', $sexo);
    $stmt->bindParam(':correo', $correo);
    $stmt->bindParam(':institucion', $institucion);
    $stmt->bindParam(':num_control', $num_control);
    $stmt->bindParam(':fecha_nacimiento', $fecha_nacimiento);


    // Proceso subir imagen

    if (!empty($_FILES['firma']['name'])) {
        $imagen_nombre = basename($_FILES['firma']['name']);
        $imagen_temp = $_FILES['firma']['tmp_name'];
        $ruta_destino = __DIR__ . "/../recursos/assets/firmas_v/" . $imagen_nombre;
        
        if (move_uploaded_file($imagen_temp, $ruta_destino)) {
            $ruta_imagen = "http://uabcs.net/pharmatechub/recursos/assets/firmas_v/" . $imagen_nombre;
            $stmt->bindParam(':firma', $imagen_nombre);
            $stmt->bindParam(':ruta_firma', $ruta_imagen);
        } else {
            throw new Exception("Error al subir la imagen: " . $_FILES['firma']['error']);
        }
    } else {
        $imagen_nombre = null;
        $ruta_imagen = null;
        $stmt->bindParam(':firma', $imagen_nombre);
        $stmt->bindParam(':ruta_firma', $ruta_imagen);
    }
    //----------------------------------------------------------------------------------


    if (!$stmt->execute()) {
        throw new Exception("Error al registrar los datos.");
    }

    // Insertar en la tabla users
    $stmt = $pdo->prepare("INSERT INTO users (username, password, nivel) VALUES (:username, :password, :nivel)");
    // Vincular los parámetros y ejecutar
    $stmt->bindParam(':username', $nombre_usuario);
    $stmt->bindParam(':password', $hashed_password);
    $stmt->bindParam(':nivel', $nivel);

    if (!$stmt->execute()) {
        throw new Exception("Error al añadir el usuario.");
    }

    // Confirmar la transacción
    $pdo->commit();
    echo "<script>
     alert('Registro y usuario añadidos exitosamente.');
     window.location.href='http://uabcs.net/pharmatechub/inicio/index.php';
     </script>";

} catch (Exception $e) {
    // Revertir la transacción en caso de error
    $pdo->rollBack();
    echo "Transacción fallida: " . $e->getMessage();
}
?>
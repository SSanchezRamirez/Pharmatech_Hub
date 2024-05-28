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

$error = ''; // Inicializar la variable de error

// Verificar si se proporciona un ID de departamento
if (isset($_GET['id_departamento'])) {
    $id_departamento = $_GET['id_departamento']; // Obtener el ID del departamento desde la URL
    // Consulta SQL para obtener los detalles del departamento y el jefe de departamento asociado
    $consulta = "SELECT d.*, j.nombre_s, j.apellido_m, j.apellido_p, j.nombre_usuario, u.password
                 FROM departamentos d
                 JOIN info_jef_dep j ON d.id_departamento = j.dep
                 JOIN users u ON j.nombre_usuario = u.username
                 WHERE d.id_departamento = ?";
    $stmt = $pdo->prepare($consulta); // Preparar la consulta
    $stmt->execute([$id_departamento]); // Ejecutar la consulta con el ID del departamento
    $departamento = $stmt->fetch(); // Obtener los datos del departamento
    if (!$departamento) {
        die('Departamento no encontrado'); // Mostrar mensaje de error si no se encuentra el departamento
    }
} else {
    die('Error: ID de departamento no proporcionado.'); // Mostrar mensaje de error si no se proporciona el ID del departamento
}

// Procesar el formulario de actualización
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre']; // Obtener el nombre del departamento del formulario
    $descripcion = $_POST['descripcion']; // Obtener la descripción del departamento del formulario
    $nombre_s = $_POST['nombre_s']; // Obtener el nombre del jefe del departamento del formulario
    $apellido_p = $_POST['apellido_p']; // Obtener el apellido paterno del jefe del departamento del formulario
    $apellido_m = $_POST['apellido_m']; // Obtener el apellido materno del jefe del departamento del formulario
    $nombre_usuario = $_POST['nombre_usuario']; // Obtener el nombre de usuario del jefe del departamento del formulario
    $password = !empty($_POST['password']) ? $_POST['password'] : null; // Obtener la contraseña del formulario (si se proporciona)

    // Iniciar una transacción
    $pdo->beginTransaction();

    try {
        // Actualizar el departamento
        $consulta = "UPDATE departamentos SET nombre = ?, descripcion = ? WHERE id_departamento = ?";
        $stmt = $pdo->prepare($consulta); // Preparar la consulta
        $stmt->execute([$nombre, $descripcion, $id_departamento]); // Ejecutar la consulta con los datos actualizados
        
        // Actualizar el nombre de usuario en la tabla users si es necesario
        if ($nombre_usuario !== $departamento['nombre_usuario']) {
            $consulta = "UPDATE users SET username = ? WHERE username = ?";
            $stmt = $pdo->prepare($consulta); // Preparar la consulta
            $stmt->execute([$nombre_usuario, $departamento['nombre_usuario']]); // Ejecutar la consulta con el nuevo nombre de usuario
        }

        // Actualizar el jefe de departamento
        $consulta = "UPDATE info_jef_dep SET nombre_s = ?, apellido_p = ?, apellido_m = ?, nombre_usuario = ? WHERE dep = ?";
        $stmt = $pdo->prepare($consulta); // Preparar la consulta
        $stmt->execute([$nombre_s, $apellido_p, $apellido_m, $nombre_usuario, $id_departamento]); // Ejecutar la consulta con los datos actualizados del jefe de departamento

        // Actualizar la contraseña del usuario si se proporciona una nueva
        if ($password !== null) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Hashear la nueva contraseña
            $consulta = "UPDATE users SET password = ? WHERE username = ?";
            $stmt = $pdo->prepare($consulta); // Preparar la consulta
            $stmt->execute([$hashed_password, $nombre_usuario]); // Ejecutar la consulta con la nueva contraseña
        }

        // Confirmar la transacción
        $pdo->commit();

        // Redirigir a la lista de departamentos
        header("Location: http://uabcs.net/pharmatechub/subdirector_inicio/ges_dep.php"); 
    } catch (Exception $e) {
        // Revertir la transacción si algo falla
        $pdo->rollBack();
        echo "<p>Error al actualizar el departamento y su información asociada</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Departamento</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <!-- Fuentes de Google -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Newsreader:wght@500&display=swap" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Sora:wght@400;700&display=swap" />
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="index.css" />
</head>
<body>
<div class="rectangle-7"></div>
    <div class="vector"></div>

    <div class="container-fluid">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <a class="navbar-brand" href="http://uabcs.net/pharmatechub/Hub/home_p.html">Pharmatechub</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item active">
                        <a class="nav-link" href="http://uabcs.net/pharmatechub/subdirector_inicio/ges_dep.php">Volver<span class="sr-only">(current)</span></a>
                    </li>
                    <li class="nav-item active">
                        <a class="nav-link" href="http://uabcs.net/pharmatechub/Hub/home_p.html" onclick="cerrarSesion()">Cerrar Sesion <span class="sr-only">(current)</span></a>
                    </li>
                </ul>
            </div>
        </nav>
    </div>

    <div class="container mt-5" style="background-color: #FFFF;">
        <h2>Editar Departamento</h2>
        <form action="" method="post">

            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <br>
                <input class="form-control" type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($departamento['nombre']) ?>" required>
            </div>
            <div class="form-group">
                <label for="descripcion">Descripción:</label>
                <br>
                <textarea class="form-control" id="descripcion" name="descripcion"><?= htmlspecialchars($departamento['descripcion']) ?></textarea>
            </div>

            <h4>Datos del Jefe de Departamento</h4>
            <div class="form-group">
                <label for="nombre_s">Nombre(s)</label>
                <input type="text" class="form-control" id="nombre_s" name="nombre_s" value="<?= htmlspecialchars($departamento['nombre_s']) ?>" maxlength="50" required>
            </div>
            <div class="form-group">
                <label for="apellido_p">Apellido Paterno</label>
                <input type="text" class="form-control" id="apellido_p" name="apellido_p" value="<?= htmlspecialchars($departamento['apellido_p']) ?>" maxlength="50" required>
            </div>
            <div class="form-group">
                <label for="apellido_m">Apellido Materno</label>
                <input type="text" class="form-control" id="apellido_m" name="apellido_m" value="<?= htmlspecialchars($departamento['apellido_m']) ?>" maxlength="50" required>
            </div>
            <div class="form-group">
                <label for="nombre_usuario">Nombre de Usuario</label>
                <input type="text" class="form-control" id="nombre_usuario" name="nombre_usuario" value="<?= $departamento['nombre_usuario'] ?>" maxlength="50" required>
            </div>
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" class="form-control" id="password" name="password" maxlength="50"
                       pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="La contraseña debe tener al menos 8 caracteres y contener al menos un número, una letra mayúscula y una letra minúscula." oninput="validatePassword()">
                <p id="passwordInvalidMessage" style="color: red; display: none;">La contraseña debe tener al menos 8 caracteres y contener al menos un número, una letra mayúscula y una letra minúscula.</p>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary" value="Guardar cambios">Actualizar</button>
            </div>
        </form>
    </div>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<script>
    function validatePassword() {
        var passwordInput = document.getElementById('password');
        var invalidMessage = document.getElementById('passwordInvalidMessage');
        var isValid = passwordInput.checkValidity();
        if (isValid) {
            passwordInput.classList.remove('invalid');
            invalidMessage.style.display = 'none';
        } else {
            passwordInput.classList.add('invalid');
            invalidMessage.style.display = 'block';
        }
    }
</script>
</body>
</html>

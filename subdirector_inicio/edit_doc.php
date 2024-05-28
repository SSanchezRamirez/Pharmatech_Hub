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

// Verificar si se proporciona un ID de doctor
if(isset($_GET['id_doc'])) {
    $id_doc = $_GET['id_doc']; // Obtener el ID del doctor desde la URL
    // Consulta SQL para obtener los detalles del doctor
    $consulta = "SELECT * FROM info_drs WHERE id_doc = ?";
    $stmt = $pdo->prepare($consulta); // Preparar la consulta
    $stmt->execute([$id_doc]); // Ejecutar la consulta con el ID del doctor
    $doctor = $stmt->fetch(); // Obtener los datos del doctor
    if (!$doctor) {
        die('Doctor no encontrado'); // Mostrar mensaje de error si no se encuentra el doctor
    }
} else {
    die('Error: ID de doctor no proporcionado.'); // Mostrar mensaje de error si no se proporciona el ID del doctor
}

// Procesar el formulario de actualización
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre_s = $_POST['nombre_s']; // Obtener el nombre del doctor del formulario
    $apellido_paterno = $_POST['apellido_paterno']; // Obtener el apellido paterno del doctor del formulario
    $apellido_materno = $_POST['apellido_materno']; // Obtener el apellido materno del doctor del formulario
    $nombre_usuario = $_POST['nombre_usuario']; // Obtener el nombre de usuario del doctor del formulario
    $correo = $_POST['correo']; // Obtener el correo del doctor del formulario
    $dep = $_POST['dep']; // Obtener el departamento del doctor del formulario

    // Consulta SQL para actualizar los datos del doctor
    $consulta = "UPDATE info_drs SET nombre_s = ?, apellido_paterno = ?, apellido_materno = ?, nombre_usuario = ?, correo = ?, dep = ? WHERE id_doc = ?";
    $stmt = $pdo->prepare($consulta); // Preparar la consulta
    if($stmt->execute([$nombre_s, $apellido_paterno, $apellido_materno, $nombre_usuario, $correo, $dep, $id_doc])) {
        // Redirigir a la página de gestión de personal si la actualización es exitosa
        header("Location: http://uabcs.net/pharmatechub/subdirector_inicio/ges_personal.php");
    } else {
        echo "<p>Error al actualizar el doctor</p>"; // Mostrar mensaje de error si la actualización falla
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <!-- Establecer la codificación del documento -->
    <meta charset="UTF-8" />
    <title>Editar Doctor</title>
    <!-- Incluir Bootstrap CSS desde un CDN para estilos responsivos -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <!-- Incluir fuentes de Google -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Newsreader:wght@500&display=swap" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Sora:wght@400;700&display=swap" />
    <!-- Incluir estilos personalizados -->
    <link rel="stylesheet" href="index.css" />
</head>
<body>

<!-- Elementos decorativos en el diseño de la página -->
<div class="rectangle-7"></div>
<div class="vector"></div>

<!-- Contenedor principal fluido -->
<div class="container-fluid">
    <!-- Barra de navegación -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <!-- Enlace a la página principal de Pharmatechub -->
        <a class="navbar-brand" href="http://uabcs.net/pharmatechub/Hub/home_p.html">Pharmatechub</a>
        <!-- Botón para colapsar la barra de navegación en pantallas pequeñas -->
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <!-- Contenedor colapsable de la barra de navegación -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <!-- Lista de elementos de navegación -->
            <ul class="navbar-nav ml-auto">
                <!-- Elemento de navegación activo para volver a la página anterior -->
                <li class="nav-item active">
                    <a class="nav-link" href="http://uabcs.net/pharmatechub/subdirector_inicio/ges_personal.php">Volver<span class="sr-only">(current)</span></a>
                </li>
                <!-- Elemento de navegación activo para cerrar sesión -->
                <li class="nav-item active">
                    <a class="nav-link" href="http://uabcs.net/pharmatechub/Hub/home_p.html" onclick="cerrarSesion()">Cerrar Sesion <span class="sr-only">(current)</span></a>
                </li>
            </ul>
        </div>
    </nav>
</div>

<!-- Contenedor principal para el formulario de edición del doctor -->
<div class="container mt-5" style="background-color: #FFFF;">
    <h2>Editar Doctor</h2>
    <!-- Formulario para editar los datos del doctor -->
    <form action="" method="post">
        <!-- Campo oculto para el ID del doctor -->
        <input type="hidden" name="id_doc" value="<?php echo $doctor['id_doc']; ?>" />
        <!-- Grupo de formulario para el nombre del doctor -->
        <div class="form-group">
            <label>Nombre:</label>
            <input type="text" name="nombre_s" value="<?php echo htmlspecialchars($doctor['nombre_s']); ?>" class="form-control" required>
        </div>
        <!-- Grupo de formulario para el apellido paterno del doctor -->
        <div class="form-group">
            <label>Apellido Paterno:</label>
            <input type="text" name="apellido_paterno" value="<?php echo htmlspecialchars($doctor['apellido_paterno']); ?>" class="form-control" required>
        </div>
        <!-- Grupo de formulario para el apellido materno del doctor -->
        <div class="form-group">
            <label>Apellido Materno:</label>
            <input type="text" name="apellido_materno" value="<?php echo htmlspecialchars($doctor['apellido_materno']); ?>" class="form-control" required>
        </div>
        <!-- Grupo de formulario para el nombre de usuario del doctor -->
        <div class="form-group">
            <label>Nombre de Usuario:</label>
            <input type="text" name="nombre_usuario" value="<?php echo htmlspecialchars($doctor['nombre_usuario']); ?>" class="form-control" required>
        </div>
        <!-- Grupo de formulario para el correo electrónico del doctor -->
        <div class="form-group">
            <label>Correo:</label>
            <input type="email" name="correo" value="<?php echo htmlspecialchars($doctor['correo']); ?>" class="form-control" required>
        </div>
        <!-- Grupo de formulario para el departamento del doctor -->
        <div class="form-group">
            <label for="dep">Departamento:</label>
            <select name="dep" class="form-control" required>
                <option value="">Seleccione un departamento</option>
                <?php
                // Obtener la lista de departamentos para el desplegable
                $stmt = $pdo->query("SELECT id_departamento, nombre FROM departamentos");
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    // Verifica si el departamento actual es el del doctor
                    $selected = $doctor['dep'] == $row['id_departamento'] ? 'selected' : '';
                    echo "<option value='" . $row['id_departamento'] . "' $selected>" . $row['nombre'] . "</option>";
                }
                ?>
            </select>
        </div>
        <!-- Botón para enviar el formulario de actualización -->
        <button type="submit" class="btn btn-primary">Actualizar</button>
    </form>
</div>

<!-- Incluir JavaScript de Bootstrap desde un CDN -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>

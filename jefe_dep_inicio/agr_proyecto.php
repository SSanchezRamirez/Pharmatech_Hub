<?php
session_start();// Inicia la sesión para mantener la información del usuario entre páginas
// Configuración de la conexión a la base de datos
$host = "localhost";
$db_username = "uabcsnet_adminParmatech";
$db_password = "CTln0KN41HJ3";
$dbname = "uabcsnet_ritienet_parmatech";

try {
    // Intenta conectar a la base de datos utilizando PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $db_username, $db_password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    // En caso de error, muestra un mensaje de error y termina la ejecución del script
    die("No se pudo conectar a la base de datos: " . $e->getMessage());
}

$nombre_usuario = $_SESSION['username'];
$consulta_departamento = "SELECT dep FROM info_jef_dep WHERE nombre_usuario = :nombre_usuario";
$stmt = $pdo->prepare($consulta_departamento);
$stmt->execute(['nombre_usuario' => $nombre_usuario]);
$departamento = $stmt->fetchColumn();

// Procesar el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener el ID más alto actual de la tabla proyectos
    $consultaMaxId = "SELECT MAX(id_proyecto) AS max_id FROM proyectos";
    $stmtMaxId = $pdo->query($consultaMaxId);
    $rowMaxId = $stmtMaxId->fetch(PDO::FETCH_ASSOC);
    $maxId = $rowMaxId['max_id'] + 1;

    // Preparar la consulta de inserción con el nuevo ID
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $id_departamento = $departamento;

    $sql = "INSERT INTO proyectos (id_proyecto, nombre, descripcion, id_departamento) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);

    // Ejecutar la inserción con el nuevo ID
    if ($stmt->execute([$maxId, $nombre, $descripcion, $id_departamento])) {
        // Redirección con mensaje de éxito
        header("Location: http://uabcs.net/pharmatechub/jefe_dep_inicio/gen_proyectos.php");
        exit;
    } else {
        // Manejo de errores
        echo "<p>Error al añadir el proyecto</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Añadir Proyecto</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="index.css" />
    <style>

        

.rectangle-7 {
  position: absolute;
  width: 100%;
  height: 100vh;
  top: 0;
  left: 0;
  background: url(imagenes/fondo.png);
  background-size: cover; /* Asegura que la imagen cubra todo el contenedor */
  border-radius: 70px;
  z-index: -1;

}



.vector {
  position: absolute;
  width: 100%;
  height: 100vh;
  top: 0;
  left: 0;
  background: url(imagenes/vector.png);
  background-size: cover; /* Asegura que la imagen cubra todo el contenedor */
  border-radius: 70px;
  z-index: 0;

}

    </style>

</head>
<body>
    <div class="rectangle-7"></div>
    <div class="vector" style="  z-index: -1;
" ></div>

<!-- Barra de navegación -->
<div class="container-fluid">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <a class="navbar-brand" href="http://uabcs.net/pharmatechub/Hub/home_p.html">Pharmatechub</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item active">
                        <a class="nav-link" href="http://uabcs.net/pharmatechub/jefe_dep_inicio/gen_proyectos.php">Volver</a>
                    </li>
                    <li class="nav-item active">
                        <a class="nav-link" href="http://uabcs.net/pharmatechub/Hub/home_p.html" onclick="cerrarSesion()">Cerrar Sesión <span class="sr-only">(current)</span></a>
                    </li>
                </ul>
            </div>
        </nav>
    </div>

  <!-- Formulario para añadir un nuevo proyecto -->
<div class="container mt-5" bgcolor="#FFFFFF" z-index=22 style="background-color: #FFFFFF; border-radius: 20px; overflow: hidden; margin-bottom: 10px;">
    <h2>Añadir Nuevo Proyecto</h2>
     <!-- Formulario con campos para el nombre y la descripción del proyecto -->
    <form action="" method="post">
        <div class="form-group"class="table" >
            <label for="nombre">Nombre del Proyecto:</label>
            <input type="text" class="form-control" id="nombre" name="nombre" required>
        </div>
        <div class="form-group">
            <label for="descripcion">Descripción:</label>
            <textarea class="form-control" id="descripcion" name="descripcion" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Añadir Proyecto</button>
    </form>
</div>
   <!-- Scripts JavaScript -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>

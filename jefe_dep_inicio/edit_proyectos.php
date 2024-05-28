<?php
session_start(); // Inicia la sesión para mantener la información del usuario entre páginas

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
// Verifica si se proporciona un ID de proyecto a través de la URL
if(isset($_GET['id'])) {
    $id_proyecto = $_GET['id'];
    
    // Consulta para obtener los detalles del proyecto con el ID proporcionado
    $consulta = "SELECT * FROM proyectos WHERE id_proyecto = ?";
    $stmt = $pdo->prepare($consulta);
    $stmt->execute([$id_proyecto]);
    $proyecto = $stmt->fetch();
        // Verifica si se encontró el proyecto con el ID proporcionado
    if (!$proyecto) {
        die('Proyecto no encontrado');
    }
} else {
    // Si no se proporciona un ID de proyecto, muestra un mensaje de error y termina la ejecución del script
    die('Error: ID de proyecto no proporcionado.');

}

// Procesar el formulario de actualización
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
 // Consulta para actualizar los detalles del proyecto en la base de datos
    $consulta = "UPDATE proyectos SET nombre = ?, descripcion = ? WHERE id_proyecto = ?";
    $stmt = $pdo->prepare($consulta);
    
    // Ejecuta la consulta preparada con los nuevos valores del proyecto
    if($stmt->execute([$nombre, $descripcion, $id_proyecto])){
         // Si la actualización es exitosa, redirige a la página principal con un mensaje de éxito
        header("Location: index.php?mensaje=actualizado");
    } else {
          // Si hay un error al actualizar el proyecto, muestra un mensaje de error
        echo "<p>Error al actualizar el proyecto</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Editar Proyecto</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <style>
        input.invalid {
            border-color: red;
        }
        .container{
            max-width: 50%;
        }

        .rectangle-7 {
            position: absolute;
            width: 100%;
            height: 150vh;
            top: 0;
            left: 0;
            background: url(http://uabcs.net/pharmatechub/recursos/assets/images/fondo.png) no-repeat center center;
            background-size: cover; /* Asegura que la imagen cubra todo el contenedor */
            border-radius: 70px;
            z-index: -2;
        }
        .vector {
            position: absolute;
            width: 100%;
            height: 180vh;
            top: 0;
            left: 0;
            background: url(http://uabcs.net/pharmatechub/recursos/assets/images/vector.png) no-repeat;
            background-size: cover; /* Asegura que la imagen cubra todo el contenedor */
            border-radius: 70px;
            z-index: -1;
        }
    </style>

</head>
<body>

<div class="rectangle-7"></div>
    <div class="vector"></div>

    <div class="container-fluid">
             <!-- Código para la barra de navegación -->
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <a class="navbar-brand" href="http://uabcs.net/pharmatechub/Hub/home_p.html">Pharmatechub</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="http://uabcs.net/pharmatechub/jefe_dep_inicio/gen_proyectos.php" >Volver<span class="sr-only">(current)</span></a>
                </li>  
                <li class="nav-item active">
                    <a class="nav-link" href="http://uabcs.net/pharmatechub/Hub/home_p.html" onclick="cerrarSesion()">Cerrar Sesion <span class="sr-only">(current)</span></a>
                </li>   
            </ul>
            </div>
        </nav>
    </div>


<div class="container mt-5" style="background-color: #FFFFFF; border-radius: 20px; overflow: hidden;">
    <h2>Editar Proyecto</h2>
      <!-- Formulario con los detalles del proyecto para editar -->
    <form action="" method="post">
        <input type="hidden" name="id_proyecto" value="<?php echo $proyecto['id_proyecto']; ?>" />
        <div class="form-group">
            <label>Nombre:</label>
            <input type="text" name="nombre" value="<?php echo htmlspecialchars($proyecto['nombre']); ?>" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Descripción:</label>
            <textarea name="descripcion" class="form-control" required><?php echo htmlspecialchars($proyecto['descripcion']); ?></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Actualizar</button>
    </form>
</div>
<!-- Scripts JavaScript -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>

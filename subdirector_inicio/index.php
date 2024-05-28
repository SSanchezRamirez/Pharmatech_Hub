<?php
// Inicia la sesión para mantener el estado del usuario
session_start(); 

// Detalles de la conexión a la base de datos
$host = "localhost";
$db_username = "uabcsnet_adminParmatech";
$db_password = "CTln0KN41HJ3";
$dbname = "uabcsnet_ritienet_parmatech";

try {
    // Intenta conectar con la base de datos utilizando PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $db_username, $db_password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    // En caso de error al conectar, muestra un mensaje y termina el script
    die("No se pudo conectar a la base de datos: " . $e->getMessage());
}

// Consulta para obtener la información necesaria para la gestión de personal
$consulta = "SELECT h_registro_visitas.*, departamentos.nombre AS nombre_departamento, proyectos.nombre AS nombre_proyecto, motivos_visita.nombre_motivo
FROM h_registro_visitas
INNER JOIN departamentos ON h_registro_visitas.id_departamento = departamentos.id_departamento
INNER JOIN proyectos ON h_registro_visitas.id_proyecto = proyectos.id_proyecto
INNER JOIN motivos_visita ON h_registro_visitas.m_visita = motivos_visita.id_motivo
WHERE h_registro_visitas.aprobacion = 2";

// Prepara la consulta
$resultado = $pdo->prepare($consulta);

// Ejecuta la consulta
$resultado->execute();


// Variable para almacenar errores (actualmente no utilizada)
$error = '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Inicio Subdirector</title>
    <!-- Fuentes de Google -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Newsreader:wght@500&display=swap" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Sora:wght@400;700&display=swap" />
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="index.css" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>

<body>

    <div class="rectangle-7"></div>
    <div class="vector"></div>

    <!-- Barra de navegación -->
    <div class="container-fluid">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <a class="navbar-brand" href="http://uabcs.net/pharmatechub/Hub/home_p.html">Pharmatechub</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <!-- Enlace para cerrar sesión -->
                    <li class="nav-item active">
                        <a class="nav-link" href="http://uabcs.net/pharmatechub/Hub/home_p.html" onclick="cerrarSesion()">Cerrar Sesión <span class="sr-only">(current)</span></a>
                    </li>   
                </ul>
            </div>
        </nav>
    </div>

    <!-- Contenedor principal -->
    <div class="container">
        <div class="row">
            <div class="col-sm-3"></div>
            <div class="col-sm-6">
                <!-- Botones para gestionar personal y departamentos -->
                <div class="row">
                    <div class="col-sm">
                        <button class="btn btn-primary g_pesonal">Gestión de Personal</button>
                    </div>
                    <div class="col-sm"></div>
                    <div class="col-sm">
                        <button class="btn btn-primary g_departamentos">Gestión de Departamentos</button>
                    </div>
                </div>
            </div>
            <div class="col-sm-3"></div>
        </div>
    </div>

    <!--Librerías JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
      
    <!-- Script para redireccionar al hacer clic en los botones -->
    <script>
        $(document).ready(function(){
            // Redirige a la página de gestión de personal al hacer clic en el botón correspondiente
            $(".g_pesonal").click(function(){
                location.href = "http://uabcs.net/pharmatechub/subdirector_inicio/ges_personal.php";
            }); 
            // Redirige a la página de gestión de departamentos al hacer clic en el botón correspondiente
            $(".g_departamentos").click(function(){
                location.href = "http://uabcs.net/pharmatechub/subdirector_inicio/ges_dep.php";
            }); 
        });
    </script>
</body>
</html>

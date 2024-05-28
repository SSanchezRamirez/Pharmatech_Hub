<?php
// Inicia o reanuda la sesión existente
session_start(); 

$host = "localhost";
$db_username = "uabcsnet_adminParmatech";
$db_password = "CTln0KN41HJ3";
$dbname = "uabcsnet_ritienet_parmatech";

try {
    // Conexión a la base de datos usando PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $db_username, $db_password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    // Manejo de errores de conexión a la base de datos
    die("No se pudo conectar a la base de datos: " . $e->getMessage());
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Inicio Director</title>
    <link rel="icon" href="http://uabcs.net/pharmatechub/recursos/assets/images/logo.png" type="image/png">

    <!-- Fuentes de Google -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Newsreader:wght@500&display=swap" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Sora:wght@400;700&display=swap" />
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="index.css" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>

<body>
    <!-- Fondo de la pagina -->
    <div class="rectangle-7"></div>
    <div class="vector"></div>
    <!-- Barra de navegación con enlaces -->
    <div class="container-fluid">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <a class="navbar-brand" href="http://uabcs.net/pharmatechub/Hub/home_p.html">Pharmatechub</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="http://uabcs.net/pharmatechub/Hub/home_p.html" onclick="cerrarSesion()">Cerrar Sesion <span class="sr-only">(current)</span></a>
                </li>   
            </ul>
            </div>
        </nav>
    </div>
    <div class="container">

        <div class="row">
            <div class="col-sm-3">

            </div>

            <div class="col-sm-6">
                <div class="row">
                    <div class="col-sm">
                    <button class="btn btn-primary stats">Revisión de estadísticas</button>
                    </div>

                    <div class="col-sm">
                    </div>
                    
                    <div class="col-sm">
                    <button class="btn btn-primary rhistorial">Historial de visitas</button>
                    </div>
                </div>
            </div>


            <div class="col-sm-3">

            </div>
        </div>

        
    </div>


    <!--Librerias -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
      


    <!-- Scripts para manejar los botones de acciones -->
    <script>
        $(document).ready(function(){
            $(".stats").click(function(){
                // Obtener el nombre del visitante de la sesión
                var nombreVisitante = "<?php echo $_SESSION['username']; ?>";
                // Redireccionar a stats.php con el nombre del visitante como parámetro
                location.href = "http://uabcs.net/pharmatechub/director_inicio/stats.php?nombre=" + nombreVisitante;
            }); 
            $(".rhistorial").click(function(){
                // Obtener el nombre del visitante de la sesión
                var nombreVisitante = "<?php echo $_SESSION['username']; ?>";
                // Redireccionar a historial.php con el nombre del visitante como parámetro
                location.href = "http://uabcs.net/pharmatechub/director_inicio/historial.php?nombre=" + nombreVisitante;
            });     
        });
    </script>


</body>
</html>

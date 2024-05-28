
<?php
    session_start();// Iniciar la sesión para mantener la información del usuario
  // Configuración de la conexión a la base de datos
    $host = "localhost";
    $db_username = "uabcsnet_adminParmatech";
    $db_password = "CTln0KN41HJ3";
    $dbname = "uabcsnet_ritienet_parmatech";

    try {
          // Establecer una conexión PDO a la base de datos
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $db_username, $db_password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,// Configurar para lanzar excepciones en caso de errores
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC // Configurar el modo de recuperación predeterminado a asociativo
        ]);
    } catch (PDOException $e) {
            // Manejar cualquier error en la conexión a la base de datos
        die("No se pudo conectar a la base de datos: " . $e->getMessage());
    }
    $error = '';// Variable para almacenar mensajes de error, aunque no se utiliza aparentemente
    
 // A partir de aquí comienza el código HTML
 
?>



<!DOCTYPE html>
<html lang="es">
<head>
      <!-- Aquí se definen metadatos y se incluyen enlaces a hojas de estilos -->

    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Historial Visitas - Visitante</title>
    <!-- Fuentes de Google -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Newsreader:wght@500&display=swap" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Sora:wght@400;700&display=swap" />
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../index.css" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
 <!-- Estilos personalizados -->
    <style>
        :root {
            --default-font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto,
                Ubuntu, "Helvetica Neue", Helvetica, Arial, "PingFang SC",
                "Hiragino Sans GB", "Microsoft Yahei UI", "Microsoft Yahei",
                "Source Han Sans CN", sans-serif;
        }

        .container {
            z-index = 1;

        }


        input,
        select,
        textarea,
        button {
            outline: 0;
        }
        .container-fluid{
            position: relative;
            width: 100%;
            min-height: 100vh;
            z-index = -1;
        }


        



    </style>

</head>

<body>
    <div class="rectangle-7"></div>
    <div class="vector"></div>
    
    <div class="container-fluid">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <a class="navbar-brand" href="http://uabcs.net/pharmatechub/Hub/home_p.html">Pharmatech</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
             <!-- Lista de elementos de la barra de navegación -->
            <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                  <!-- Enlace para volver a la página anterior -->
                 <li class="nav-item active">
                     <a class="nav-link" href="http://uabcs.net/pharmatechub/visitante_inicio//index.php" >Volver <span class="sr-only">(current)</span></a>
                 </li> 
                 <!-- Enlace para cerrar sesión -->  
                <li class="nav-item active">
                    <a class="nav-link" href="http://uabcs.net/pharmatechub/Hub/home_p.html" onclick="cerrarSesion()">Cerrar Sesion <span class="sr-only">(current)</span></a>
                </li>   
                
            </ul>
            </div>
        </nav>


  <!-- Iframe para mostrar el visor de PDF incrustado -->
        <iframe src="http://uabcs.net/pharmatechub/visitante_inicio/c_aceptacion/index.php" width="70%" height="800px">
        Este navegador no soporta PDFs. Por favor descarga el PDF para verlo: <a href="ruta/a/tu/archivo.pdf">Descargar PDF</a>.
        </iframe>
    

    </div>
</div>



<!--Librerias -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>

</body>


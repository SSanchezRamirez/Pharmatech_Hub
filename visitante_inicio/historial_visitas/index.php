<?php
// Inicia una nueva sesión o reanuda la existente
session_start();
// Define las credenciales de la base de datos
$host = "localhost";
$db_username = "uabcsnet_adminParmatech";
$db_password = "CTln0KN41HJ3";
$dbname = "uabcsnet_ritienet_parmatech";

try {
    // Intenta establecer una conexión con la base de datos
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $db_username, $db_password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
       // Si la conexión falla, termina el script
    die("No se pudo conectar a la base de datos: " . $e->getMessage());
}
$error = '';

// Verifica si el nombre de usuario está definido en la sesión
if(isset($_SESSION['username'])) {
    // Consulta que obtiene todos los datos de h_registro_visitas para el usuario de la sesión
    $consulta = "SELECT hrv.*, 
    proyectos.nombre AS proyecto_nombre,
    departamentos.nombre AS departamento_nombre,
    (SELECT txt_estado FROM estados_reg WHERE id = hrv.aprobacion) AS txt_estado,
    CONCAT(iv.nombre, ' ', iv.apellido_paterno, ' ', iv.apellido_materno) AS nombre_visitante
        
    FROM h_registro_visitas hrv
    INNER JOIN proyectos ON hrv.id_proyecto = proyectos.id_proyecto 
    INNER JOIN departamentos ON hrv.id_departamento = departamentos.id_departamento 
    INNER JOIN info_visitantes iv ON hrv.nombre_usuario = iv.nombre_usuario
    WHERE hrv.nombre_usuario = :username AND hrv.aprobacion > 5";

 // Prepara la consulta SQL
    $resultado = $pdo->prepare($consulta);
      // Vincula el parámetro :username a $_SESSION['username']
    $resultado->bindParam(':username', $_SESSION['username'], PDO::PARAM_STR);
    // Ejecuta la consulta
    $resultado->execute();

// Verifica si la consulta devolvió algún resultado
    if ($resultado->rowCount() > 0) {
        // HTML y JavaScript para mostrar los resultados
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8" />
            <meta name="viewport" content="width=device-width, initial-scale=1.0" />
            <title>Historial Visitas - Visitante</title>
            <!-- Fuentes de Google -->
            <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Newsreader:wght@500&display=swap" />
            <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Sora:wght@400;700&display=swap" />
            <!-- Bootstrap CSS -->
            <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
            <!--Librerias -->
            <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

            <link rel="stylesheet" href="index.css" />

            <!-- Incluir jQuery -->
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

            <!-- Incluir DataTables JS -->
            <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

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
                .table{
                    background;
                }
        .rectangle-7,
        .vector {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: #E1F5F5;
            color: #fff;
            z-index: -1; /* Asegura que estén detrás del contenido */
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
                    <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ml-auto">
                         <li class="nav-item active">
                             <a class="nav-link" href="http://uabcs.net/pharmatechub/visitante_inicio//index.php" >Volver <span class="sr-only">(current)</span></a>
                         </li>   
                        <li class="nav-item active">
                            <a class="nav-link" href="http://uabcs.net/pharmatechub/Hub/home_p.html" onclick="cerrarSesion()">Cerrar Sesion <span class="sr-only">(current)</span></a>
                        </li>   

                    </ul>
                    </div>
                </nav>

                <div class="container" >
                    <h2>Historial de Visitas</h2>
                    <div style="background-color:white; background-size: cover; background-position: center;">
                        <table class="table" id="table_historial" style="background-color: #FFFFFF; z-index: 25;">
                            <thead>
                                <tr>
                                    <th>Nombre Visitante</th>
                                    <th>Fecha Inicio</th>
                                    <th>Fecha Fin</th>
                                    <th>Departamento</th>
                                    <th>Proyecto</th>
                                    <th>Estado</th>
                                    <th>Motivo Visita</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                 // Itera sobre cada fila devuelta por la consulta
                                while($fila = $resultado->fetch(PDO::FETCH_ASSOC)) {
                                     // Genera una fila de la tabla para cada registro
                                    echo "<tr>
                                        <td>{$fila['nombre_visitante']}</td>
                                        <td>{$fila['f_inicio']}</td>
                                        <td>{$fila['f_fin']}</td>
                                        <td>{$fila['departamento_nombre']}</td>
                                        <td>{$fila['proyecto_nombre']}</td>
                                        <td>{$fila['txt_estado']}</td>
                                        <td>{$fila['motivo_v']}</td>
                                    </tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </body>
        </html>
        <?php
    } else {
        // Si no hay resultados, muestra un mensaje
        echo "<div class='container'><p>No se encontraron registros para el usuario.</p></div>";
    }
} else {
      // Si el nombre de usuario no está definido en la sesión, muestra un mensaje
    echo "<div class='container'><p>Nombre de usuario no está definido en la sesión.</p></div>";
}
// Imprime en la consola del navegador el número de filas devueltas por la consulta
echo "<script>console.log('Número de filas: " . $resultado->rowCount() . "')</script>";
?>

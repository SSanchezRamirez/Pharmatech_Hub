<?php
session_start();

$host = "localhost";
$db_username = "uabcsnet_adminParmatech";
$db_password = "CTln0KN41HJ3";
$dbname = "uabcsnet_ritienet_parmatech";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $db_username, $db_password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("No se pudo conectar a la base de datos: " . $e->getMessage());
}

$error = '';

// Consulta que une las tablas h_registro_visitas e info_drs
$consulta = "SELECT hrv.*, idr.id_doc 
            FROM h_registro_visitas hrv
            INNER JOIN info_drs idr ON hrv.id_doc = idr.id_doc
            WHERE hrv.aprobacion > 4";

// Preparar la consulta
$resultado = $pdo->prepare($consulta);

// Ejecutar la consulta
$resultado->execute();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial Visitas</title>
    <!-- Fuentes de Google -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Newsreader:wght@500&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Sora:wght@400;700&display=swap">
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">

    <style>
        :root {
            --default-font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto,
                Ubuntu, "Helvetica Neue", Helvetica, Arial, "PingFang SC",
                "Hiragino Sans GB", "Microsoft Yahei UI", "Microsoft Yahei",
                "Source Han Sans CN", sans-serif;
        }

        .container {
            z-index: 1;
        }

        input,
        select,
        textarea,
        button {
            outline: 0;
        }

        .container-fluid {
            position: relative;
            width: 100%;
            min-height: 100vh;
        }

        .table {
            background-color: #FFFFFF;
            z-index: 25;
        }
    </style>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#tabla_visitas').DataTable({
                "order": [[0, "desc"]], // Ordenar por defecto por la columna ID Visita en orden descendente
                "language": {
                    "url": "https://cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json"
                }
            });
        });
    </script>

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
                        <a class="nav-link" href="http://uabcs.net/pharmatechub/director_inicio/index.php">Volver <span class="sr-only">(current)</span></a>
                    </li>
                    <li class="nav-item active">
                        <a class="nav-link" href="http://uabcs.net/pharmatechub/Hub/home_p.html" onclick="cerrarSesion()">Cerrar Sesión <span class="sr-only">(current)</span></a>
                    </li>
                </ul>
            </div>
        </nav>




        <div class="container">
            <h2>Visitas finalzadas </h2>
                <div style="
                    background-color:white; 
                    background-size: cover;
                    background-position: center;
                    ">

                    <table class="table" id="tabla_visitas">

                    <thead>
                        <tr>
                            <th>No. Visita</th>
                            <th>Nombre Usuario</th>
                            <th>Fecha Inicio</th>
                            <th>Fecha Fin</th>
                            <th>Motivo Visita</th>
                            <th>Proyecto a desarrollar</th>
                        </tr>
                    </thead
                    >
                    <tbody>
                        <?php
                        if ($resultado->rowCount() > 0) {
                            // Salida de datos de cada fila
                            while ($fila = $resultado->fetch()) {
                                // Obtener el nombre del motivo de la visita
                                $consulta_motivo = "SELECT nombre_motivo FROM motivos_visita WHERE id_motivo = :id_motivo";
                                $resultado_motivo = $pdo->prepare($consulta_motivo);
                                $resultado_motivo->bindParam(':id_motivo', $fila['m_visita'], PDO::PARAM_INT);
                                $resultado_motivo->execute();
                                $fila_motivo = $resultado_motivo->fetch();

                                // Consulta para obtener el nombre del proyecto
                                $consulta_proyecto = "SELECT nombre FROM proyectos WHERE id_proyecto = :id_proyecto";
                                $resultado_proyecto = $pdo->prepare($consulta_proyecto);
                                $resultado_proyecto->bindParam(':id_proyecto', $fila['id_proyecto'], PDO::PARAM_INT);
                                $resultado_proyecto->execute();
                                $fila_proyecto = $resultado_proyecto->fetch();

                                echo "<tr>
                                        <td>{$fila['id_visita']}</td>
                                        <td>{$fila['nombre_usuario']}</td>
                                        <td>{$fila['f_inicio']}</td>
                                        <td>{$fila['f_fin']}</td>
                                        <td>{$fila_motivo['nombre_motivo']}</td> <!-- Mostrar el nombre del motivo -->
                                        <td>{$fila_proyecto['nombre']}</td> <!-- Mostrar el nombre del proyecto -->
     
                                    </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='8'>No se encontraron resultados</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
                    </div>
        </div>
    </div>



</body>
</html>

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

if(isset($_SESSION['username'])) {
    // Consulta que une las tablas h_registro_visitas e info_drs
    $consulta = "SELECT hrv.*, idr.id_doc, iv.nombre, iv.apellido_paterno, iv.apellido_materno 
                FROM h_registro_visitas hrv
                INNER JOIN info_drs idr ON hrv.id_doc = idr.id_doc
                INNER JOIN info_visitantes iv ON hrv.nombre_usuario = iv.nombre_usuario
                WHERE idr.nombre_usuario = :username AND hrv.aprobacion = 1";

    // Preparar la consulta
    $resultado = $pdo->prepare($consulta);

    // Vincular el parámetro :username con el valor de la variable de sesión $_SESSION['username']
    $resultado->bindParam(':username', $_SESSION['username'], PDO::PARAM_STR);

    // Ejecutar la consulta
    $resultado->execute();
} else {
    // Manejar el caso en que la sesión no tenga un nombre de usuario establecido
    $error = 'Nombre de usuario no está definido en la sesión.';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Inicio Doctor</title>
    <!-- Fuentes de Google -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Newsreader:wght@500&display=swap" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Sora:wght@400;700&display=swap" />
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="index.css" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">

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


    </style>
</head>

<body>
            <!-- Fondos de la pagina -->

    <div class="rectangle-7"></div>
    <div class="vector"></div>
    
    <div class="container-fluid">
                <!-- Barra de navegación -->

        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <a class="navbar-brand" href="http://uabcs.net/pharmatechub/Hub/home_p.html">Pharmatech</a>
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

        <!-- Tabla -->

    <div class="container">
    <h2>Aprobacion de cartas </h2>
    <form id="form-aprobacion" method="post">
    <table class="table" bgcolor="#FFFFFF" z-index=25>
            <thead class="text-center">
                
                <tr>
                    <!-- Headers de la tabla -->                
                    <th>Nombre del Visitante</th>
                    <th>Fecha Inicio</th>
                    <th>Fecha Fin</th>
                    <th>Motivo Visita</th>
                    <th>Proyecto a desarrollar</th>
                    <th>Estado aprobación</th>
                    <th>Aprobar</th>
                    <th>Rechazar</th>

                </tr>
            </thead>
            <tbody class="text-center">
                <?php


                    if ($resultado->rowCount() > 0) {
                        // Salida de datos de cada fila
                        while($fila = $resultado->fetch()) {
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

                            // Consulta para obtener la tabla de visitas, mandando a llamr las columnas
                            echo "<tr>
                                    <td>".$fila['nombre']." ".$fila['apellido_paterno']." ".$fila['apellido_materno']."</td>
                                    <td>".$fila['f_inicio']."</td>
                                    <td>".$fila['f_fin']."</td>
                                    <td>".$fila_motivo['nombre_motivo']."</td> <!-- Mostrar el nombre del motivo -->
                                    <td>".$fila_proyecto['nombre']."</td> <!-- Mostrar el nombre del proyecto -->
                                    <td>".($fila['aprobacion'] == 1 ? 'En proceso de confirmación' :
                                            ($fila['aprobacion'] == 2 ? 'Asistencia confirmada por el médico, en espera de aprobación departamental' :
                                            ($fila['aprobacion'] == 3 ? 'Visita finalizada' : $fila['aprobacion'])))."</td>
                                    <td><input type='checkbox' name='aprobacion[]' value='".$fila['id_visita']."'></td>
                                    <td><input type='checkbox' name='rechazo[]' value='".$fila['id_visita']."'></td>
                                    

                                </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='8'>No se encontraron resultados</td></tr>";
                    }
                    ?>
            </tbody>
        </table>

        <button type="submit" name="submit" class="btn btn-primary">Actualizar Aprobaciones</button>
    </form>
</div>
</div>



<!--Librerias -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>


<script>
    //Funcion para manejar las acciones
$(document).ready(function(){
    $('#form-aprobacion').on('submit', function(e){
        e.preventDefault();
        var formData = $(this).serializeArray(); // Cambiado a serializeArray para manejar múltiples valores
        formData.push({ name: 'accion', value: 'actualizar' }); // Agregar un campo de acción para el procesamiento del servidor
        $.ajax({
            type: 'POST',
            url: 'notificacion_act.php', // Asegúrate de que la URL es correcta
            data: formData,
            success: function(response){
                console.log(response);
                location.reload(); // Recargar la página para ver los cambios
            }
        });
    });
});

</script>

</body>
</html>

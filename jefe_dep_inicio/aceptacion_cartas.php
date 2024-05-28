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
$error = ''; // Variable para manejar errores (no parece estar siendo utilizada en este código)
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Inicio Jefe departamental</title>
    <!-- Fuentes de Google -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Newsreader:wght@500&display=swap" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Sora:wght@400;700&display=swap" />
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="index.css" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>

<body>
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
                    <a class="nav-link" href="http://uabcs.net/pharmatechub/Hub/home_p.html" onclick="cerrarSesion()">Cerrar Sesion <span class="sr-only">(current)</span></a>
                </li>   
            </ul>
            </div>
        </nav>
    </div>


    <div class="container">
        <h2>Registro de Visitas</h2>
        <form id="form-aprobacion" method="post">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID Visita</th>
                        <th>Nombre Usuario</th>
                        <th>Fecha Inicio</th>
                        <th>Fecha Fin</th>
                        <th>Motivo Visita</th>
                        <th>ID Departamento</th>
                        <th>ID Proyecto</th>
                        <th>ID Documento</th>
                        <th>Aprobación</th>
                        <th>Motivo</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Aquí comienza la consulta utilizando PDO
                    $consulta = "SELECT * FROM h_registro_visitas WHERE aprobacion = 1";
                    $resultado = $pdo->query($consulta);

                    if ($resultado->rowCount() > 0) {
                        // Salida de datos de cada fila
                        while($fila = $resultado->fetch()) {
                            echo "<tr>
                                    <td>".$fila['id_visita']."</td>
                                    <td>".$fila['nombre_usuario']."</td>
                                    <td>".$fila['f_inicio']."</td>
                                    <td>".$fila['f_fin']."</td>
                                    <td>".$fila['m_visita']."</td>
                                    <td>".$fila['id_departamento']."</td>
                                    <td>".$fila['id_proyecto']."</td>
                                    <td>".$fila['id_doc']."</td>
                                    <td>".$fila['aprobacion']."</td>
                                    <td>".$fila['motivo_v']."</td>
                                    <td><input type='checkbox' name='aprobacion[]' value='".$fila['id_visita']."'></td>
                                </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='11'>No se encontraron resultados</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
            <button type="submit" name="submit" class="btn btn-primary">Actualizar Aprobaciones</button>
        </form>
    </div>
    <!--Librerias -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
     <!-- Script para manejar el envío del formulario de actualización mediante AJAX -->              
    <script>
    $(document).ready(function(){
        $('#form-aprobacion').on('submit', function(e){
            e.preventDefault();
            var formData = $(this).serialize();
            $.ajax({
                type: 'POST',
                url: 'notificacion_act.php',
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

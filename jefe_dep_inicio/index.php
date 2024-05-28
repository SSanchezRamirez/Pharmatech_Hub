<?php
session_start(); // Inicia la sesión

$host = "localhost"; // Host de la base de datos
$db_username = "uabcsnet_adminParmatech"; // Nombre de usuario de la base de datos
$db_password = "CTln0KN41HJ3"; // Contraseña de la base de datos
$dbname = "uabcsnet_ritienet_parmatech"; // Nombre de la base de datos

try {
    // Intenta conectar a la base de datos utilizando PDO (PHP Data Objects)
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $db_username, $db_password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Establece el modo de error a excepción para manejar errores fácilmente
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC // Establece el modo de obtención de resultados predeterminado a asociativo
    ]);
} catch (PDOException $e) {
    die("No se pudo conectar a la base de datos: " . $e->getMessage()); // Si la conexión falla, muestra un mensaje de error y termina la ejecución del script
}
$error = ''; // Variable para almacenar errores (no parece ser utilizada más adelante)
?>



<!DOCTYPE html>
<html lang="es">
<head>
     <!-- Etiquetas meta y título de la página -->
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
      <!-- Estilos adicionales -->
    <style>
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
      <!-- Divs para los fondos -->
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
                    <a class="nav-link" href="http://uabcs.net/pharmatechub/Hub/home_p.html" onclick="cerrarSesion()">Cerrar Sesion <span class="sr-only">(current)</span></a>
                </li>   
            </ul>
            </div>
        </nav>
    </div>


    <!-- Contenido principal -->
    <div class="row">
    <div class="col--4">
         <!-- Botón para gestionar proyectos -->
        <div class="rectangle-1 mb-3">
            <button class="btn btn-primary g_proyectos">Gestionar proyectos</button>
        </div>
    </div>
    <div class="col-md-8">
           <!-- Formulario para mostrar y actualizar solicitudes de cartas de aceptación institucionales -->
        <div class="container">
            <h2>Solicitudes de cartas de aceptación institucionales</h2>
            <form id="form-aprobacion" method="post">
                  <!-- Tabla para mostrar las solicitudes -->
                <table class="table" bgcolor="#FFFFFF" z-index=22>
                    <thead>
                          <!-- Encabezados de la tabla -->
                        <tr>
                            
                            <th>Nombre </th>
                            <th>Fecha Inicio</th>
                            <th>Fecha Fin</th>
                            <th>Departamento</th>
                            <th>Proyecto</th>
                            <th>Doctor</th>
                            <th>Aprobación</th>
                            <th>Motivo</th>
                            <th>Aprobar</th>
                            <th>Rechazar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Consulta principal para obtener las visitas pendientes de aprobación
                        $consulta = "SELECT hv.*, d.nombre AS departamento, p.nombre AS proyecto, 
                                        CONCAT(dr.nombre_s, ' ', dr.apellido_paterno, ' ', dr.apellido_materno) AS doctor,
                                        CONCAT(v.nombre, ' ', v.apellido_paterno, ' ', v.apellido_materno) AS nombre_completo_usuario
                                     FROM h_registro_visitas hv
                                     LEFT JOIN departamentos d ON hv.id_departamento = d.id_departamento
                                     LEFT JOIN proyectos p ON hv.id_proyecto = p.id_proyecto
                                     LEFT JOIN info_drs dr ON hv.id_doc = dr.id_doc
                                     LEFT JOIN info_visitantes v ON hv.nombre_usuario = v.nombre_usuario
                                     WHERE hv.aprobacion = 2";

                        $resultado = $pdo->query($consulta);

                        if ($resultado->rowCount() > 0) {
                            // Salida de datos de cada fila
                            while($fila = $resultado->fetch(PDO::FETCH_ASSOC)) {
                                echo "<tr>
                                      
                                        <td>".$fila['nombre_completo_usuario']."</td>
                                        <td>".$fila['f_inicio']."</td>
                                        <td>".$fila['f_fin']."</td>
                                        <td>".$fila['departamento']."</td>
                                        <td>".$fila['proyecto']."</td>
                                        <td>".$fila['doctor']."</td>
                                        <td>".($fila['aprobacion'] == 1 ? 'En proceso de confirmación del médico' :
                                        ($fila['aprobacion'] == 2 ? 'En espera de aprobación' :
                                        ($fila['aprobacion'] == 3 ? 'Visita finalizada' : $fila['aprobacion'])))."</td>
                                        <td>".$fila['motivo_v']."</td>
                                        <td><input type='checkbox' name='aprobacion[]' value='".$fila['id_visita']."'></td>
                                        <td><input type='checkbox' name='rechazo[]' value='".$fila['id_visita']."'></td>
                                    </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='11'>No se encontraron resultados</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
                     <!-- Botón para actualizar las aprobaciones -->
                <button type="submit" name="submit" class="btn btn-primary">Actualizar Aprobaciones</button>
            </form>
        </div>
    </div>
</div>


<!-- Segunda sección de solicitudes de cartas de terminación -->
<div class="row">
    <div class="col--4">
        <div class="rectangle-1 mb-3">
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="container">
            <h2>Visitas en proceso</h2>
            <table class="table" bgcolor="#FFFFFF" z-index=22>
                <thead>
                    <tr>
                       
                        <th>Nombre Usuario</th>
                        <th>Fecha Inicio</th>
                        <th>Fecha Fin</th>
                        <th>Departamento</th>
                        <th>Proyecto</th>
                        <th>Motivo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Aquí comienza la consulta utilizando PDO
                    $consulta = "SELECT hv.*, 
                                    CONCAT(v.nombre, ' ', v.apellido_paterno, ' ', v.apellido_materno) AS nombre_completo_usuario,
                                    d.nombre AS departamento, 
                                    p.nombre AS proyecto 
                                 FROM h_registro_visitas hv
                                 LEFT JOIN info_visitantes v ON hv.nombre_usuario = v.nombre_usuario
                                 LEFT JOIN departamentos d ON hv.id_departamento = d.id_departamento
                                 LEFT JOIN proyectos p ON hv.id_proyecto = p.id_proyecto
                                 WHERE hv.aprobacion = 3";
                    $resultado = $pdo->query($consulta);

                    if ($resultado->rowCount() > 0) {
                        // Salida de datos de cada fila
                        while($fila = $resultado->fetch(PDO::FETCH_ASSOC)) {
                            echo "<tr>
                                   
                                    <td>".$fila['nombre_completo_usuario']."</td>
                                    <td>".$fila['f_inicio']."</td>
                                    <td>".$fila['f_fin']."</td>
                                    <td>".$fila['departamento']."</td>
                                    <td>".$fila['proyecto']."</td>
                                    <td>".$fila['motivo_v']."</td>
                                </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='9'>No se encontraron resultados</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

    
<div class="row">
    <div class="col--4">
        <div class="rectangle-1 mb-3">
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="container">
            <h2>Solicitudes de cartas de terminación</h2>
               <!-- Formulario para mostrar y actualizar solicitudes de cartas de terminación -->
            <form id="form-aprobacion_2" method="post">
                <table class="table" bgcolor="#FFFFFF" z-index=22>
                    <thead>
                        <tr>
                        
                            <th>Nombre Usuario</th>
                            <th>Fecha Inicio</th>
                            <th>Fecha Fin</th>
                            <th>Departamento</th>
                            <th>Proyecto</th>
                            <th>Aprobación</th>
                            <th>Motivo</th>
                            <th>Aprobar</th>
                            <th>Rechazar</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        // Aquí comienza la consulta utilizando PDO
                        $consulta = "SELECT hv.*, 
                                        CONCAT(v.nombre, ' ', v.apellido_paterno, ' ', v.apellido_materno) AS nombre_completo_usuario,
                                        d.nombre AS departamento, 
                                        p.nombre AS proyecto 
                                     FROM h_registro_visitas hv
                                     LEFT JOIN info_visitantes v ON hv.nombre_usuario = v.nombre_usuario
                                     LEFT JOIN departamentos d ON hv.id_departamento = d.id_departamento
                                     LEFT JOIN proyectos p ON hv.id_proyecto = p.id_proyecto
                                     WHERE hv.aprobacion = 4";
                        $resultado = $pdo->query($consulta);

                        if ($resultado->rowCount() > 0) {
                            // Salida de datos de cada fila
                            while($fila = $resultado->fetch(PDO::FETCH_ASSOC)) {
                                echo "<tr>
                                        
                                        <td>".$fila['nombre_completo_usuario']."</td>
                                        <td>".$fila['f_inicio']."</td>
                                        <td>".$fila['f_fin']."</td>
                                        <td>".$fila['departamento']."</td>
                                        <td>".$fila['proyecto']."</td>
                                        <td>".($fila['aprobacion'] == 1 ? 'En proceso de confirmación del médico' :
                                        ($fila['aprobacion'] == 4 ? 'En espera de aprobación' :
                                        ($fila['aprobacion'] == 5 ? 'Visita finalizada' : $fila['aprobacion'])))."</td>
                                        <td>".$fila['motivo_v']."</td>
                                        <td><input type='checkbox' name='aprobacion[]' value='".$fila['id_visita']."'></td>
                                        <td><input type='checkbox' name='rechazo[]' value='".$fila['id_visita']."'></td>
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
    </div>
</div>



    <!--Librerias -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
                
    <script>
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
        
        $(document).ready(function(){
            $('#form-aprobacion_2').on('submit', function(e){
                e.preventDefault();
                var formData = $(this).serializeArray(); // Cambiado a serializeArray para manejar múltiples valores
                formData.push({ name: 'accion', value: 'actualizar' }); // Agregar un campo de acción para el procesamiento del servidor
                $.ajax({
                    type: 'POST',
                    url: 'notificacion_act_2.php', // Asegúrate de que la URL es correcta
                    data: formData,
                    success: function(response){
                        console.log(response);
                        location.reload(); // Recargar la página para ver los cambios
                    }
                });
            });
        });
    </script>


<!-- Script para redirigir a la página de gestión de proyectos -->
<script>
    $(document).ready(function(){
        $(".g_proyectos").click(function(){
            location.href = "http://uabcs.net/pharmatechub/jefe_dep_inicio/gen_proyectos.php";
        }); 
    });
</script>


    </body>
</html>

<?php
// Inicia la sesión para manejar variables de sesión
session_start();
// Parámetros de conexión a la base de datos
$host = "localhost";
$db_username = "uabcsnet_adminParmatech";
$db_password = "CTln0KN41HJ3";
$dbname = "uabcsnet_ritienet_parmatech";

try {
    // Intenta conectar a la base de datos usando PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $db_username, $db_password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Configura PDO para lanzar excepciones en errores
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC // Establece el modo de recuperación de datos predeterminado a asociativo
    ]);
} catch (PDOException $e) {
      // En caso de error al conectar, muestra un mensaje de error y termina la ejecución del script
    die("No se pudo conectar a la base de datos: " . $e->getMessage());
}
// Consulta SQL para obtener los datos de la visita del usuario actual
$consulta = "SELECT h_registro_visitas.*, departamentos.nombre AS nombre_departamento, 
proyectos.nombre AS nombre_proyecto, motivos_visita.nombre_motivo, 
info_visitantes.nombre AS nombre_visitante, info_visitantes.apellido_paterno AS apellido_paterno_visitante, 
info_visitantes.apellido_materno AS apellido_materno_visitante
FROM h_registro_visitas
INNER JOIN departamentos ON h_registro_visitas.id_departamento = departamentos.id_departamento
INNER JOIN proyectos ON h_registro_visitas.id_proyecto = proyectos.id_proyecto
INNER JOIN motivos_visita ON h_registro_visitas.m_visita = motivos_visita.id_motivo
INNER JOIN info_visitantes ON h_registro_visitas.nombre_usuario = info_visitantes.nombre_usuario
WHERE h_registro_visitas.nombre_usuario = :username AND (h_registro_visitas.aprobacion < 6 OR h_registro_visitas.aprobacion > 10)
ORDER BY id_visita DESC
LIMIT 1 ; ";
// Prepara y ejecuta la consulta SQL, pasando el valor de usuario actual a través de un parámetro
$resultado = $pdo->prepare($consulta);
$resultado->bindParam(':username', $_SESSION['username'], PDO::PARAM_STR);
$resultado->execute();
// Obtiene todas las filas de resultados
$filas = $resultado->fetchAll();
// Variable para almacenar el estado de aprobación
$estado_aprobacion = '';
// Si hay filas de resultados, establece el estado de aprobación
if (count($filas) > 0) {
    $fila = $filas[0];
    $estado_aprobacion = $fila['aprobacion'];
}
// Imprime mensajes de depuración en la consola del navegador
echo "<script>console.log('Estado de aprobación: " . $estado_aprobacion . "')</script>";
echo "<script>console.log('Número de filas: " . $resultado->rowCount() . "')</script>";
// Variable para almacenar mensajes de error
$error = '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Inicio Visitante</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Newsreader:wght@500&display=swap" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Sora:wght@400;700&display=swap" />
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="index.css" />
    <style>
        .btn-custom {
            width: 200px;
            margin-bottom: auto;
            text-align: center;
            padding: auto;
            background-color: #007BFF;
            color: #fff;
        }
    </style>
</head>
<body>
      <!-- Encabezado y navegación -->
    <div class="rectangle-7"></div>
    <div class="vector"></div>

    <div class="container-fluid">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <a class="navbar-brand" href="http://uabcs.net/pharmatechub/Hub/home_p.html">Pharmatechub</a>
            <!-- Botón de colapso para dispositivos móviles -->
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                       <!-- Enlace para cerrar sesión -->
                    <li class="nav-item active">
                        <a class="nav-link" href="http://uabcs.net/pharmatechub/Hub/home_p.html"
                            onclick="cerrarSesion()">Cerrar Sesión <span class="sr-only">(current)</span></a>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
 <!-- Contenido principal -->
    <div class="row">
          <!-- Menú lateral -->
        <div class="col-md-4">
            <div class="mb-3">
                <h2>Menú </h2>
                <!-- Enlace para solicitar una visita -->
                <a href="http://uabcs.net/pharmatechub/visitante_inicio/solicitar_visita/index.php" class="btn btn-custom 
                <?php if ($resultado->rowCount() > 0) echo 'disabled'; ?>">Solicitar visita</a>
                </div>
                  <!-- Enlace para imprimir carta de aceptación -->
            <div class="mb-3">
                <a href="http://uabcs.net/pharmatechub/visitante_inicio/c_aceptacion/index.php" class="btn btn-custom btn-imprimir-carta-aceptacion 
                <?php if ($estado_aprobacion != 2) echo 'disabled'; ?>">Imprimir Carta de Aceptación</a>
            </div>
            <!-- Enlace para imprimir carta institucional -->
            <div class="mb-3">
                <a href="http://uabcs.net/pharmatechub/visitante_inicio/c_aceptacion_ins/index.php" 
                class="btn btn-custom btn-imprimir-carta 
                <?php echo ($estado_aprobacion != 3) ? 'disabled' : ''; ?>">Imprimir Carta Institucional</a>
            </div>
             <!-- Enlace para imprimir credencial -->
            <div class="mb-3">
                <a href="http://uabcs.net/pharmatechub/visitante_inicio/s_credencial/index.php" 
                class="btn btn-custom btn-imprimir-credencial 
                <?php echo ($estado_aprobacion != 3) ? 'disabled' : ''; ?>">Imprimir Credencial</a>
            </div>
            <!-- Enlace para imprimir carta de terminación -->
            <div class="mb-3">
                <a href="http://uabcs.net/pharmatechub/visitante_inicio/c_terminacion/index.php" 
                class="btn btn-custom generar-terminacion <?php echo ($estado_aprobacion != 5) ? 'disabled' : ''; ?>">Imprimir Carta de Terminación</a>
            </div>
            <!-- Enlace para ver historial de visitas -->
            <div class="mb-3">
                <a href="http://uabcs.net/pharmatechub/visitante_inicio/historial_visitas/index.php" 
                class="btn btn-custom h_visitas">Historial de visitas</a>
            </div>
        </div>
 <!-- Área principal con la tabla de proceso de visitas -->
        <div class="col-md-8">
            <div class="container">
                <h2>Proceso visitas</h2>
             <!-- Tabla para mostrar los detalles de las visitas -->
                <table class="table" bgcolor="#FFFFFF" style="width: 120% ;">
                <thead>
                     <!-- Cabecera de la tabla -->
                    <tr class="text-center">
                    <th style="width: 15%;">Nombre del Visitante</th> 
                        <th style="width: 10%;">Fecha Inicio</th>
                        <th style="width: 10%;">Fecha Fin</th>
                        <th style="width: 10%;">Departamento</th>
                        <th style="width: 10%;">Proyecto</th>
                        <th style="width: 10%;">Motivo</th>
                        <th style="width: 10%;">Estado de Aprobación</th>
                        <th style="width:10%;"> </th>
                        <th style="width:20%;">Acciónes</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    <?php
                      // Comprueba si hay filas de visitas para mostrar
                    if (count($filas) > 0) {
                         // Itera sobre cada fila y muestra los detalles de la visita en la tabla
                        foreach ($filas as $fila) {
                            // Obtener el texto del estado de aprobación
                            $estado_aprobacion = $fila['aprobacion'];
                            $estado_texto = '';
                            $consulta_estado = "SELECT txt_estado FROM estados_reg WHERE id = :id";
                            $stmt_estado = $pdo->prepare($consulta_estado);
                            $stmt_estado->bindParam(':id', $estado_aprobacion, PDO::PARAM_INT);
                            $stmt_estado->execute();
                            $estado_resultado = $stmt_estado->fetch(PDO::FETCH_ASSOC);

                            if ($estado_resultado) {
                                $estado_texto = $estado_resultado['txt_estado'];
                            }

                            // Construir el nombre completo del visitante
                            $nombre_completo_visitante = $fila['nombre_visitante'] . " " . $fila['apellido_paterno_visitante'] . " " . $fila['apellido_materno_visitante'];
                         // Imprime una fila en la tabla con los detalles de la visita y botones de acción
                            echo "<tr>
                            <td>" . $nombre_completo_visitante . "</td>
                                <td class=\"text-nowrap\">" . $fila['f_inicio'] . "</td>
                                <td class=\"text-nowrap\">" . $fila['f_fin'] . "</td>
                                <td>" . $fila['nombre_departamento'] . "</td>
                                <td>" . $fila['nombre_proyecto'] . "</td>
                                <td>" . $fila['nombre_motivo'] . "</td>
                                <td>" . $estado_texto . "</td>
                                <td>
                                    <!-- Formulario para cancelar una visita -->
                                    <form method='post' action='eliminar_visita.php' onsubmit='return confirm(\"¿Estás seguro de que quieres eliminar esta visita?\");'>
                                        <input type='hidden' name='id_visita' value='" . $fila['id_visita'] . "'>
                                        <button type='submit' class='btn btn-danger' " . ($estado_aprobacion > 2 ? 'disabled style="background-color: grey;"' : '') . ">Cancelar Visita</button>
                                    </form>
                                </td>
                                <td>
                                    <!-- Formulario para finalizar una visita -->
                                    <form method='post' action='solicitar_finalizacion.php' onsubmit='return confirm(\"¿Estás seguro de que quieres solicitar la finalización de la visita?\");'>
                                        <input type='hidden' name='id_visita' value='" . $fila['id_visita'] . "'>
                                        <button type='submit' class='btn btn-danger' " . ($estado_aprobacion != 3 ? 'disabled style="background-color: grey;"' : '') . ">Finalizar Visita</button>
                                    </form>
                                </td>
                                <td>
                                    <!-- Formulario para archivar una visita -->
                                    <form method='post' action='arch_visita.php'>
                                        <input type='hidden' name='id_visita' value='" . $fila['id_visita'] . "'>
                                        <input type='hidden' name='estado_aprobacion' value='" . $estado_aprobacion . "'>
                                        <button type='submit' class='btn btn-danger' " . ($estado_aprobacion < 5 ? 'disabled style="background-color: grey;"' : '') . ">Archivar Visita</button>
                                    </form>
                                </td>
                            </tr>";
                        }
                    } else {
                        // Si no hay filas de visitas, muestra un mensaje de que no se encontraron resultados
                        echo "<tr><td colspan='10'>No se encontraron resultados</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
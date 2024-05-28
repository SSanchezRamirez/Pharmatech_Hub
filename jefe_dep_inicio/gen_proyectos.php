<?php
session_start(); // Inicia la sesión para mantener la información del usuario entre páginas

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
$error = '';// Variable para almacenar mensajes de error, se inicializa vacía


// Obtener el departamento del usuario autenticado
$nombre_usuario = $_SESSION['username'];
$consulta_departamento = "SELECT dep FROM info_jef_dep WHERE nombre_usuario = :nombre_usuario";
$stmt = $pdo->prepare($consulta_departamento);
$stmt->execute(['nombre_usuario' => $nombre_usuario]);
$departamento = $stmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Pharmatecch</title>
    <!-- Fuentes de Google -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Newsreader:wght@500&display=swap" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Sora:wght@400;700&display=swap" />
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="index.css" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="stylesheet" href="index.css" />
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

<!-- Barra de navegación -->
<div class="container-fluid">
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="http://uabcs.net/pharmatechub/Hub/home_p.html">Pharmatech</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="http://uabcs.net/pharmatechub/jefe_dep_inicio/index.php">Volver</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="http://uabcs.net/pharmatechub/Hub/home_p.html" onclick="cerrarSesion()">Cerrar Sesion <span class="sr-only">(current)</span></a>
                </li>
            </ul>
        </div>
    </nav>
</div>
    <!-- Contenedor principal -->
<div class="container" style="position: relative; padding: 10px;">
  <!-- Título y tabla de registros -->
    <h2 style="margin-bottom: 10px;">Registro de Proyectos</h2>
    <div class="tabla-container" style="background-color: #FFFFFF; border-radius: 20px; overflow: hidden;">
        <table class="table" style="margin-bottom: 0;">
        <thead class="text-center">
                <tr>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Departamento</th>
                    <th>Acciones</th>
                </tr>
            </thead>
             <!-- Encabezados de la tabla -->
            <tbody class="text-center">
                 <!-- Filas de encabezados -->
            <?php
            // Aquí comienza la consulta utilizando PDO para la tabla proyectos con JOIN para obtener el nombre del departamento
            $consulta = "SELECT proyectos.id_proyecto, proyectos.nombre, proyectos.descripcion, departamentos.nombre AS nombre_departamento 
                         FROM proyectos 
                         JOIN departamentos ON proyectos.id_departamento = departamentos.id_departamento
                         WHERE proyectos.id_departamento = :departamento";
            $stmt = $pdo->prepare($consulta);
            $stmt->execute(['departamento' => $departamento]);

            if ($stmt->rowCount() > 0) {
                // Salida de datos de cada fila
                while($fila = $stmt->fetch()) {
                    echo "<tr>
                            <td>".$fila['nombre']."</td>
                            <td>".$fila['descripcion']."</td>
                            <td>".$fila['nombre_departamento']."</td>
                            <td><button onclick='editar(".$fila['id_proyecto'].")' class='btn btn-info'>Editar</button></td> <!-- Botón de edición -->
                            <td><button onclick='confirmarEliminar(".$fila['id_proyecto'].")' class='btn btn-danger'>Eliminar</button></td>
                        </tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No se encontraron resultados</td></tr>";
            }
            ?>
        </tbody>
    </table>
    <!-- Botón para añadir un proyecto -->
    <button onclick="window.location.href='agr_proyecto.php'" class="btn btn-success">Añadir Proyecto</button>
</div>

    <!--Librerias -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
    

    <script>
         // Funciones JavaScript para editar y confirmar la eliminación de proyectos
    function editar(id_proyecto) {
        window.location.href = 'edit_proyectos.php?id=' + id_proyecto;
    }

    function confirmarEliminar(id_proyecto) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "¡No podrás revertir la accion!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'eliminar_proy.php?id=' + id_proyecto;
            }
        })
    }
    </script>
</body>
</html>

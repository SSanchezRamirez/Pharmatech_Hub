<?php
// Iniciar la sesión para mantener la información del usuario
session_start(); 

// Configuración de conexión a la base de datos
$host = "localhost";
$db_username = "uabcsnet_adminParmatech";
$db_password = "CTln0KN41HJ3";
$dbname = "uabcsnet_ritienet_parmatech";

// Intentar establecer la conexión con la base de datos
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $db_username, $db_password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    // En caso de error al conectar, mostrar un mensaje y terminar la ejecución del script
    die("No se pudo conectar a la base de datos: " . $e->getMessage());
}

// Variable para almacenar mensajes de error
$error = '';
?>



<!DOCTYPE html>
<html lang="es">
<head>
    <!-- Metadatos -->
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Gestión de Departamentos</title>
    <!-- Fuentes de Google -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Newsreader:wght@500&display=swap" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Sora:wght@400;700&display=swap" />
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="index.css" />
</head>

<body>
    <!-- Encabezado de la página -->
    <div class="rectangle-7"></div>
    <div class="vector"></div>
    <div class="container-fluid">
        <!-- Barra de navegación -->
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <a class="navbar-brand" href="http://uabcs.net/pharmatechub/Hub/home_p.html">Pharmatechub</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item active">
                        <!-- Enlace para volver -->
                        <a class="nav-link" href="http://uabcs.net/pharmatechub/subdirector_inicio/index.php">Volver<span class="sr-only">(current)</span></a>
                    </li>
                    <li class="nav-item active">
                        <!-- Enlace para cerrar sesión -->
                        <a class="nav-link" href="http://uabcs.net/pharmatechub/Hub/home_p.html" onclick="cerrarSesion()">Cerrar Sesion <span class="sr-only">(current)</span></a>
                    </li>   
                </ul>
            </div>
        </nav>
    </div>

    <!-- Contenido principal de la página -->
    <div class="container mt-5" style="background-color: #FFFF;">
        <h2>Gestión de departamentos</h2>
        <!-- Tabla para mostrar la lista de departamentos -->
        <table class="table">
            <thead>
                <tr>
                    <!-- Encabezados de la tabla -->
                    <th>Nombre del Departamento</th>
                    <th>Descripción</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Consulta para obtener la lista de departamentos desde la base de datos
                $consulta = "SELECT id_departamento, nombre, descripcion FROM departamentos";
                $resultado = $pdo->query($consulta);
                // Verificar si se encontraron resultados
                if ($resultado->rowCount() > 0) {
                    // Iterar sobre cada fila de resultados
                    while ($fila = $resultado->fetch()) {
                        // Mostrar los datos de cada departamento en una fila de la tabla
                        echo "<tr>
                                <td>" . $fila['nombre'] . "</td>
                                <td>" . $fila['descripcion'] . "</td>
                                <td><button onclick='editar(" . $fila['id_departamento'] . ")' class='btn btn-info'>Editar</button></td>
                                <td><button onclick='confirmarEliminar(" . $fila['id_departamento'] . ")' class='btn btn-danger'>Eliminar</button></td>
                            </tr>";
                    }
                } else {
                    // Si no se encontraron resultados, mostrar un mensaje en una fila de la tabla
                    echo "<tr><td colspan='4'>No se encontraron resultados</td></tr>";
                }
                ?>
            </tbody>
        </table>
        <!-- Botón para añadir un nuevo departamento -->
        <button onclick="window.location.href='gen_dep.php'" class="btn btn-success">Añadir Departamento</button>
    </div>

    <!-- Scripts para funcionalidades interactivas -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
                        
    <script>
    // Función para redirigir a la página de edición de un departamento
    function editar(id_departamento) {
        // Construir la URL con el ID del departamento y redirigir a la página de edición
        window.location.href = 'edit_dep.php?id_departamento=' + id_departamento;
    }

    // Función para mostrar un mensaje de confirmación antes de eliminar un departamento
    function confirmarEliminar(id_departamento) {
        // Mostrar un cuadro de diálogo de confirmación utilizando SweetAlert2
        Swal.fire({
            title: '¿Estás seguro?',
            text: "¡No podrás revertir la acción!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar!'
        }).then((result) => {
            // Si se confirma la acción, redirigir a la página de eliminación del departamento
            if (result.isConfirmed) {
                window.location.href = 'eliminar_dep.php?id=' + id_departamento;
            }
        })
    }
</script>

</body>
</html>

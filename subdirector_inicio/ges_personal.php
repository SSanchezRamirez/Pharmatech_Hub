<?php
// Iniciar la sesión para manejar variables de sesión
session_start(); 

// Configuración de la conexión a la base de datos
$host = "localhost";
$db_username = "uabcsnet_adminParmatech";
$db_password = "CTln0KN41HJ3";
$dbname = "uabcsnet_ritienet_parmatech";

try {
    // Intentar establecer una conexión a la base de datos utilizando PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $db_username, $db_password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Configurar el modo de error para lanzar excepciones
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC // Configurar el modo de obtención de resultados predeterminado
    ]);
} catch (PDOException $e) {
    // Capturar cualquier excepción que pueda ocurrir durante la conexión y mostrar un mensaje de error
    die("No se pudo conectar a la base de datos: " . $e->getMessage());
}
$error = ''; // Variable para manejar errores, aunque parece no estar en uso

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Gestión de personal</title>
    <!-- Fuentes de Google -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Newsreader:wght@500&display=swap" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Sora:wght@400;700&display=swap" />
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="index.css" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>

<body>
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
                    <li class="nav-item active">
                        <a class="nav-link" href="http://uabcs.net/pharmatechub/subdirector_inicio/index.php">Volver<span class="sr-only">(current)</span></a>
                    </li>
                    <li class="nav-item active">
                        <a class="nav-link" href="http://uabcs.net/pharmatechub/Hub/home_p.html" onclick="cerrarSesion()">Cerrar Sesion <span class="sr-only">(current)</span></a>
                    </li>   
                </ul>
            </div>
        </nav>
    </div>

    <!-- Contenedor principal -->
    <div class="container mt-5" style="background-color: #FFFF;">
        <h2>Gestión de personal</h2>
        <!-- Tabla para mostrar los datos del personal -->
        <table class="table">
            <thead>
                <tr>
                    <!-- Cabeceras de la tabla -->
                    <th>Nombre</th>
                    <th>Apellido Paterno</th>
                    <th>Apellido Materno</th>
                    <th>Nombre de Usuario</th>
                    <th>Correo</th>
                    <th>Departamento</th>
                    <th>Acciones</th> <!-- Nueva columna para acciones -->
                </tr>
            </thead>
            <tbody>
                <?php
                // Consulta para obtener los datos del personal y sus respectivos departamentos
                $consulta = "SELECT i.*, d.nombre AS nombre_departamento
                    FROM info_drs i
                    LEFT JOIN departamentos d ON i.dep = d.id_departamento";
                $resultado = $pdo->query($consulta);
                // Verificar si hay resultados en la consulta
                if ($resultado->rowCount() > 0) {
                    // Salida de datos de cada fila
                    while($fila = $resultado->fetch()) {
                        // Mostrar los datos del personal en la tabla
                        echo "<tr>
                                <td>".$fila['nombre_s']."</td>
                                <td>".$fila['apellido_paterno']."</td>
                                <td>".$fila['apellido_materno']."</td>
                                <td>".$fila['nombre_usuario']."</td>
                                <td>".$fila['correo']."</td>
                                <td>" . $fila['nombre_departamento'] . "</td>
                                <td><button onclick='editar(".$fila['id_doc'].")' class='btn btn-info'>Editar</button></td> <!-- Botón de edición -->                                        
                                <td><button onclick='confirmarEliminar(".$fila['id_doc'].")' class='btn btn-danger'>Eliminar</button></td> <!-- Botón de eliminación -->
                            </tr>";
                        }
                } else {
                        // Si no hay resultados en la consulta, mostrar un mensaje
                        echo "<tr><td colspan='8'>No se encontraron resultados</td></tr>";
                    }
                ?>
            </tbody>
        </table>
        <!-- Botón para añadir nuevo personal -->
        <button onclick="window.location.href='gen_personal.php'" class="btn btn-success">Añadir Personal</button>
    </div>

    <!-- Librerías -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
                        
    <!-- JavaScript personalizado -->
    <script>
        // Función para redirigir a la página de edición de un empleado
        function editar(id_doc) {
            window.location.href = 'edit_doc.php?id_doc=' + id_doc;
        }

        function confirmarEliminar(id_doc) {
    // Utiliza SweetAlert (Swal) para mostrar un cuadro de diálogo de confirmación
    Swal.fire({
        title: '¿Estás seguro?',
        text: "¡No podrás revertir la acción!",
        icon: 'warning',
        showCancelButton: true, // Muestra un botón de cancelar en el cuadro de diálogo
        confirmButtonColor: '#3085d6', // Color del botón de confirmación
        cancelButtonColor: '#d33', // Color del botón de cancelar
        confirmButtonText: 'Sí, eliminar!' // Texto del botón de confirmación
    }).then((result) => {
        // Se ejecuta después de que el usuario interactúa con el cuadro de diálogo
        if (result.isConfirmed) {
            // Redirige a la página de eliminación de empleado si el usuario confirma la acción
            window.location.href = 'eliminar_personal.php?id=' + id_doc;
        }
    });
    }


</script>
</body>
</html>

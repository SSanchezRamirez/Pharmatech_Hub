<?php
session_start(); // Iniciar una sesión de PHP

// Configuración de conexión a la base de datos
$host = "localhost";
$db_username = "uabcsnet_adminParmatech";
$db_password = "CTln0KN41HJ3";
$dbname = "uabcsnet_ritienet_parmatech";

// Intenta establecer una conexión a la base de datos usando PDO
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $db_username, $db_password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Establece el modo de error a excepciones
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC // Establece el modo de obtención de datos a asociativo
    ]);
} catch (PDOException $e) {
    die("No se pudo conectar a la base de datos: " . $e->getMessage()); // Termina el script si la conexión falla
}

$var_usuario = $_SESSION['username']; // Obtiene el nombre de usuario de la sesión
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitud de Visita</title>
    <!-- Incluir Bootstrap CSS para estilos responsivos y modernos -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Incluir SweetAlert2 para alertas atractivas -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <!-- Incluir Prettify para resaltar código, si es necesario -->
    <script src="https://google-code-prettify.googlecode.com/svn/loader/run_prettify.js"></script>
</head>

<style>
    /* Estilos para los inputs inválidos */
    input.invalid {
        border-color: red;
    }
    /* Limitar el ancho máximo del contenedor principal */
    .container {
        max-width: 50%;
    }

    /* Estilos para el fondo y los vectores */
    .rectangle-7 {
        position: absolute;
        width: 100%;
        height: 100vh;
        top: 0;
        left: 0;
        background: url(http://uabcs.net/pharmatechub/recursos/assets/images/fondo.png) no-repeat center center;
        background-size: cover;
        border-radius: 70px;
        z-index: -2;
    }
    .vector {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: url(http://uabcs.net/pharmatechub/recursos/assets/images/vector.png);
        background-repeat: no-repeat;
        background-size: cover;
        background-attachment: fixed;
        color: #ffffff;
        z-index: -1;
    }

    /* Estilos para el fondo del cuerpo */
    body {
        background-image: url(http://uabcs.net/pharmatechub/recursos/assets/images/fondo.png);
        background-repeat: no-repeat;
        background-size: cover;
        background-attachment: fixed;
        color: #000000;
    }

    /* Estilos para el contenedor principal */
    .container {
        margin-top: 5rem;
        background-color: #FFFFFF;
        border-radius: 20px;
        padding: 20px;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
    }
</style>

<body>
<div class="vector"></div>
<div class="container-fluid">
    <!-- Navegación principal -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="http://uabcs.net/pharmatechub/Hub/home_p.html">Pharmatechub</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="http://uabcs.net/pharmatechub/visitante_inicio//index.php"
                       onclick="cerrarSesion()">Volver <span class="sr-only">(current)</span></a>
                </li>
            </ul>
        </div>
    </nav>
</div>

<!-- Formulario de solicitud de visita -->
<div class="container mt-5">
    <h2>Formulario de solicitud de visita</h2>
    <form action="proceso_registro_v.php" method="post" enctype="multipart/form-data">
        <!-- Campo para la fecha de inicio -->
        <div class="form-group">
            <label for="f_inicio">Fecha de inicio:</label>
            <input type="date" class="form-control" id="f_inicio" name="f_inicio" required>
        </div>

        <!-- Campo para la fecha de fin -->
        <div class="form-group">
            <label for="f_fin">Fecha de fin:</label>
            <input type="date" class="form-control" id="f_fin" name="f_fin" required>
        </div>

        <!-- Campo para seleccionar el motivo de la visita -->
        <div class="form-group">
            <label for="m_visita">Motivo de la visita:</label>
            <select class="form-control" id="m_visita" name="m_visita" required>
                <?php
                // Obtener datos de la tabla motivos_visita
                $query = $pdo->query("SELECT * FROM motivos_visita");
                while ($row = $query->fetch()) {
                    echo "<option value='" . $row['id_motivo'] . "'>" . $row['nombre_motivo'] . "</option>";
                }
                ?>
            </select>
        </div>

        <!-- Campo para seleccionar el departamento -->
        <div class="form-group">
            <label for="id_departamento">Departamento:</label>
            <select class="form-control" id="id_departamento" name="id_departamento" onchange="cargarProyectos(); cargarDoctores();" required>
                <option value="" disabled selected>Seleccione un departamento</option>
                <?php
                try {
                    // Preparar la consulta SQL para obtener todos los departamentos
                    $stmt = $pdo->prepare("SELECT id_departamento, nombre FROM departamentos");
                    $stmt->execute();
                    // Obtener todas las filas como un array asociativo
                    $departamentos = $stmt->fetchAll();
                    // Iterar sobre los resultados y crear una opción de select para cada uno
                    foreach ($departamentos as $departamento) {
                        echo '<option value="' . $departamento['id_departamento'] . '">' . $departamento['nombre'] . '</option>';
                    }
                } catch (PDOException $e) {
                    die("Error al realizar la consulta: " . $e->getMessage());
                }
                ?>
            </select>
        </div>   
        
        <!-- Campo para seleccionar el proyecto -->
        <div class="form-group">
            <label for="id_proyecto">Proyecto:</label>
            <select class="form-control" id="id_proyecto" name="id_proyecto" required>
                <option value="" disabled selected>Seleccione un departamento</option>
            </select>
        </div>

        <!-- Campo para seleccionar el doctor -->
        <div class="form-group">
            <label for="id_doc">Doctor:</label>
            <select class="form-control" id="id_doc" name="id_doc" required>
                <option value="" disabled selected>Seleccione un departamento</option>
            </select>
        </div>  

        <!-- Campo para subir una imagen en formato JPG -->
        <div class="form-group">
            <label for="imagen" class="form-label">Imagen en jpg:</label>
            <input type="file" class="form-control form-control-md" id="imagen" name="imagen" accept="image/*" required>
        </div>
     
        <!-- Botón para enviar el formulario -->
        <div class="form-group">
            <button type="submit" class="btn btn-primary">Registrar</button>
        </div>         
    </form>
</div>

<script>
    // Función para cargar proyectos en función del departamento seleccionado
    function cargarProyectos() {
        var departamentoSelect = document.getElementById('id_departamento');
        var departamentoId = departamentoSelect.value;
        var proyectoSelect = document.getElementById('id_proyecto');

        // Realizar una llamada AJAX a rec_proyectos.php
        fetch('rec_proyectos.php?departamento_id=' + departamentoId)
            .then(response => response.json())
            .then(data => {
                // Actualizar las opciones del select de proyectos
                proyectoSelect.innerHTML = '';
                // Añadir la opción por defecto que pide seleccionar un proyecto
                proyectoSelect.innerHTML = '<option value="" disabled selected>Seleccione un proyecto</option>';
                data.forEach(proyecto => {
                    var option = document.createElement('option');
                    option.value = proyecto.id_proyecto;
                    option.textContent = proyecto.nombre;
                    proyectoSelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error:', error));
    }

    // Función para cargar doctores en función del departamento seleccionado
    function cargarDoctores() {
        var departamentoSelect = document.getElementById('id_departamento');
        var departamentoId = departamentoSelect.value;
        var doctorSelect = document.getElementById('id_doc');

        // Realizar una llamada AJAX a rec_doctores.php
        fetch('rec_doctores.php?departamento_id=' + departamentoId)
            .then(response => response.json())
            .then(data => {
                // Actualizar las opciones del select de doctores
                doctorSelect.innerHTML = '<option value="" disabled selected>Seleccione un doctor</option>';
                data.forEach(doctor => {
                    var option = document.createElement('option');
                    option.value = doctor.id_doc;
                    option.textContent = doctor.nombre_completo; // 
                    doctorSelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error:', error));
    }
</script>
</body>
</html>

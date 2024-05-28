<?php
// Iniciar sesión
session_start();

// Configuración de conexión a la base de datos
$host = "localhost";
$db_username = "uabcsnet_adminParmatech";
$db_password = "CTln0KN41HJ3";
$dbname = "uabcsnet_ritienet_parmatech";

try {
    // Crear una instancia de PDO para la conexión a la base de datos
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $db_username, $db_password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    // Si hay un error de conexión, mostrar el mensaje y detener la ejecución
    die("No se pudo conectar a la base de datos: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Registro</title>
    <!-- Incluir Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <!-- Fuentes de Google -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Newsreader:wght@500&display=swap" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Sora:wght@400;700&display=swap" />
    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="index.css" />
</head>

<style>
    input.invalid {
        border-color: red;
    }
    .container {
        max-width: 50%;
    }
</style>

<body>
    
  <!-- Div para el rectángulo de fondo -->
  <div class="rectangle-7"></div>
    <!-- Div para el vector de fondo -->
    <div class="vector"></div>

    <!-- Barra de navegación -->
    <div class="container-fluid">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <!-- Enlace al inicio de Pharmatechub -->
            <a class="navbar-brand" href="http://uabcs.net/pharmatechub/Hub/home_p.html">Pharmatechub</a>
            <!-- Botón para mostrar el menú de navegación en dispositivos pequeños -->
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <!-- Menú de navegación -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <!-- Enlace para volver a la página de gestión de departamentos -->
                    <li class="nav-item active">
                        <a class="nav-link" href="http://uabcs.net/pharmatechub/subdirector_inicio/ges_dep.php">Volver<span class="sr-only">(current)</span></a>
                    </li>
                    <!-- Enlace para cerrar sesión -->
                    <li class="nav-item active">
                        <a class="nav-link" href="http://uabcs.net/pharmatechub/Hub/home_p.html" onclick="cerrarSesion()">Cerrar Sesión <span class="sr-only">(current)</span></a>
                    </li>
                </ul>
            </div>
        </nav>
    </div>

    <div class="container mt-5" style="background-color: #FFFF;">
        <h2>Añadir Departamento</h2>
        <form action="agg_dep.php" method="post" enctype="multipart/form-data">
            <!-- Campo Nombre -->
            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" class="form-control" required>
            </div>
            <!-- Campo Descripción -->
            <div class="form-group">
                <label for="descripcion">Descripción:</label>
                <textarea id="descripcion" name="descripcion" class="form-control"></textarea>
            </div>
            <!-- Datos del Jefe de Departamento -->
            <h4>Datos del Jefe de Departamento</h4>
            <!-- Campo Nombre(s) -->
            <div class="form-group">
                <label for="nombre_s">Nombre(s)</label>
                <input type="text" class="form-control" id="nombre_s" name="nombre_s" maxlength="50" required>
            </div>
            <!-- Campo Apellido Paterno -->
            <div class="form-group">
                <label for="apellido_paterno">Apellido Paterno</label>
                <input type="text" class="form-control" id="apellido_paterno" name="apellido_paterno" maxlength="50" required>
            </div>
            <!-- Campo Apellido Materno -->
            <div class="form-group">
                <label for="apellido_materno">Apellido Materno</label>
                <input type="text" class="form-control" id="apellido_materno" name="apellido_materno" maxlength="50" required>
            </div>
            <!-- Campo Nombre de Usuario -->
            <div class="form-group">
                <label for="nombre_usuario">Nombre de Usuario</label>
                <input type="text" class="form-control" id="nombre_usuario" name="nombre_usuario" maxlength="50" required>
            </div>
            <!-- Campo Contraseña -->
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" class="form-control" id="password" name="password" maxlength="50"
                    pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="La contraseña debe tener al menos 8 caracteres y contener al menos un número, una letra mayúscula y una letra minúscula." required oninput="validatePassword()">
                <p id="passwordInvalidMessage" style="color: red; display: none;">La contraseña debe tener al menos 8 caracteres y contener al menos un número, una letra mayúscula y una letra minúscula.</p>
            </div>
            <!-- Campo para subir la imagen de la firma -->
            <div class="form-group">
                <label for="firma" class="form-label">Añadir Imagen de Firma:</label>
                <input type="file" class="form-control form-control-md" id="firma" name="firma" accept="image/*" required>
            </div>

            <!-- Botón de guardar -->
            <button type="submit" class="btn btn-primary">Guardar</button>
        </form>
    </div>

    <!-- Incluir Bootstrap JS y dependencias -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
    <!-- Script para validar la contraseña -->
    <script

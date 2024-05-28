<?php
session_start();

// Configuración de conexión a la base de datos
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

$var_usuario = $_SESSION['username'];

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
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Inicio Subdirector</title>
    <!-- Fuentes de Google -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Newsreader:wght@500&display=swap" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Sora:wght@400;700&display=swap" />
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="index.css" />
</head>

<style>
    input.invalid {
        border-color: red;
    }
    .container{
        max-width: 50%;
    }
</style>

<body>
        <!-- Imagenes de fondo -->

    <div class="rectangle-7"></div>
    <div class="vector"></div>
    <div class="container-fluid">
        <!-- Barra de navegacion -->
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <a class="navbar-brand" href="http://uabcs.net/pharmatechub/Hub/home_p.html">Pharmatechub</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item active">
                        <a class="nav-link" href="http://uabcs.net/pharmatechub/subdirector_inicio/ges_personal.php">Volver<span class="sr-only">(current)</span></a>
                    </li>
                    <li class="nav-item active">
                        <a class="nav-link" href="http://uabcs.net/pharmatechub/Hub/home_p.html" onclick="cerrarSesion()">Cerrar Sesion <span class="sr-only">(current)</span></a>
                    </li>
                </ul>
            </div>
        </nav>
    </div>

    <!-- Formulario -->
    <div class="container mt-5" style="background-color: #FFFF;">
        <h2>Formulario de Registro De Personal</h2>
            <!-- Campos -->
        <form action="agg_personal.php" method="post"  enctype="multipart/form-data" onsubmit="return validateForm()">
            <div class="form-group">
                <label for="nombre_s">Nombre(s)</label>
                <input type="text" class="form-control" id="nombre_s" name="nombre_s" maxlength="50">
            </div>
            <div class="form-group">
                <label for="apellido_paterno">Apellido Paterno</label>
                <input type="text" class="form-control" id="apellido_paterno" name="apellido_paterno" maxlength="50">
            </div>
            <div class="form-group">
                <label for="apellido_materno">Apellido Materno</label>
                <input type="text" class="form-control" id="apellido_materno" name="apellido_materno" maxlength="50">
            </div>
            <div class="form-group">
                <label for="nombre_usuario">Nombre de Usuario</label>
                <input type="text" class="form-control" id="nombre_usuario" name="nombre_usuario" maxlength="50">
            </div>
            <div class="form-group">
                <label for="correo">Correo Electrónico</label>
                <input type="email" class="form-control" id="correo" name="correo" maxlength="50">
            </div>
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" class="form-control" id="password" name="password" maxlength="50"
                    pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="La contraseña debe tener al menos 8 caracteres y 
                    contener al menos un número, una letra mayúscula y una letra minúscula." oninput="validatePassword()">
                <p id="passwordInvalidMessage" style="color: red; display: none;">La contraseña debe tener al menos 8 caracteres y contener al menos un número, una letra mayúscula y una letra minúscula.</p>
            </div>
            <div class="form-group">
                <label for="dept">Departamento</label>
                <select class="form-control" id="dept" name="dept">
                    <option value="" disabled selected>Seleccione un departamento</option>
                    <?php
                        // Obtener datos de la tabla para ponerlos en las opcinoes del select
                        $query = $pdo->query("SELECT * FROM departamentos");
                        while ($row = $query->fetch()) {
                            echo "<option value='" . $row['id_departamento'] . "'>" . $row['nombre'] . "</option>";
                        }                    
                    ?>
                </select>
            </div>
            <!-- Campo para la imagen -->
            <div class="form-group">
                <label for="firma" class="form-label">Firma:</label>
                <input type="file" class="form-control form-control-md" id="firma" name="firma" accept="image/*" required>
            </div>
            <!-- Boton con metodo para subir -->
            <button type="submit" class="btn btn-primary">Registrar</button>
        </form>
    </div>

    <!-- Incluir Bootstrap JS y dependencias -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
    <!-- Incluir Bootstrap JS y dependencias -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
    <script>
        //Scrip para saber si todos los campos fueron llenados
        function validateForm() {
            var inputs = document.getElementsByTagName("input");
            var isValid = true;
            for (var i = 0; i < inputs.length; i++) {
                if (inputs[i].value.trim() === "") {
                    inputs[i].classList.add('invalid');
                    isValid = false;
                }
            }
            return isValid;
        }
        
        //Scrip para saber si La contraseña cumple los parametros establecidod        
        function validatePassword() {
            var passwordInput = document.getElementById('password');
            var invalidMessage = document.getElementById('passwordInvalidMessage');
            var isValid = passwordInput.checkValidity();
            if (isValid) {
                passwordInput.classList.remove('invalid');
                invalidMessage.style.display = 'none'; // Ocultar el mensaje de contraseña inválida
            } else {
                passwordInput.classList.add('invalid');
                invalidMessage.style.display = 'block'; // Mostrar el mensaje de contraseña inválida
            }
        }
    </script>
</body>
</html>


<?php
// Inicio de sesión para mantener la sesión activa
session_start();

// Configuración de conexión a la base de datos
$host = "localhost";
$db_username = "uabcsnet_adminParmatech";
$db_password = "CTln0KN41HJ3";
$dbname = "uabcsnet_ritienet_parmatech";

try {
      // Intento de conexión a la base de datos usando PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $db_username, $db_password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
       // Si hay un error en la conexión, muestra un mensaje de error y termina el script
    die("No se pudo conectar a la base de datos: " . $e->getMessage());
}
// Recuperación del nombre de usuario de la sesión PHP
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
 <!-- Estilos personalizados -->
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
        .formulario-fondo {
            background-color: #FFFFFF;
            border-radius: 20px; 
            padding: 20px; /* Espaciado interno */
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1); /* Sombra */
        }

        .formulario-fondo h2 {
            margin-bottom: 20px; /* Espaciado inferior para el título */
        }
    </style>
</head>

<body>
    <div class="rectangle-7"></div> 
    <div class="vector"></div>
    
    <div class="container-fluid">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <a class="navbar-brand" href="http://uabcs.net/pharmatechub/Hub/home_p.html">Pharmatechub</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item active">
                        <a class="nav-link" href="http://uabcs.net/pharmatechub/Hub/home_p.html"    
                            onclick="cerrarSesion()">Volver <span class="sr-only">(current)</span></a>
                    </li>
                </ul>
            </div>
        </nav>
    </div>

    <!-- Nuevo div para el fondo azul -->
        <!-- Contenedor del formulario -->
    <div class="container mt-5">
        <div class="formulario-fondo">
              <!-- Título del formulario -->
            <h2>Formulario de Registro</h2>
              <!-- Formulario de registro -->
            <form enctype="multipart/form-data" action="procesar_registro.php" method="post" onsubmit="return validateForm()">
                <div class="form-group">
                    <label for="nombre">Nombre(s)</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" maxlength="255">
                </div>
                <div class="form-group">
                    <label for="apellido_paterno">Apellido Paterno</label>
                    <input type="text" class="form-control" id="apellido_paterno" name="apellido_paterno" maxlength="255">
                </div>
                <div class="form-group">
                    <label for="apellido_materno">Apellido Materno</label>
                    <input type="text" class="form-control" id="apellido_materno" name="apellido_materno" maxlength="255">
                </div>
                <div class="form-group">
                    <label for="nombre_usuario">Nombre de Usuario</label>
                    <input type="text" class="form-control" id="nombre_usuario" name="nombre_usuario" maxlength="100">
                </div>
                <div class="form-group">
                    <label for="sexo">Sexo</label>
                    <select class="form-control" id="sexo" name="sexo">
                        <option value="Femenino">Seleccionar</option>
                        <option value="Prefiero no decirlo">Prefiero no decirlo</option>
                        <option value="Masculino">Masculino</option>
                        <option value="Femenino">Femenino</option>
                    </select>
                </div>            

                <div class="form-group">
                    <label for="nacionalidad">Nacionalidad</label>
                    <select class="form-control" id="pas" name="pais" maxlength="255">
                        <option value="" disabled selected>Seleccione un pais</option>

                        <?php

                            // Obtener datos de la tabla nacionalidad
                            $query = $pdo->query("SELECT * FROM nacionalidad");
                            while ($row = $query->fetch()) {
                                echo "<option value='" . $row['PAIS_NAC'] . "'>" . $row['PAIS_NAC'] . "</option>";
                            }                    
                        ?>
                    </select>
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
                    <label for="institucion">Institución</label>
                    <input type="text" class="form-control" id="institucion" name="institucion" maxlength="128">
                </div>
            <div class="form-group">
                <label for="num_control">Número de Control</label>
                <input type="text" class="form-control" id="num_control" name="num_control" maxlength="10">
            </div>
            <div class="form-group">
                <label for="fecha_nacimiento">Fecha de Nacimiento</label>
                <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento">
            </div>
            <div class="form-group">
    <label for="firma" class="form-label">Firma en jpg:</label>
    <input type="file" class="form-control form-control-md" id="firma" name="firma" accept="image/*" required>
</div>

            <!--<button type="submit" class="btn btn-primary">Registrar</button>-->
            <button type="submit" class="btn btn-primary">Registrar</button>
        </form>
    </div>
    
    <!-- Incluir Bootstrap JS y dependencias -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
    
    <script>
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
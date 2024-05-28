<?php
session_destroy();// Destruye cualquier sesión existente y comienza una nueva sesión
session_start();// Inicia o reanuda una sesión

// Configuración de conexión a la base de datos
$host = "localhost";
$db_username = "uabcsnet_adminParmatech";
$db_password = "CTln0KN41HJ3";
$dbname = "uabcsnet_ritienet_parmatech";

try {
    // Intenta establecer una conexión a la base de datos utilizando PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $db_username, $db_password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
      // Si hay un error al conectarse a la base de datos, muestra un mensaje de error y finaliza el script
    die("No se pudo conectar a la base de datos: " . $e->getMessage());
}
// Variable para almacenar mensajes de error
$error = '';
// Verifica si se envió el formulario de inicio de sesión (POST request)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Verifica que se hayan proporcionado ambos campos
    if (!empty($username) && !empty($password)) {
         // Consulta la base de datos para obtener los detalles del usuario basados en el nombre de usuario proporcionado
        $sql = "SELECT * FROM users WHERE username = :username";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();
         // Verifica si se encontró un usuario y si la contraseña coincide con el hash almacenado
        if ($user && password_verify($password, $user['password'])) {
             // Si las credenciales son válidas, inicia una sesión para el usuario
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['nivel'] = $user['nivel'];
// Genera un token de sesión basado en el ID del usuario, el nombre de usuario y el nivel de acceso
            $token = hash('sha256',$user['id'].$user['username'].$user['nivel']);
             // Redirige al usuario a la página correspondiente según su nivel de acceso
            switch ($_SESSION['nivel']) {
                case 0: // Visitante
                    header("Location: ../visitante_inicio/index.php?token=" . urlencode($token));
                    break;
                case 1: // Doctor
                    header("Location: ../doctores_inicio/index.php?token=" . urlencode($token));
                    break;
                case 2: // Jefe del departamento
                    header("Location: ../jefe_dep_inicio/index.php?token=" . urlencode($token));
                    break;
                case 3: // Subdirector
                    header("Location: ../subdirector_inicio/index.php?token=" . urlencode($token));
                    break;
                case 4: // Director
                    header("Location: ../director_inicio/index.php?token=" . urlencode($token));
                    break;
                default:
                    // Si el nivel no está definido, redirigir a una página de inicio genérica
                    header("Location: ../Hub/home_p.html");
                    break;
            }
            exit;
        } else {
            $error = "Usuario o contraseña incorrectos.";
        }
    } else {
        $error = "Por favor complete ambos campos.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de sesión</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Sora:wght@400;700&display=swap" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Newsreader:wght@500&display=swap" />
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="index.css" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* Estilo personalizado para el fondo de la alerta */
        .swal2-popup {
            background-color: #FFC0CB;            
        }
    </style>
</head>

<body>
    <!-- Navegación de la aplicación -->
<div class="container-fluid">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <a class="navbar-brand" href="http://uabcs.net/pharmatechub/Hub/home_p.html">Pharmatech</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item active">
                       <!-- Enlace para volver a la página principal -->
                    <a class="nav-link" href="http://uabcs.net/pharmatechub/Hub/home_p.html" onclick="cerrarSesion()">Volver <span class="sr-only">(current)</span></a>
                </li>   
            </ul>
            </div>
        </nav>
    
<div class="main-container">

    <div class="flex-row">
        <button class="rectangle-button">
            <span class="inicio-sesion-span">Inicio de Sesión</span>
        </button>
    </div>
    <div class="rectangle-div">
         <!-- Muestra un mensaje de error si existe -->
        <?php if (!empty($error)): ?>
            <div>
                <script>
                     // Sobrescribe la función de alerta por defecto con una función vacía para desactivarla
                     window.alert = function () { };
                    // Use SweetAlert to display an alert
                    Swal.fire({
                        title: 'Datos Incorrectos',
                        text: 'Revisa los datos',
                        icon: 'error',
                        confirmButtonText: 'Reintentar',
                       width: 5000,                       
                    });               
                </script>            
            </div>      
        <?php endif; ?>

        <form method="POST" action="">
            <span class="usuario-span">Usuario</span>
            <input type="text" id="username" name="username" class="form-control" required style="width: 300px; margin: 0 auto; display: block;">
            <span class="password">Contraseña</span>
            <input type="password" id="password" name="password" class="form-control" required style="width: 300px; margin: 0 auto; display: block;">
            <button class="button" type="submit"><span class="access">Acceder</span></button>
        </form>

        <span class="no-account" onmouseover="this.style.color='red'" onmouseout="this.style.color='black'"onclick="olvidar_c()" style="cursor: pointer;">
            ¿Olvidaste tu contraseña?
        </span>
        
        <span class="no-account" onmouseover="this.style.color='red'" onmouseout="this.style.color='black'" onclick="no_cuenta()" style="cursor: pointer;">
            ¿No tienes una cuenta?
        </span>

    </div>
        <div class="untitled-design"></div>
    </div>



<script>
    // Muestra una alerta al usuario con un mensaje indicando que debe enviar un correo electrónico a una dirección específica para continuar con su caso
    function olvidar_c() {
        window.alert("Manda un correo a esta cuenta para seguir tu caso: pharmatech.rec@pharmatech.com");
    }
</script>



<script>
    // Muestra una alerta al usuario indicando que se va a redirigir a la página de registro
    function no_cuenta() {
        window.alert("Redirigiendo a registro");
        window.location.href = "http://uabcs.net/pharmatechub/registro_visitantes/index.php"; // Adde URL paginam ad quam redirecionari vis
    }
</script>

</body>
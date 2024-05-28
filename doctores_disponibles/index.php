<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Doctores Disponibles</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>

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
      width: 100%;
      height: 100vh;
      top: 0;
      left: 0;
      background: url(http://uabcs.net/pharmatechub/recursos/assets/images/vector.png);
      background-size: cover;
      border-radius: 70px;
      z-index: -1;
    }
  </style>
</head>
<body>
    <!-- Barra de navegacion -->

  <div class="rectangle-7"></div>
  <div class="vector"></div>
  <div class="container-fluid">
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
      <a class="navbar-brand" href="http://uabcs.net/pharmatechub/Hub/home_p.html">Pharmatechub</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
          <li class="nav-item active">
            <a class="nav-link" href="http://uabcs.net/pharmatechub/Hub/home_p.html" onclick="cerrarSesion()">Volver <span class="sr-only">(current)</span></a>
          </li>
        </ul>
      </div>
    </nav>
  </div>

    <!-- Contenedor donde estaran las cards -->

  <div class="container">
    <div class="container mt-5 bg-white" style="border: 1px solid #CCCCCC; border-radius: 25px;">
      <h1>Doctores disponibles</h1>
      <div class="departamentos-fondo">
        <div class="row">
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

          // Consulta SQL para obtener los datos de la tabla info_drs junto con el nombre del departamento
          $consulta_doctores = "
            SELECT info_drs.*, departamentos.nombre AS nombre_departamento 
            FROM info_drs 
            LEFT JOIN departamentos ON info_drs.dep = departamentos.id_departamento
          ";

          // Ejecutar la consulta
          $resultado_doctores = $pdo->query($consulta_doctores);

          // Recorrer los resultados y mostrarlos en tarjetas
          foreach ($resultado_doctores as $fila) {
              echo '<div class="col-md-4 mb-4">';
              echo '<div class="card bg-image" style="background-color: #FFFFF;">';
              echo '<div class="card-body">';
              echo '<h5 class="card-title">' . htmlspecialchars($fila['nombre_s']) . ' ' . htmlspecialchars($fila['apellido_paterno']) . ' ' . htmlspecialchars($fila['apellido_materno']) . '</h5>';
              echo '<p class="card-text">' . htmlspecialchars($fila['correo']) . '</p>';
              echo '<p class="card-text"> ' . htmlspecialchars($fila['nombre_departamento']) . '</p>';
              echo '</div>';
              echo '</div>';
              echo '</div>';
          }

          // Cerrar la conexión
          $pdo = null;
          ?>
        </div>
      </div>
    </div>
  </div>

  <!-- Scripts de Bootstrap -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-46Nbi63vSyz4ZxEh7d5VWmGqyQqVvceNp0zaZL/CXUJdncv1CEUHhuPFR1m+Zv9B" crossorigin="anonymous"></script>
</body>
</html>

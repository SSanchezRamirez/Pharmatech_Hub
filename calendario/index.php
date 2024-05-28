<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Calendario 2024</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <style>
    .month-box {
      border: 1px solid #ccc;
      padding: 10px;
      margin-bottom: 20px;
    }
    .month-name {
      font-weight: bold;
      text-align: center;
    }
    .day-box {
      border: 1px solid #ccc;
      padding: 5px;
      margin: 2px;
      display: inline-block;
      text-align: center;
      cursor: pointer;
    }
    .selected {
      background-color: #007bff;
      color: white;
    }
    .day-name {
      font-weight: bold;
      text-align: center;
    }
    .rectangle-7,
    .vector {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: url(http://uabcs.net/pharmatechub/recursos/assets/images/vector.png) no-repeat;
      background-color: #E1F5F5;
      z-index: -1; /* Asegura que estén detrás del contenido */
    }
    .calendario-fondo {
      background-color: #FFFFFF;
      border-radius: 20px; 
      padding: 20px; /* Espaciado interno */
      box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1); /* Sombra */
      z-index: 0; /* Asegura que esté delante de los fondos */
      position: relative;
    }
  </style>
</head>
<body>
  <div class="rectangle-7"></div>
  <div class="vector"></div>
  <div class="container-fluid">
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
      <a class="navbar-brand" href="http://uabcs.net/pharmatechub/Hub/home_p.html">Pharmatech</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
          <li class="nav-item active">
            <a class="nav-link" href="http://uabcs.net/pharmatechub/Hub/home_p.html">Volver <span class="sr-only">(current)</span></a>
          </li>   
        </ul>
      </div>
    </nav>
  </div>
  <div class="container mt-5">
    <div class="calendario-fondo">
      <h2>Calendario 2024</h2>
      <div class="row">
        <?php
        $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        $dias_mes = [31, 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
        $dias_semana = ['L', 'M', 'Mi', 'J', 'V', 'S', 'D'];

        $conexion = new mysqli('localhost', 'uabcsnet_adminParmatech', 'CTln0KN41HJ3', 'uabcsnet_ritienet_parmatech');
        if ($conexion->connect_error) {
          die("Conexión fallida: " . $conexion->connect_error);
        }

        $festivos = [];
        $resultado = $conexion->query("SELECT * FROM dias_festivos");
        while ($fila = $resultado->fetch_assoc()) {
          $festivos[] = $fila['fecha'];
        }
        $conexion->close();

        foreach ($meses as $index => $nombre_mes) {
          echo "<div class='col-md-3 month-box'>";
          echo "<div class='month-name'>$nombre_mes</div>";
          echo "<div class='row'>";
          foreach ($dias_semana as $nombre_dia) {
            echo "<div class='col day-name'>$nombre_dia</div>";
          }
          echo "</div>";
          $primer_dia_semana = date('N', strtotime("2024-" . ($index + 1) . "-01"));
          $contador_dias = 0;
          echo "<div class='row'>";
          for ($i = 1; $i < $primer_dia_semana; $i++) {
            echo "<div class='col day-box'></div>";
            $contador_dias++;
          }
          for ($dia = 1; $dia <= $dias_mes[$index]; $dia++) {
            $fecha_actual = "2024-" . str_pad($index + 1, 2, "0", STR_PAD_LEFT) . "-" . str_pad($dia, 2, "0", STR_PAD_LEFT);
            $clase = in_array($fecha_actual, $festivos) ? 'selected' : '';
            echo "<div class='col day-box $clase' onclick='showDay(\"$fecha_actual\")'>$dia</div>";
            $contador_dias++;
            if ($contador_dias % 7 === 0) {
              echo "</div><div class='row'>";
            }
          }
          while ($contador_dias % 7 !== 0) {
            echo "<div class='col day-box'></div>";
            $contador_dias++;
          }
          echo "</div>";
          echo "</div>";
        }
        ?>
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
  <script>
    function showDay(fecha) {
      $.ajax({
        url: 'get_day_info.php', // PHP file to fetch the day info
        type: 'POST',
        data: {fecha: fecha},
        success: function(response) {
          alert(response);
        },
        error: function() {
          alert("Error al obtener información del día.");
        }
      });
    }
  </script>
</body>
</html>

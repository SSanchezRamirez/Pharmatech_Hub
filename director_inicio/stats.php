<?php
session_start(); 

$host = "localhost";
$db_username = "uabcsnet_adminParmatech";
$db_password = "CTln0KN41HJ3";
$dbname = "uabcsnet_ritienet_parmatech";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $db_username, $db_password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    
    // Inicializar el arreglo con todos los meses y valores en cero
    $visitas_por_mes = array(
        '1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0, '6' => 0,
        '7' => 0, '8' => 0, '9' => 0, '10' => 0, '11' => 0, '12' => 0
    );

    // Obtener las visitas por mes con una consulta optimizada
    $sql = "SELECT MONTH(f_inicio) AS mes, COUNT(*) AS visitas
            FROM h_registro_visitas
            WHERE aprobacion >= 4 AND YEAR(f_inicio) = :anio AND aprobacion < 10
            GROUP BY MONTH(f_inicio)";
    $stmt = $pdo->prepare($sql);
    $anio_actual = date('Y');
    $stmt->bindParam(':anio', $anio_actual, PDO::PARAM_INT);
    $stmt->execute();
    $resultados = $stmt->fetchAll();

    // Actualizar el arreglo con los datos de la base de datos
    foreach ($resultados as $fila) {
        $visitas_por_mes[$fila['mes']] = $fila['visitas'];
    }
    // Suma el total de visitas anuales
    $total_visitas_anual = array_sum($visitas_por_mes);

} catch (PDOException $e) {
    die("No se pudo conectar a la base de datos: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Estadísticas</title>
    <link rel="icon" href="http://uabcs.net/pharmatechub/recursos/assets/images/logo.png" type="image/png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Newsreader:wght@500&display=swap" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Sora:wght@400;700&display=swap" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="index.css" />
    <script src="https://www.gstatic.com/charts/loader.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    
    <!-- Carga y configuración de gráficos de Google Charts -->
    <script type="text/javascript">
        google.charts.load('current', {packages: ['corechart', 'bar']});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var data = google.visualization.arrayToDataTable([
                ['Mes', 'Visitas'],
                <?php
                foreach ($visitas_por_mes as $mes => $valor) {
                    $nombre_mes = date('F', mktime(0, 0, 0, $mes));
                    echo "['{$nombre_mes}', {$valor}],";
                }
                ?>
            ]);

            var options = {
                title: 'Visitas Mensuales',
                legend: { position: 'none' },
                hAxis: {
                    title: 'Meses',
                    format: 'string',
                    textStyle: { fontSize: 12 }
                },
                vAxis: {
                    title: 'Visitas'
                }
            };

            var chart = new google.visualization.ColumnChart(document.getElementById('chart_mensual'));
            chart.draw(data, options);
        }
    </script>

    <script type="text/javascript">
        google.charts.load('current', {packages: ['corechart', 'bar']});
        google.charts.setOnLoadCallback(drawChart);
        function drawChart() {
            var data = google.visualization.arrayToDataTable([
                ['Trimestre', 'Visitas'],
                ['1er Trimestre', <?php echo $visitas_por_mes['1'] + $visitas_por_mes['2'] + $visitas_por_mes['3']; ?>],
                ['2do Trimestre', <?php echo $visitas_por_mes['4'] + $visitas_por_mes['5'] + $visitas_por_mes['6']; ?>],
                ['3er Trimestre', <?php echo $visitas_por_mes['7'] + $visitas_por_mes['8'] + $visitas_por_mes['9']; ?>],
                ['4to Trimestre', <?php echo $visitas_por_mes['10'] + $visitas_por_mes['11'] + $visitas_por_mes['12']; ?>]
            ]);

            var options = {
                title: 'Visitas Trimestrales',
                legend: { position: 'none' },
                hAxis: {
                    title: 'Trimestres',
                    format: 'string',
                    textStyle: { fontSize: 12 }
                },
                vAxis: {
                    title: 'Visitas'
                }
            };

            var chart = new google.visualization.ColumnChart(document.getElementById('chart_trimestral'));
            chart.draw(data, options);
        }
    </script>

    <script type="text/javascript">
        google.charts.load('current', {packages: ['corechart', 'bar']});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var data = google.visualization.arrayToDataTable([
                ['Año', 'Visitas'],
                ['2024', <?php echo $total_visitas_anual; ?>]
            ]);

            var options = {
                title: 'Visitas Anuales',
                legend: { position: 'none' },
                hAxis: {
                    title: 'Año',
                    format: 'string',
                    textStyle: { fontSize: 12 }
                },
                vAxis: {
                    title: 'Visitas'
                }
            };

            var chart = new google.visualization.ColumnChart(document.getElementById('chart_anual'));
            chart.draw(data, options);
        }
    </script>
</head>
<style>
    #chart_mensual, #chart_trimestral, #chart_anual {
        width: 900px;
        height: 500px;
    }
    .chart-container {
            display: none;
        }

</style>
<body>
    <div class="rectangle-7"></div>
    <div class="vector"></div>
    
    <!-- Carga y configuración de gráficos de Google Charts -->
    <div class="container-fluid">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <a class="navbar-brand" href="http://uabcs.net/pharmatechub/Hub/home_p.html">Pharmatechub</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item active">
                        <a class="nav-link" href="http://uabcs.net/pharmatechub/director_inicio/index.php">Volver</a>
                    </li>
                    <li class="nav-item active">
                        <a class="nav-link" href="http://uabcs.net/pharmatechub/Hub/home_p.html" onclick="cerrarSesion()">Cerrar Sesión <span class="sr-only">(current)</span></a>
                    </li>
                </ul>
            </div>
        </nav>
    </div>

    <!-- Contenedores de gráficos -->
    <div class="row">
        <div class="col--8">
            <div class="container mt-5">
                <div id="chart_mensual">Contenido del gráfico mensual</div>
                <div id="chart_trimestral">Contenido del gráfico trimestral</div>
                <div id="chart_anual">Contenido del gráfico anual</div>
            </div>
        </div>

        <div class="col--4">
        <div class="container mt-5">
            <!-- Contenedores de gráficos -->
            <form id="filtros-form" method="POST" action="procesar.php">
                <div class="form-group">
                    <label for="periodo">Periodo:</label>
                    <select class="form-control" id="periodo" name="periodo" onchange="mostrarDiv()">
                        <option value="">Todos</option>
                        <option value="mensual">Mensual</option>
                        <option value="trimestral">Trimestral</option>
                        <option value="anual">Anual</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="sexo">Sexo:</label>
                    <select class="form-control" id="sexo" name="sexo">
                        <option value="">Todos</option>
                        <option value="Masculino">Masculino</option>
                        <option value="Femenino">Femenino</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="nacionalidad">Nacionalidad:</label>
                    <select class="form-control" id="nacionalidad" name="nacionalidad">
                        <option value="">Todos</option>
                        <?php
                        // Obtener datos de la tabla nacionalidad
                        $query = $pdo->query("SELECT * FROM nacionalidad");
                        while ($row = $query->fetch()) {
                            echo "<option value='" . $row['ISO_NAC'] . "'>" . $row['PAIS_NAC'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="motivo">Motivo de la visita:</label>
                    <select class="form-control" id="motivo" name="motivo">
                        <option value="">Todos</option>
                        <?php
                        // Obtener datos de la tabla motivos_visita
                        $query = $pdo->query("SELECT * FROM motivos_visita");
                        while ($row = $query->fetch()) {
                            echo "<option value='" . $row['id_motivo'] . "'>" . $row['nombre_motivo'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="id_departamento">Departamento:</label>
                    <select class="form-control" id="id_departamento" name="id_departamento" onchange="cargarProyectos(); cargarDoctores();">
                        <option value="">Todos</option>
                        <?php
                        try {
                            // Preparar la consulta SQL para obtener todos los departamentos
                            $stmt = $pdo->prepare("SELECT id_departamento, nombre FROM departamentos");
                            $stmt->execute();
                            // Fetch all rows as an associative array
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
                <div class="form-group">
                    <label for="id_doc">Doctor:</label>
                    <select class="form-control" id="id_doc" name="id_doc">
                        <option value="">Todos</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="id_proyecto">Proyecto:</label>
                    <select class="form-control" id="id_proyecto" name="id_proyecto">
                        <option value="">Todos</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Generar PDF</button>
            </form>
            <div class="form-group"></div>
        </div>
        </div>



    <!-- Librerías -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>




    <!-- Scripts para manejar la lógica de los gráficos y filtros -->
    <script>
        // Mostrar el gráfico correspondiente según el período seleccionado
        function mostrarDiv() {
            const periodoSeleccionado = document.getElementById("periodo").value;
            const divMensual = document.getElementById("chart_mensual");
            const divTrimestral = document.getElementById("chart_trimestral");
            const divAnual = document.getElementById("chart_anual");
            // Oculta todos los divs
            divMensual.style.display = "none";
            divTrimestral.style.display = "none";
            divAnual.style.display = "none";
            // Muestra el div correspondiente según el período seleccionado
            if (periodoSeleccionado === "mensual") {
                divMensual.style.display = "block";
            } else if (periodoSeleccionado === "trimestral") {
                divTrimestral.style.display = "block";
            } else if (periodoSeleccionado === "anual") {
                divAnual.style.display = "block";
            }
        }
        
        
        // Mostrar el gráfico correspondiente según el período seleccionado
        $(document).ready(function() {
            $('#filtros-form').on('submit', function(event) {
                event.preventDefault();

                $.ajax({
                    url: 'procesar.php',
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.error) {
                            alert(response.error);
                        } else {
                            // Crear un formulario temporal para enviar los datos a procesar.php
                            var tempForm = $('<form>', {
                                'method': 'POST',
                                'action': 'procesar.php',
                                'target': '_blank'
                            });

                            // Añadir los campos de los datos del formulario original
                            var formData = $('#filtros-form').serializeArray();
                            $.each(formData, function(i, field) {
                                tempForm.append($('<input>', {
                                    'type': 'hidden',
                                    'name': field.name,
                                    'value': field.value
                                }));
                            });

                            // Añadir el formulario temporal al body y enviarlo
                            tempForm.appendTo('body').submit().remove();
                        }
                    }
                });
            });
        });

        // Cargar proyectos y doctores según el departamento seleccionado
        function cargarProyectos() {
            var departamentoSelect = document.getElementById('id_departamento');
            var departamentoId = departamentoSelect.value;
            var proyectoSelect = document.getElementById('id_proyecto');
            // Realizar una llamada AJAX a rec_proyectos.php
            fetch('rec_proyectos.php?departamento_id=' + departamentoId)
                .then(response => response.json())
                .then(data => {
                    // Actualizar las opciones del select de proyectos
                    proyectoSelect.innerHTML = '<option value="" disabled selected>Todos</option>';
                    data.forEach(proyecto => {
                        var option = document.createElement('option');
                        option.value = proyecto.id_proyecto;
                        option.textContent = proyecto.nombre;
                        proyectoSelect.appendChild(option);
                    });
                })
            .catch(error => console.error('Error:', error));
        }

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
                        option.textContent = doctor.nombre_completo;// Asumiendo que quieres mostrar el nombre completo
                        doctorSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error:', error));
        }
    </script>
</body>
</html>
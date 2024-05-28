<?php
session_start();

$host = "localhost";
$db_username = "uabcsnet_adminParmatech";
$db_password = "CTln0KN41HJ3";
$dbname = "uabcsnet_ritienet_parmatech";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $db_username, $db_password, [
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    //incluimos la libreria para generar pdfs a partir de codigos php
    require('../fdpf/fpdf.php');

    //Creamos la tabla con los parametros deseados
    class PDF extends FPDF
    {
        function FancyTable($header, $data, $w)
        {
            $this->SetFillColor(99, 185, 206 );
            $this->SetTextColor(255);
            $this->SetDrawColor(136,232,255);
            $this->SetLineWidth(.3);
            $this->SetFont('Arial','B',12);
            //Generacion de encabezados de la tabla
            for ($i = 0; $i < count($header); $i++) {
                $this->Cell($w[$i], 7, utf8_decode($header[$i]), 1, 0, 'C', true);
            }
            $this->Ln();

            $this->SetFillColor(202,237,253);
            $this->SetTextColor(0);
            $this->SetFont('Arial','',10);

            $fill = false;
            foreach ($data as $row) {
                foreach ($row as $key => $value) {
                    $this->Cell($w[array_search($key, array_keys($row))], 6, utf8_decode($value), 'LR', 0, 'L', $fill);
                }
                $this->Ln();
                $fill = !$fill;
            }
            $this->Cell(array_sum($w), 0, '', 'T');
        }
        // Función para mostrar un mensaje de error en el PDF
        function ErrorMsg($msg)
        {
            $this->SetFont('Arial', 'B', 12);
            $this->SetFillColor(202,237,253);
            $this->Cell(0, 10, 'Error', 0, 1, 'C');
            $this->SetFont('Arial', '', 10);
            $this->SetTextColor(0, 0, 0);
            $this->MultiCell(0, 10, utf8_decode($msg), 0, 'L');
        }
    }
    // Variables de filtro recibidas por la pagina stats

    $periodo = $_POST['periodo'] ?? null;
    $nacionalidad = $_POST['nacionalidad'] ?? null;
    $motivo = $_POST['motivo'] ?? null;
    $id_departamento = $_POST['id_departamento'] ?? null;
    $id_doc = $_POST['id_doc'] ?? null;
    $id_proyecto = $_POST['id_proyecto'] ?? null;
    $sexo = $_POST['sexo'] ?? null;
    // Consulta base SQL para obtener los datos filtrados, solo mostrara los registros con los rangos admitidos 
    $sql = "SELECT 
                iv.nombre AS Nombre, 
                iv.apellido_paterno AS 'Apellido Paterno', 
                iv.apellido_materno AS 'Apellido Materno', 
                hrv.f_inicio AS 'Fecha Inicio', 
                hrv.f_fin AS 'Fecha Fin', 
                hrv.id_departamento AS Departamento, 
                hrv.id_proyecto AS Proyecto, 
                hrv.id_doc AS Doctor, 
                hrv.motivo_v AS Motivo, 
                n.PAIS_NAC AS País, 
                iv.sexo AS Sexo 
            FROM 
                h_registro_visitas hrv
                INNER JOIN info_visitantes iv ON hrv.nombre_usuario = iv.nombre_usuario
                INNER JOIN nacionalidad n ON iv.pais = n.PAIS_NAC
            WHERE 
                hrv.aprobacion > 4 AND hrv.aprobacion < 10";



    // Agregar condiciones de los filtros a partir de la pagina stats
    $conditions = [];
    if ($nacionalidad) {
        $conditions[] = "n.ISO_NAC = :nacionalidad";
    }
    if ($motivo) {
        $conditions[] = "hrv.m_visita = :motivo";
    }
    if ($id_departamento) {
        $conditions[] = "hrv.id_departamento = :departamento";
    }
    if ($id_doc) {
        $conditions[] = "hrv.id_doc = :doctor";
    }
    if ($id_proyecto) {
        $conditions[] = "hrv.id_proyecto = :proyecto";
    }
    if ($sexo) {
        $conditions[] = "iv.sexo = :sexo";
    }

    if (count($conditions) > 0) {
        $sql .= " AND " . implode(" AND ", $conditions);
    }

    $stmt = $pdo->prepare($sql);// Prepara la consulta SQL  

    
        // Parámetros para la consulta
    $params = [];
    if ($nacionalidad) {
        $params[':nacionalidad'] = $nacionalidad;
    }
    if ($motivo) {
        $params[':motivo'] = $motivo;
    }
    if ($id_departamento) {
        $params[':departamento'] = $id_departamento;
    }
    if ($id_doc) {
        $params[':doctor'] = $id_doc;
    }
    if ($id_proyecto) {
        $params[':proyecto'] = $id_proyecto;
    }
    if ($sexo) {
        $params[':sexo'] = $sexo;
    }

    $stmt->execute($params);// Ejecuta la consulta con los parámetros ingresadis si es que se ingresaron
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);// Obtiene todos los resultados y los almacena para mostrarlos en el pdf

    if (empty($data)) {
        throw new Exception("No se encontraron resultados para los filtros aplicados.");
    }
    
    
    
    // Obtener nombres para los campos de departamento, proyecto y doctor
    foreach ($data as &$row) {
        $stmtDep = $pdo->prepare("SELECT nombre FROM departamentos WHERE id_departamento = ?");
        $stmtDep->execute([$row['Departamento']]);
        $row['Departamento'] = $stmtDep->fetchColumn();

        $stmtProy = $pdo->prepare("SELECT nombre FROM proyectos WHERE id_proyecto = ?");
        $stmtProy->execute([$row['Proyecto']]);
        $row['Proyecto'] = $stmtProy->fetchColumn();

        $stmtDoc = $pdo->prepare("SELECT CONCAT(nombre_s, ' ', apellido_paterno, ' ', apellido_materno) FROM info_drs WHERE id_doc = ?");
        $stmtDoc->execute([$row['Doctor']]);
        $row['Doctor'] = $stmtDoc->fetchColumn();
    }
    
    
    // Calcular el ancho de las columnas para el PDF a partir del ancho de las letras
    $w = array();
    $pdf_temp = new PDF();
    $pdf_temp->SetFont('Arial', '', 10);
    foreach ($data[0] as $col => $value) {
        $w[] = $pdf_temp->GetStringWidth($col) + 6;
    }
    // Ajustar el ancho de las columnas según el contenido a partir del ancho de las letras
    foreach ($data as $row) {
        foreach ($row as $key => $value) {
            $width = $pdf_temp->GetStringWidth($value) + 6;
            if ($width > $w[array_search($key, array_keys($row))]) {
                $w[array_search($key, array_keys($row))] = $width;
            }
        }
    }
    // Crear el PDF con las medidas y orientacion deseada
    $pdf = new PDF('L', 'mm', array(array_sum($w)+20, 200));
    $pdf->SetFont('Arial','',10);
    $pdf->AddPage();
    $pdf->Image('http://uabcs.net/pharmatechub/recursos/assets/images/logo.png', 0, 0, 40, 30); //logotipo   
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, 'Reporte de visitas', 0, 1, 'C'); //titulo
    $pdf->SetFont('Arial', 'B', 14);
    
    $pdf->Cell(0, 10, 'Visitas totales: ' . count($data), 0, 1, 'C');//subtitulo para conteo

    // Obtener nombres correspondientes a los filtros aplicados
    $filtros = [];//adicion de filtros
    if ($nacionalidad) {
        $stmtNac = $pdo->prepare("SELECT GENTILICIO_NAC FROM nacionalidad WHERE ISO_NAC = ?");
        $stmtNac->execute([$nacionalidad]);
        $nacionalidadNombre = $stmtNac->fetchColumn();
        $filtros[] = "Nacionalidad: $nacionalidadNombre";
    }
    if ($motivo) {
        $stmtMotivo = $pdo->prepare("SELECT nombre_motivo FROM motivos_visita WHERE id_motivo = ?");
        $stmtMotivo->execute([$motivo]);
        $motivoNombre = $stmtMotivo->fetchColumn();
        $filtros[] = "Motivo: $motivoNombre";
    }
    if ($id_departamento) {
        $stmtDep = $pdo->prepare("SELECT nombre FROM departamentos WHERE id_departamento = ?");
        $stmtDep->execute([$id_departamento]);
        $depNombre = $stmtDep->fetchColumn();
        $filtros[] = "Departamento: $depNombre";
    }
    if ($id_doc) {
        $stmtDoc = $pdo->prepare("SELECT CONCAT(nombre_s, ' ', apellido_paterno, ' ', apellido_materno) FROM info_drs WHERE id_doc = ?");
        $stmtDoc->execute([$id_doc]);
        $docNombre = $stmtDoc->fetchColumn();
        $filtros[] = "Doctor: $docNombre";
    }
    if ($id_proyecto) {
        $stmtProy = $pdo->prepare("SELECT nombre FROM proyectos WHERE id_proyecto = ?");
        $stmtProy->execute([$id_proyecto]);
        $proyNombre = $stmtProy->fetchColumn();
        $filtros[] = "Proyecto: $proyNombre";
    }
    if ($sexo) {
        $filtros[] = "Sexo: $sexo";
    }

    // Agregar filtros aplicados al PDF
    if (empty($filtros)) {
        $pdf->Cell(0, 10, 'Visitas Finalizadas', 0, 1, 'L');
    } else {
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, 'Lista de Filtros Aplicados:', 0, 1, 'L');
        $pdf->SetFont('Arial', '', 10);
        foreach ($filtros as $filtro) {
            $pdf->Cell(0, 10, utf8_decode($filtro), 0, 1, 'L');        }
    }
    
    // Generar la tabla en el PDF
    $pdf->FancyTable(array_keys($data[0]), $data, $w);
    
    //Parametros para mostrar el PDF en el navegador
    $pdf->Output('documento.pdf', 'I');

} catch (PDOException $e) {
    echo "<script type='text/javascript'>alert('Error de conexión a la base de datos: " . $e->getMessage() . "');</script>";
} catch (Exception $e) {
    // Manejo para el caso de que no existan datos con ese filtrado
    echo "<script type='text/javascript'>
        alert('Error: No existen registros con los parámetros ingresados');
        window.history.back();
        window.close();
    </script>";
}
?>

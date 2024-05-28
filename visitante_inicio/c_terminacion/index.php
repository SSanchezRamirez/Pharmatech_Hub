<?php
// Habilitar la visualización de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();// Iniciar sesión 
require('../../fdpf/fpdf.php');// Incluir la librería FPDF

// Verificar si la variable de sesión 'username' está definida
if (!isset($_SESSION['username'])) {
    die('Error: La sesión de usuario no está definida.');
}

// Configuración de conexión a la base de datos
$host = "localhost";
$db_username = "uabcsnet_adminParmatech";
$db_password = "CTln0KN41HJ3";
$dbname = "uabcsnet_ritienet_parmatech";
try {
     // Crear una conexión PDO a la base de datos
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $db_username, $db_password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
     // Manejar errores en la conexión a la base de datos
    die("No se pudo conectar a la base de datos: " . $e->getMessage());
}

// Consulta para obtener todos los datos necesarios
$sql = "SELECT hv.*, 
               idr.nombre_s, idr.apellido_paterno, idr.apellido_materno, idr.ruta_firma AS firma_doctor, 
               jefes.ruta_firma AS firma_jefe, 
               depto.nombre AS nombre_departamento, 
               proj.nombre AS nombre_proyecto, 
               motivo.nombre_motivo, 
               visit.nombre AS nombre_visitante, visit.apellido_paterno AS apellido_paterno_visitante, visit.apellido_materno AS apellido_materno_visitante, visit.ruta_firma,
               visit.pais
        FROM h_registro_visitas hv
        JOIN info_drs idr ON hv.id_doc = idr.id_doc
        JOIN info_jef_dep jefes ON hv.id_departamento = jefes.dep
        JOIN departamentos depto ON hv.id_departamento = depto.id_departamento
        JOIN proyectos proj ON hv.id_proyecto = proj.id_proyecto
        JOIN motivos_visita motivo ON hv.m_visita = motivo.id_motivo
        JOIN info_visitantes visit ON hv.nombre_usuario = visit.nombre_usuario
        WHERE hv.nombre_usuario = :username 
        ORDER BY hv.id_visita DESC 
        LIMIT 1";

$stmt = $pdo->prepare($sql);
$stmt->bindParam(':username', $_SESSION['username'], PDO::PARAM_STR);
$stmt->execute();
$visitData = $stmt->fetch(PDO::FETCH_ASSOC); // Obtener los datos de la visita

// Verificar si se obtuvieron datos de la visita
if (!$visitData) {
    die('Error: No se encontraron datos de la visita.');
}

// Crear la carta en PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 14);

// Encabezado de la carta
$pdf->Image('http://uabcs.net/pharmatechub/recursos/assets/images/logo.png', 10, 10, 30);
$pdf->Cell(0, 10, 'Pharmatech', 0, 1, 'C');
$pdf->Cell(0, 10, 'La Paz, Baja California Sur, 3080', 0, 1, 'C');
$pdf->Cell(0, 10, mb_convert_encoding('Teléfono: 6120521032', 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
$pdf->Ln(10); // Espacio

// Cuerpo de la carta
$pdf->SetFont('Arial', '', 12);
$nombreCompletoDoctor = mb_convert_encoding($visitData['nombre_s'] . " " . $visitData['apellido_paterno'] . " " . $visitData['apellido_materno'], 'ISO-8859-1', 'UTF-8');
$pdf->MultiCell(0, 10, mb_convert_encoding("País: {$visitData['pais']}\nFecha inicio: {$visitData['f_inicio']}\nFecha final: {$visitData['f_fin']}\n\nEstimado/a {$visitData['nombre_visitante']}:\n\nLe informamos que su visita a Pharmatech ha concluido exitosamente. Agradecemos su interés y participación en nuestras actividades. Durante su estancia, se alcanzaron los objetivos propuestos, y esperamos que la experiencia haya sido valiosa para usted.\n\nEl folio de su visita es: {$visitData['id_visita']}, y se realizó bajo el motivo de \"{$visitData['nombre_motivo']}\". Si necesita más detalles sobre su visita o tiene alguna solicitud, no dude en contactarnos.\n\nEsperamos que su visita haya sido de su agrado y quedamos a la espera de poder recibirle nuevamente en el futuro.\n\nCordialmente,\nPharmatech                                                                                                         Firma Director", 'ISO-8859-1', 'UTF-8'));

// Agregar las firmas y nombres de las firmas
if (!empty($visitData['ruta_firma'])) {
    $pdf->Image($visitData['ruta_firma'], 30, 230, 30); // Firma del solicitante
}
if (!empty($visitData['firma_doctor'])) {
    $pdf->Image($visitData['firma_doctor'], 85, 230, 30); // Firma del médico
}
if (!empty($visitData['firma_jefe'])) {
    $pdf->Image($visitData['firma_jefe'], 140, 230, 35); // Firma del jefe de departamento
}
$pdf->Image("http://uabcs.net/pharmatechub/recursos/assets/firmas_dr/A_mar.png", 150, 210, 40); // Firma del jefe de departamento



$pdf->Ln(20); // Espacio

$pdf->Cell(62, 10, mb_convert_encoding('Firma del solicitante', 'ISO-8859-1', 'UTF-8'), 0, 0, 'C');
$pdf->Cell(62, 10, mb_convert_encoding('Firma médico', 'ISO-8859-1', 'UTF-8'), 0, 0, 'C');
$pdf->Cell(62, 10, mb_convert_encoding('Firma Jefe de Departamento', 'ISO-8859-1', 'UTF-8'), 0, 0, 'C');


// Guardar el archivo PDF
$pdf->Output('I', 'carta_final_' . $_SESSION["username"] . '.pdf');
?>

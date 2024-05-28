<?php
// Habilitar la visualización de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();// Iniciar la sesión
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
// Intentar establecer la conexión a la base de datos
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $db_username, $db_password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    // En caso de error, mostrar un mensaje de error y terminar la ejecución del script
    die("No se pudo conectar a la base de datos: " . $e->getMessage());
}

// Consulta para obtener todos los datos necesarios
$sql = "SELECT hv.*, 
               idr.nombre_s, idr.apellido_paterno, idr.apellido_materno, idr.ruta_firma AS firma_doctor, 
               jefes.ruta_firma AS firma_jefe, 
               depto.nombre AS nombre_departamento, 
               proj.nombre AS nombre_proyecto, 
               motivo.nombre_motivo, 
               visit.nombre AS nombre_visitante, visit.apellido_paterno AS apellido_paterno_visitante, visit.apellido_materno AS apellido_materno_visitante, visit.ruta_firma 
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

// Preparar la consulta SQL
$stmt = $pdo->prepare($sql);
// Asignar el valor de la variable de sesión 'username' al parámetro de la consulta
$stmt->bindParam(':username', $_SESSION['username'], PDO::PARAM_STR);
// Ejecutar la consulta
$stmt->execute();
// Obtener los datos de la visita
$visitData = $stmt->fetch(PDO::FETCH_ASSOC);

// Verificar si se obtuvieron datos de la visita
if (!$visitData) {
    die('Error: No se encontraron datos de la visita.');
}

// Crear la carta en PDF
$pdf = new FPDF();
$pdf->AddPage();

$pdf->SetFont('Arial', 'B', 14);

// Encabezado de la carta
$pdf->Image('http://uabcs.net/pharmatechub/recursos/assets/images/logo.png', 10, 10, 30); // Cambia la URL de la imagen, coordenadas x, y, ancho
$pdf->Cell(0, 10, 'Pharmatech', 0, 1, 'C');
$pdf->Cell(0, 10, 'La Paz, Baja California Sur, 3080', 0, 1, 'C');
$pdf->Cell(0, 10, 'No.Contacto: 6120521032', 0, 1, 'C');
$pdf->Ln(10); // Espacio

$pdf->SetFont('Arial', '', 12);
$nombreCompletoDoctor = $visitData['nombre_s'] . " " . $visitData['apellido_paterno'] . " " . $visitData['apellido_materno'];
$text = "Folio: 00{$visitData['id_visita']}\nFecha: {$visitData['f_inicio']}\n\nEstimado/a {$visitData['nombre_visitante']}:\n\nNos complace informarle que su solicitud de visita a nuestras instalaciones ha sido aceptada. Le extendemos una cálida bienvenida y esperamos que su experiencia con nosotros sea enriquecedora. La visita está programada para el día {$visitData['f_inicio']} en nuestras instalaciones ubicadas en La Paz, BCS, con el doctor {$nombreCompletoDoctor}. Durante su estancia, tendrá la oportunidad de conocer nuestro proceso de investigación y desarrollo de productos farmacéuticos. Le solicitamos que se presente en la recepción el día acordado, a las 9:00 am, portando esta carta de aceptación. Además, le proporcionaremos una credencial de visitante. No dude en contactarnos si tiene alguna pregunta adicional. Esperamos darle la bienvenida y compartir nuestro trabajo en farmacéutica.\n\nAtentamente,\nPharmatech";
$pdf->MultiCell(0, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $text));

// Agregar las firmas
$pdf->Image($visitData['ruta_firma'], 40, 230, 25); // Firma del visitante
$pdf->Image($visitData['firma_doctor'], 125, 232, 30); // Firma del doctor

$pdf->Ln(20); // Espacio
$pdf->Cell(100, 10, '_______________________', 0, 0, 'C');
$pdf->Cell(60, 10, '_______________________', 0, 1, 'C');
$pdf->Cell(100, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'Firma del solicitante'), 0, 0, 'C');
$pdf->Cell(60, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'Firma médico'), 0, 0, 'C');

// Guardar el archivo PDF
$pdf->Output('I', 'carta_aceptacion_' . $_SESSION["username"] . '.pdf'); // Descargar el archivo directamente
?>

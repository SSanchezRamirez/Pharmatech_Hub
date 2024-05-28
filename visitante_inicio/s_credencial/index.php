<?php
session_start();//Iniciar la sesión
// Incluir la librería FPDF para generar PDFs
require('../../fdpf/fpdf.php');


// Verificar si la variable de sesión 'username' está definida
if (!isset($_SESSION['username'])) {
    die('Error: La sesión de usuario no está definida.');
}

// Configuración de conexión a la base de datos
$host = "localhost"; // Dirección del servidor de la base de datos
$db_username = "uabcsnet_adminParmatech"; // Nombre de usuario de la base de datos
$db_password = "CTln0KN41HJ3"; // Contraseña de la base de datos
$dbname = "uabcsnet_ritienet_parmatech"; // Nombre de la base de datos

try {
    // Intentar establecer la conexión a la base de datos utilizando PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $db_username, $db_password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Establecer el modo de error para lanzar excepciones en caso de errores
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC // Establecer el modo de recuperación predeterminado para devolver un array asociativo
    ]);
} catch (PDOException $e) {
    // Si hay un error de conexión, mostrar un mensaje y terminar la ejecución
    die("No se pudo conectar a la base de datos: " . $e->getMessage());
}

// Consulta para obtener los datos del visitante y la última visita en proceso
$sql = "SELECT iv.*, hv.id_visita, hv.f_inicio, hv.f_fin, hv.ruta_imagen, idr.nombre_s AS nombre_doctor, idr.apellido_paterno AS apellido_paterno_doctor, idr.apellido_materno AS apellido_materno_doctor, d.nombre AS nombre_departamento
        FROM info_visitantes iv
        LEFT JOIN h_registro_visitas hv ON iv.nombre_usuario = hv.nombre_usuario
        LEFT JOIN info_drs idr ON hv.id_doc = idr.id_doc
        LEFT JOIN departamentos d ON hv.id_departamento = d.id_departamento
        WHERE iv.nombre_usuario = :username 
        ORDER BY hv.id_visita DESC 
        LIMIT 1";
$stmt = $pdo->prepare($sql); // Preparar la consulta SQL
$stmt->bindParam(':username', $_SESSION['username'], PDO::PARAM_STR); // Vincular el parámetro :username con el valor de $_SESSION['username']
$stmt->execute(); // Ejecutar la consulta preparada
$userData = $stmt->fetch(); // Obtener la primera fila de resultados y almacenarla en $userData


// Verificar si se obtuvieron datos del visitante
if (!$userData) {
    die('Error: No se encontraron datos del visitante.');
}

// Crear la credencial en PDF
$pdf = new FPDF();
$pdf->AddPage();

// Cargar la imagen de la credencial como fondo y ajustar el tamaño
$pdf->Image('http://uabcs.net/pharmatechub/recursos/assets/images/credencial.png', 0, 10, 210, 90);

// Agregar texto encima de la imagen
$pdf->SetFont('Arial', '', 12);

// ID de Visita (Folio)
$pdf->SetXY(62, 25);
$pdf->Cell(0, 8, "Folio: " . ($userData['id_visita'] ?? 'N/A'));

// Nombre y Apellido del Visitante
$pdf->SetXY(20, 65);
$pdf->Cell(0, 10, " " . utf8_decode($userData['nombre'] ?? 'N/A') . " " . utf8_decode($userData['apellido_paterno'] ?? '') . " " . utf8_decode($userData['apellido_materno'] ?? ''));

// Nacionalidad
$pdf->SetXY(54, 32);
$pdf->Cell(0, 10, " " . utf8_decode($userData['pais'] ?? 'N/A'));

// Fecha de inicio
$pdf->SetXY(55, 38);
$pdf->Cell(0, 10, "Fecha inicio: " . ($userData['f_inicio'] ?? 'N/A'));

// Fecha de finalización
$pdf->SetXY(55, 45);
$pdf->Cell(0, 10, "Fecha fin: " . ($userData['f_fin'] ?? 'N/A'));

// Doctor Asociado
$pdf->SetXY(55, 51);
$pdf->Cell(0, 10, "Doctor: " . utf8_decode($userData['nombre_doctor'] ?? 'N/A') . " " . utf8_decode($userData['apellido_paterno_doctor'] ?? '') . " " . utf8_decode($userData['apellido_materno_doctor'] ?? ''));

// Departamento
$pdf->SetXY(12, 70);
$pdf->Cell(0, 10, " " . utf8_decode($userData['nombre_departamento'] ?? 'N/A'));

// Firma Jefe del Departamento
//$pdf->SetXY(20, 85);
//$pdf->Cell(0, 10, utf8_decode("Firma Jefe Depto\n\n\n"));

// Firma Visitante
//$pdf->SetXY(62, 85);
//$pdf->Cell(0, 10, utf8_decode("Firma Visitante\n"));

// Visitante
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetXY(45, 76);
$pdf->Cell(0, 10, "VISITANTE ");

// Cargar la imagen del usuario
if (!empty($userData['ruta_imagen'])) {
    $imagenUsuario = $userData['ruta_imagen'];
    // Ajusta las coordenadas y el tamaño según tu diseño
    $pdf->Image($imagenUsuario, 20, 35, 30, 30); 
}

// Guardar el archivo PDF
$pdf->Output('credencial_visitante_' . $_SESSION["username"] . '.pdf', 'I'); // Descargar el archivo directamente
?>

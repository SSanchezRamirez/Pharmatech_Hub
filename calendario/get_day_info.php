<?php
session_start();
header('Content-Type: text/plain; charset=utf-8'); // Asegura la correcta codificación de caracteres

// Obtener la fecha desde la solicitud AJAX
$fecha = $_POST['fecha'] ?? ''; // Usar el operador de fusión de null para evitar errores si no se establece

// Conexión a la base de datos
$conexion = new mysqli('localhost', 'uabcsnet_adminParmatech', 'CTln0KN41HJ3', 'uabcsnet_ritienet_parmatech');
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// Preparar la consulta SQL para evitar inyecciones SQL
$stmt = $conexion->prepare("SELECT * FROM dias_festivos WHERE fecha = ?");
$stmt->bind_param("s", $fecha); // 's' especifica el tipo de dato como string
$stmt->execute();
$resultado = $stmt->get_result();

// Comprobar si hay resultados y devolverlos
if ($resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        echo "Nombre: " . $fila['nombre'] . "\n";
        echo "Fecha: " . $fila['fecha'] . "\n";
        echo "Descripción: " . $fila['descripcion'] . "\n";
    }
} else {
    echo "No hay información para este día.";
}

$conexion->close();
?>

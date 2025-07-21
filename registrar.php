<?php
session_start();
require 'conexion.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$accion = $_POST['accion'];
$latitud = $_POST['latitud'];
$longitud = $_POST['longitud'];
$fecha_hora = date('Y-m-d H:i:s');

try {
    // Insertar el registro en la base de datos
    $stmt = $conn->prepare("INSERT INTO registros (usuario_id, tipo, fecha_hora, latitud, longitud) VALUES (:usuario_id, :tipo, :fecha_hora, :latitud, :longitud)");
    $stmt->execute([
        'usuario_id' => $usuario_id,
        'tipo' => $accion,
        'fecha_hora' => $fecha_hora,
        'latitud' => $latitud,
        'longitud' => $longitud
    ]);

    // Devolver una respuesta JSON exitosa
    echo json_encode(['success' => true, 'message' => 'Registro exitoso']);
} catch (PDOException $e) {
    // Devolver una respuesta JSON en caso de error
    echo json_encode(['success' => false, 'message' => 'Error al registrar: ' . $e->getMessage()]);
}
?>
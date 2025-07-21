<?php
session_start();
require 'conexion.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

// Obtener el filtro y el ID del empleado (si es administrador)
$filtro = isset($_GET['filtro']) ? $_GET['filtro'] : 'todos';
$empleado_id = isset($_GET['empleado_id']) ? $_GET['empleado_id'] : $_SESSION['usuario_id'];

// Obtener el nombre del empleado
$stmt = $conn->prepare("SELECT nombre, apellido FROM usuarios WHERE id = :empleado_id");
$stmt->execute(['empleado_id' => $empleado_id]);
$empleado = $stmt->fetch(PDO::FETCH_ASSOC);
$nombre_empleado = $empleado['nombre'] . ' ' . $empleado['apellido'];

// Construir la consulta SQL según el filtro seleccionado
$sql = "SELECT tipo, fecha_hora FROM registros WHERE usuario_id = :usuario_id";
$params = ['usuario_id' => $empleado_id];

switch ($filtro) {
    case 'hoy':
        $sql .= " AND DATE(fecha_hora) = CURDATE()";
        break;
    case 'semana':
        $sql .= " AND YEARWEEK(fecha_hora, 1) = YEARWEEK(CURDATE(), 1)";
        break;
    case 'mes':
        $sql .= " AND YEAR(fecha_hora) = YEAR(CURDATE()) AND MONTH(fecha_hora) = MONTH(CURDATE())";
        break;
    // 'todos' no necesita filtro adicional
}

$sql .= " ORDER BY fecha_hora ASC"; // Ordenar por fecha y hora ascendente

// Ejecutar la consulta
$stmt = $conn->prepare($sql);
$stmt->execute($params);
$registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calcular horas trabajadas y horas de tiempo de espera
$total_horas_trabajadas = 0;
$total_horas_espera = 0;
$inicio_jornada = null;
$inicio_espera = null;

foreach ($registros as $registro) {
    $tipo = $registro['tipo'];
    $fecha_hora = new DateTime($registro['fecha_hora']);

    if ($tipo == 'inicio_jornada') {
        $inicio_jornada = $fecha_hora;
    } elseif ($tipo == 'fin_jornada' && $inicio_jornada) {
        $diferencia = $fecha_hora->diff($inicio_jornada);
        $total_horas_trabajadas += $diferencia->h + ($diferencia->i / 60) + ($diferencia->s / 3600);
        $inicio_jornada = null;
    } elseif ($tipo == 'inicio_espera') {
        $inicio_espera = $fecha_hora;
    } elseif ($tipo == 'fin_espera' && $inicio_espera) {
        $diferencia = $fecha_hora->diff($inicio_espera);
        $total_horas_espera += $diferencia->h + ($diferencia->i / 60) + ($diferencia->s / 3600);
        $inicio_espera = null;
    }
}

// Formatear las horas totales en HH:MM:SS
function formatoHora($horas) {
    $horas_enteras = intval($horas);
    $minutos = intval(($horas - $horas_enteras) * 60);
    $segundos = intval((($horas - $horas_enteras) * 60 - $minutos) * 60);
    return sprintf("%02d:%02d:%02d", $horas_enteras, $minutos, $segundos);
}

$total_horas_trabajadas_formato = formatoHora($total_horas_trabajadas);
$total_horas_espera_formato = formatoHora($total_horas_espera);

// Crear el archivo CSV
$filename = "registros_" . $nombre_empleado . "_" . date('Y-m-d') . ".csv";
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$output = fopen('php://output', 'w');

// Escribir la cabecera del CSV
fputcsv($output, ['Nombre del Empleado', 'Total Horas Trabajadas', 'Total Horas de Espera']);

// Escribir los totales en el CSV
fputcsv($output, [$nombre_empleado, $total_horas_trabajadas_formato, $total_horas_espera_formato]);

// Escribir los registros en el CSV
fputcsv($output, []); // Línea en blanco
fputcsv($output, ['Tipo', 'Fecha y Hora']); // Cabecera de registros

foreach ($registros as $registro) {
    fputcsv($output, [$registro['tipo'], $registro['fecha_hora']]);
}

fclose($output);
exit();
?>
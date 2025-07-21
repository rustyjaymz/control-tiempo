<?php
session_start();
require 'conexion.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Obtener empresas y sus empleados
$empresas = $conn->query("
    SELECT e.id, e.nombre, 
           (SELECT COUNT(*) FROM usuarios u WHERE u.empresa_id = e.id) as total_empleados
    FROM empresas e
")->fetchAll(PDO::FETCH_ASSOC);

// Obtener estado de empleados por empresa
foreach ($empresas as &$empresa) {
    $stmt = $conn->prepare("
        SELECT u.id, u.nombre, u.apellido, 
               (SELECT r.tipo FROM registros r 
                WHERE r.usuario_id = u.id 
                ORDER BY r.fecha_hora DESC LIMIT 1) as ultimo_registro
        FROM usuarios u
        WHERE u.empresa_id = ?
    ");
    $stmt->execute([$empresa['id']]);
    $empresa['empleados'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
unset($empresa); // Romper la referencia
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Monitor por Empresas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .empresa-card {
            margin-bottom: 2rem;
            border-left: 5px solid #0d6efd;
        }
        .status-indicator {
            width: 15px;
            height: 15px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 8px;
        }
        .active { background-color: #28a745; box-shadow: 0 0 8px #28a745; }
        .inactive { background-color: #dc3545; }
        .badge-empresa {
            font-size: 0.9rem;
            background-color: #6c757d;
        }
    </style>
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="admin_panel.php">Volver al Home</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="agregar_empleado.php">Agregar Nuevo Empleado</a>
                <a class="nav-link" href="logout.php">Cerrar Sesión</a>
            </div>
        </div>
    </nav>
    <div class="container py-4">
        <h1 class="mb-4">Monitor por Empresas</h1>
        
        <?php foreach ($empresas as $empresa): ?>
        <div class="card empresa-card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3><?= $empresa['nombre'] ?></h3>
                <span class="badge badge-empresa"><?= $empresa['total_empleados'] ?> empleados</span>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php foreach ($empresa['empleados'] as $empleado): ?>
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <span class="status-indicator <?= ($empleado['ultimo_registro'] == 'inicio_jornada') ? 'active' : 'inactive' ?>"></span>
                                        <h5 class="d-inline"><?= $empleado['nombre'] ?> <?= $empleado['apellido'] ?></h5>
                                    </div>
                                    <span class="badge bg-light text-dark">
                                        <?= ($empleado['ultimo_registro'] == 'inicio_jornada') ? 'Activo' : 'Inactivo' ?>
                                    </span>
                                </div>
                                <a href="ver_registros.php?empleado_id=<?= $empleado['id'] ?>" class="btn btn-sm btn-outline-primary mt-2">Ver detalles</a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <script>
        // Actualizar cada 60 segundos
        setTimeout(() => location.reload(), 60000);
    </script>
</body>
</html>
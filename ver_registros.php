<?php
session_start();
require 'conexion.php';

// Verificar si el usuario es administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Obtener el filtro seleccionado
$filtro = isset($_GET['filtro']) ? $_GET['filtro'] : 'todos';

// Obtener la lista de empleados
$stmt = $conn->prepare("SELECT id, nombre, apellido FROM usuarios WHERE rol = 'empleado'");
$stmt->execute();
$empleados = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener los registros de un empleado específico (si se selecciona)
$registros = [];
$empleado_seleccionado = null;
if (isset($_GET['empleado_id'])) {
    $empleado_id = $_GET['empleado_id'];
    
    // Obtener los datos del empleado seleccionado
    $stmt = $conn->prepare("SELECT nombre, apellido, rut, telefono FROM usuarios WHERE id = :empleado_id");
    $stmt->execute(['empleado_id' => $empleado_id]);
    $empleado_seleccionado = $stmt->fetch(PDO::FETCH_ASSOC);

    // Construir la consulta SQL según el filtro seleccionado
    $sql = "SELECT tipo, fecha_hora, latitud, longitud FROM registros WHERE usuario_id = :empleado_id";
    $params = ['empleado_id' => $empleado_id];

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

    $sql .= " ORDER BY fecha_hora DESC";

    // Ejecutar la consulta
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Registros de Empleados</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="styles.css">
</head>
<body class="bg-light">
    <!-- Barra de navegación -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <!-- Brand/logo -->
            <a class="navbar-brand" href="#">Registros de Empleados</a>

            <!-- Botón para colapsar el menú en móviles -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Menú colapsable -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="gestion_empleados.php">Volver al Panel</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Cerrar Sesión</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Contenido de la página -->
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-10 mx-auto">
                <div class="card shadow animate__animated animate__fadeInUp">
                    <div class="card-body">
                        <h2 class="card-title text-center mb-4">Registros de Empleados</h2>

                        
                        <!-- Filtro y exportación de registros -->
                        <?php if (isset($_GET['empleado_id'])): ?>
                            <div class="mb-4">
                                <h4>Filtrar y Exportar Registros</h4>
                                <form method="GET" action="ver_registros.php" class="row g-3">
                                    <input type="hidden" name="empleado_id" value="<?php echo $_GET['empleado_id']; ?>">
                                    <div class="col-md-8">
                                        <select id="filtro" name="filtro" class="form-select" onchange="this.form.submit()">
                                            <option value="todos" <?php echo ($filtro == 'todos') ? 'selected' : ''; ?>>Todos los Registros</option>
                                            <option value="hoy" <?php echo ($filtro == 'hoy') ? 'selected' : ''; ?>>Hoy</option>
                                            <option value="semana" <?php echo ($filtro == 'semana') ? 'selected' : ''; ?>>Esta Semana</option>
                                            <option value="mes" <?php echo ($filtro == 'mes') ? 'selected' : ''; ?>>Este Mes</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <a href="exportar_csv.php?empleado_id=<?php echo $_GET['empleado_id']; ?>&filtro=<?php echo $filtro; ?>" class="btn btn-success w-100">Exportar a CSV</a>
                                    </div>
                                </form>
                            </div>
                        <?php endif; ?>

                        <!-- Registros del empleado seleccionado -->
                        <?php if (isset($_GET['empleado_id'])): ?>
                            <div class="mt-4">
                                <h4>Registros de <?php echo $empleado_seleccionado['nombre'] . ' ' . $empleado_seleccionado['apellido']; ?></h4>
                                <h7>Fono <?php echo $empleado_seleccionado['telefono'] . ' - Rut ' . $empleado_seleccionado['rut']; ?></h7>
                                
                                <!-- Pestañas -->
<ul class="nav nav-tabs" id="registrosTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="jornada-tab" data-bs-toggle="tab" data-bs-target="#jornada" type="button" role="tab">Jornada</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="espera-tab" data-bs-toggle="tab" data-bs-target="#espera" type="button" role="tab">Espera</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="descanso-tab" data-bs-toggle="tab" data-bs-target="#descanso" type="button" role="tab">Descanso</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="colacion-tab" data-bs-toggle="tab" data-bs-target="#colacion" type="button" role="tab">Colación</button>
    </li>
</ul>

<!-- Contenido de las pestañas -->
<div class="tab-content" id="registrosTabsContent">
    <!-- Pestaña Jornada -->
    <div class="tab-pane fade show active" id="jornada" role="tabpanel">
        <table class="table table-striped mt-3">
            <thead>
                <tr>
                    <th>Tipo</th>
                    <th>Fecha/Hora</th>
                    <th>Ubicación</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($registros as $registro): ?>
                    <?php if (in_array($registro['tipo'], ['inicio_jornada', 'fin_jornada'])): ?>
                        <tr>
                            <td><?= ucfirst(str_replace('_', ' ', $registro['tipo'])) ?></td>
                            <td><?= $registro['fecha_hora'] ?></td>
                            <td>
                                <?php if ($registro['latitud']): ?>
                                    <a href="https://maps.google.com?q=<?= $registro['latitud'] ?>,<?= $registro['longitud'] ?>" target="_blank">Ver</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Pestaña Descanso (nueva) -->
    <div class="tab-pane fade" id="descanso" role="tabpanel">
        <table class="table table-striped mt-3">
            <thead>
                <tr>
                    <th>Tipo</th>
                    <th>Fecha/Hora</th>
                    <th>Ubicación</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($registros as $registro): ?>
                    <?php if (in_array($registro['tipo'], ['inicio_descanso', 'fin_descanso'])): ?>
                        <tr>
                            <td><?= ucfirst(str_replace('_', ' ', $registro['tipo'])) ?></td>
                            <td><?= $registro['fecha_hora'] ?></td>
                            <td>
                                <?php if ($registro['latitud']): ?>
                                    <a href="https://maps.google.com?q=<?= $registro['latitud'] ?>,<?= $registro['longitud'] ?>" target="_blank">Ver</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Pestaña Colación (nueva) -->
    <div class="tab-pane fade" id="colacion" role="tabpanel">
        <table class="table table-striped mt-3">
            <thead>
                <tr>
                    <th>Tipo</th>
                    <th>Fecha/Hora</th>
                    <th>Ubicación</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($registros as $registro): ?>
                    <?php if (in_array($registro['tipo'], ['inicio_colacion', 'fin_colacion'])): ?>
                        <tr>
                            <td><?= ucfirst(str_replace('_', ' ', $registro['tipo'])) ?></td>
                            <td><?= $registro['fecha_hora'] ?></td>
                            <td>
                                <?php if ($registro['latitud']): ?>
                                    <a href="https://maps.google.com?q=<?= $registro['latitud'] ?>,<?= $registro['longitud'] ?>" target="_blank">Ver</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS y dependencias -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>
</html>
<?php
session_start();
require 'conexion.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Procesar eliminación si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['eliminar_id'])) {
    $id_eliminar = $_POST['eliminar_id'];
    
    try {
        // Primero eliminamos los registros asociados al empleado
        $conn->beginTransaction();
        
        $stmt = $conn->prepare("DELETE FROM registros WHERE usuario_id = ?");
        $stmt->execute([$id_eliminar]);
        
        // Luego eliminamos al empleado
        $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ? AND rol = 'empleado'");
        $stmt->execute([$id_eliminar]);
        
        $conn->commit();
        
        // Recargar la página para ver los cambios
        header("Location: gestion_empleados.php?empresa=" . ($_GET['empresa'] ?? ''));
        exit();
    } catch (PDOException $e) {
        $conn->rollBack();
        $error = "Error al eliminar empleado: " . $e->getMessage();
    }
}

// Obtener empresas para filtro
$empresas = $conn->query("SELECT * FROM empresas")->fetchAll(PDO::FETCH_ASSOC);
$empresa_filtro = $_GET['empresa'] ?? null;

// Construir consulta con filtro
$sql = "SELECT u.id, u.nombre, u.apellido, u.username, u.telefono, u.rut, e.nombre as empresa 
        FROM usuarios u 
        LEFT JOIN empresas e ON u.empresa_id = e.id 
        WHERE u.rol = 'empleado'";

$params = [];
if ($empresa_filtro) {
    $sql .= " AND u.empresa_id = ?";
    $params[] = $empresa_filtro;
}

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$empleados = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Empleados</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.0/font/bootstrap-icons.css">
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
    
    <div class="container mt-4">
        <h2>Gestión de Empleados</h2>
        
        <!-- Mostrar mensaje de error si existe -->
        <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        
        <!-- Filtro por empresa -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Filtrar por Empresa</label>
                        <select name="empresa" class="form-select">
                            <option value="">Todas las empresas</option>
                            <?php foreach ($empresas as $empresa): ?>
                            <option value="<?= $empresa['id'] ?>" <?= ($empresa_filtro == $empresa['id']) ? 'selected' : '' ?>>
                                <?= $empresa['nombre'] ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary mt-4">Filtrar</button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Listado de empleados -->
        <div class="card">
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Teléfono</th>
                            <th>Rut</th>
                            <th>Empresa</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($empleados as $empleado): ?>
                        <tr>
                            <td><?= $empleado['id'] ?></td>
                            <td><?= $empleado['nombre'] ?> <?= $empleado['apellido'] ?></td>
                            <td><?= $empleado['telefono'] ?></td>
                            <td><?= $empleado['rut'] ?></td>
                            <td><?= $empleado['empresa'] ?? 'Sin empresa' ?></td>
                            <td>
                                <a href="editar_empleado.php?id=<?= $empleado['id'] ?>" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i> Editar
                                </a>
                                <a href="ver_registros.php?empleado_id=<?= $empleado['id'] ?>" class="btn btn-sm btn-info">
                                    <i class="bi bi-clock-history"></i> Registros
                                </a>
                                <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#confirmarEliminar<?= $empleado['id'] ?>">
                                    <i class="bi bi-trash"></i> Eliminar
                                </button>
                                
                                <!-- Modal de Confirmación -->
                                <div class="modal fade" id="confirmarEliminar<?= $empleado['id'] ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Confirmar Eliminación</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                ¿Estás seguro de eliminar al empleado <?= $empleado['nombre'] ?> <?= $empleado['apellido'] ?>?<br>
                                                <strong>Esta acción eliminará todos sus registros y no se puede deshacer.</strong>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                <form method="POST" style="display:inline;">
                                                    <input type="hidden" name="eliminar_id" value="<?= $empleado['id'] ?>">
                                                    <button type="submit" class="btn btn-danger">Confirmar Eliminación</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS y dependencias -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>
</html>
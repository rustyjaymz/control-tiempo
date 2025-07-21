<?php
session_start();
require 'conexion.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] != 'admin') {
    header("Location: login.php");
    exit();
}

$id = $_GET['id'];
$empresas = $conn->query("SELECT * FROM empresas")->fetchAll(PDO::FETCH_ASSOC);

// Obtener datos del empleado
$stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$id]);
$empleado = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $username = $_POST['username'];
    $rut = $_POST['rut'];
    $telefono = $_POST['telefono'];
    $direccion = $_POST['direccion'];
    $cargo = $_POST['cargo'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $empresa_id = $_POST['empresa_id'];
    
    $sql = "UPDATE usuarios SET nombre = ?, apellido = ?, username = ?, rut = ?, telefono = ?, direccion = ?, cargo = ?, empresa_id = ?";
    $params = [$nombre, $apellido, $username, $rut, $telefono, $direccion, $cargo, $empresa_id];
    
    // Actualizar contraseña solo si se proporcionó una nueva
    if (!empty($_POST['password'])) {
        $sql .= ", password = ?";
        $params[] = password_hash($_POST['password'], PASSWORD_BCRYPT);
    }
    
    $sql .= " WHERE id = ?";
    $params[] = $id;
    
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    
    header("Location: gestion_empleados.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Empleado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
     <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="admin_panel.php">Volver al Home</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="gestion_empleados.php">Gestión de Empleados</a>
                <a class="nav-link" href="logout.php">Cerrar Sesión</a>
            </div>
        </div>
    </nav>
    <div class="container mt-4">
        <h2>Editar Empleado</h2>
        
        <div class="card">
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="nombre" class="form-control" value="<?= $empleado['nombre'] ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Apellido</label>
                        <input type="text" name="apellido" class="form-control" value="<?= $empleado['apellido'] ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Usuario</label>
                        <input type="text" name="username" class="form-control" value="<?= $empleado['username'] ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Teléfono</label>
                        <input type="text" name="telefono" class="form-control" value="<?= $empleado['telefono'] ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Dirección</label>
                        <input type="text" name="direccion" class="form-control" value="<?= $empleado['direccion'] ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Rut</label>
                        <input type="text" name="rut" class="form-control" value="<?= $empleado['rut'] ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Cargo</label>
                        <input type="text" name="cargo" class="form-control" value="<?= $empleado['cargo'] ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nueva Contraseña (dejar en blanco para no cambiar)</label>
                        <input type="password" name="password" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Empresa</label>
                        <select name="empresa_id" class="form-select" required>
                            <option value="">Seleccionar empresa</option>
                            <?php foreach ($empresas as $empresa): ?>
                            <option value="<?= $empresa['id'] ?>" <?= ($empresa['id'] == $empleado['empresa_id']) ? 'selected' : '' ?>>
                                <?= $empresa['nombre'] ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
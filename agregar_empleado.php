<?php
session_start();
require 'conexion.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] != 'admin') {
    header("Location: login.php");
    exit();
}

$empresas = $conn->query("SELECT * FROM empresas")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $username = $_POST['username'];
    $rut = $_POST['rut'];
    $telefono = $_POST['telefono'];
    $direccion = $_POST['direccion'];
    $cargo = $_POST['cargo'];
    $rol = 'empleado'; // Fijamos el rol como 'empleado' ya que es para agregar empleados
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $empresa_id = $_POST['empresa_id'];
    
    try {
        $stmt = $conn->prepare("INSERT INTO usuarios (nombre, apellido, username, password, rut, telefono, direccion, cargo, rol, empresa_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $stmt->execute([
            $nombre, 
            $apellido, 
            $username, 
            $password, 
            $rut, 
            $telefono, 
            $direccion, 
            $cargo, 
            $rol, 
            $empresa_id
        ]);
        
        header("Location: gestion_empleados.php");
        exit();
        
    } catch (PDOException $e) {
        // Log del error para diagnóstico
        error_log("Error al agregar empleado: " . $e->getMessage());
        die("Error al procesar la solicitud. Por favor intente nuevamente.");
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Empleado</title>
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
        <h2>Agregar Nuevo Empleado</h2>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="nombre" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Apellido</label>
                        <input type="text" name="apellido" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Usuario</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contraseña</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Rut</label>
                        <input type="text" name="rut" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Teléfono</label>
                        <input type="text" name="telefono" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Dirección</label>
                        <input type="text" name="direccion" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Cargo</label>
                        <input type="text" name="cargo" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Empresa</label>
                        <select name="empresa_id" class="form-select" required>
                            <option value="">Seleccionar empresa</option>
                            <?php foreach ($empresas as $empresa): ?>
                            <option value="<?= $empresa['id'] ?>"><?= $empresa['nombre'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
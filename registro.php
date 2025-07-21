<?php
session_start();
require 'conexion.php';

// Solo los administradores pueden registrar usuarios
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] != 'admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hasheamos la contraseña
    $rol = $_POST['rol'];

    $stmt = $conn->prepare("INSERT INTO usuarios (username, password, rol) VALUES (:username, :password, :rol)");
    $stmt->execute([
        'username' => $username,
        'password' => $password,
        'rol' => $rol
    ]);

    echo "Usuario registrado exitosamente.";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Usuarios</title>
</head>
<body>
    <h1>Registro de Usuarios</h1>
    <form action="registro.php" method="post">
        <label for="username">Usuario:</label>
        <input type="text" id="username" name="username" required>
        <br>
        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password" required>
        <br>
        <label for="rol">Rol:</label>
        <select id="rol" name="rol">
            <option value="admin">Administrador</option>
            <option value="empleado">Empleado</option>
        </select>
        <br>
        <button type="submit">Registrar</button>
    </form>
    <br>
    <a href="index.php">Volver al Inicio</a>
</body>
</html>
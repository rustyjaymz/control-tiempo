<?php
$host = 'localhost';
$dbname = 'happymen_control_tiempo';
$username = 'happymen_control_tiempo';
$password = 'ND)e=bsW,*wu';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
}
?>
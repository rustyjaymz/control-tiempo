<?php
session_start();
require 'conexion.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

// Obtener el filtro seleccionado
$filtro = isset($_GET['filtro']) ? $_GET['filtro'] : 'todos';

// Construir la consulta SQL según el filtro seleccionado
$sql = "SELECT tipo, fecha_hora, latitud, longitud FROM registros WHERE usuario_id = :usuario_id";
$params = ['usuario_id' => $usuario_id];

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
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Tiempo</title>
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
            <a class="navbar-brand" href="#">Control de Tiempo</a>

            <!-- Botón para colapsar el menú en móviles -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Menú colapsable -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
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
                        <h2 class="card-title text-center mb-4">Panel de Conductor</h2>

                        <!-- Botones para registrar tiempo -->
<form id="registroForm" class="d-grid gap-2 mb-4">
    <!-- Jornada -->
    <button type="button" onclick="registrarTiempo('inicio_jornada')" class="btn btn-primary">Iniciar Jornada</button>
    <button type="button" onclick="registrarTiempo('fin_jornada')" class="btn btn-danger">Terminar Jornada</button>
    
    <!-- Espera (existente) -->
    <button type="button" onclick="registrarTiempo('inicio_espera')" class="btn btn-warning">Iniciar Tiempo de Espera</button>
    <button type="button" onclick="registrarTiempo('fin_espera')" class="btn btn-info">Terminar Tiempo de Espera</button>
    
    <!-- Descanso (nuevo) -->
    <button type="button" onclick="registrarTiempo('inicio_descanso')" class="btn btn-secondary">Iniciar Descanso</button>
    <button type="button" onclick="registrarTiempo('fin_descanso')" class="btn btn-dark">Terminar Descanso</button>
    
    <!-- Colación (nuevo) -->
    <button type="button" onclick="registrarTiempo('inicio_colacion')" class="btn btn-success">Iniciar Colación</button>
    <button type="button" onclick="registrarTiempo('fin_colacion')" class="btn btn-light">Terminar Colación</button>
    
    <input type="hidden" id="latitud" name="latitud">
    <input type="hidden" id="longitud" name="longitud">
</form>

                        <!-- Mensaje de confirmación -->
                        <div id="mensaje" class="alert" style="display: none;"></div>
                        
                        <!-- Lista desplegable para filtrar registros -->
                        <div class="mb-4">
                            <h4>Filtrar Registros</h4>
                            <form method="GET" action="index.php" class="row g-3">
                                <div class="col-md-8">
                                    <select id="filtro" name="filtro" class="form-select" onchange="this.form.submit()">
                                        <option value="todos" <?php echo ($filtro == 'todos') ? 'selected' : ''; ?>>Todos los Registros</option>
                                        <option value="hoy" <?php echo ($filtro == 'hoy') ? 'selected' : ''; ?>>Hoy</option>
                                        <option value="semana" <?php echo ($filtro == 'semana') ? 'selected' : ''; ?>>Esta Semana</option>
                                        <option value="mes" <?php echo ($filtro == 'mes') ? 'selected' : ''; ?>>Este Mes</option>
                                    </select>
                                </div>
                            </form>
                        </div>

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
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS y dependencias -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

    <!-- Script para manejar el registro y la redirección -->
    <script>
    async function registrarTiempo(accion) {
        // Obtener la ubicación
        const ubicacion = await obtenerUbicacion();
        if (!ubicacion) {
            mostrarMensaje("No se pudo obtener la ubicación.", "danger");
            return;
        }

        // Crear el objeto de datos
        const datos = {
            accion: accion,
            latitud: ubicacion.latitud,
            longitud: ubicacion.longitud
        };

        // Enviar los datos al servidor usando Fetch API
        try {
            const respuesta = await fetch('registrar.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams(datos)
            });

            const resultado = await respuesta.json();

            if (resultado.success) {
                mostrarMensaje(resultado.message, "success");
                // Redirigir al usuario después de 2 segundos
                setTimeout(() => {
                    window.location.href = "index.php";
                }, 2000);
            } else {
                mostrarMensaje(resultado.message, "danger");
            }
        } catch (error) {
            mostrarMensaje("Error al conectar con el servidor.", "danger");
            console.error(error);
        }
    }

    function obtenerUbicacion() {
        return new Promise((resolve, reject) => {
            if (!navigator.geolocation) {
                reject("Tu navegador no soporta la geolocalización.");
                return;
            }

            navigator.geolocation.getCurrentPosition(
                (position) => {
                    resolve({
                        latitud: position.coords.latitude,
                        longitud: position.coords.longitude
                    });
                },
                (error) => {
                    reject("No se pudo obtener la ubicación.");
                    console.error(error);
                }
            );
        });
    }

    function mostrarMensaje(mensaje, tipo) {
        const mensajeDiv = document.getElementById('mensaje');
        mensajeDiv.textContent = mensaje;
        mensajeDiv.className = `alert alert-${tipo}`;
        mensajeDiv.style.display = 'block';
    }
    </script>
</body>
</html>
<?php
session_start();

// 1. Protección de ruta: si no hay sesión iniciada, redirigir al login
if (!isset($_SESSION['id'])) {
    header("Location: index.html");
    exit();
}

$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control - Biblioteca</title>
    <!-- Bootstrap CSS -->
    <link href="./wwwroot/css/bootstrap.min.css" rel="stylesheet">
    <!-- Tu archivo CSS con el fondo y la clase .login-box-transparente -->
    <link href="./wwwroot/css/style.css" rel="stylesheet">
    <!-- Iconos de Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        /* Ajuste extra para asegurar que las tarjetas se vean bien sobre el fondo */
        .card {
            transition: transform 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
    </style>
</head>
<body>

<!-- Barra de navegación superior -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow">
    <div class="container">
        <a class="navbar-brand" href="#">📚 Sistema Bibliotecario</a>
        <div class="ms-auto d-flex align-items-center">
            <span class="text-white me-3 d-none d-md-inline">
                Bienvenido, <strong><?php echo htmlspecialchars($username); ?></strong>
            </span>
            <a href="logout.php" class="btn btn-outline-danger btn-sm">
                <i class="bi bi-box-arrow-right"></i> Salir
            </a>
        </div>
    </div>
</nav>

<!-- Contenido Principal -->
<main id="main" class="container py-5 mt-4">
    <div class="text-center mb-5 text-white">
        <h1 class="display-4 fw-bold">Panel de Administración</h1>
        <p class="lead">Aqui puedes registrar libros, autores, prestamos y devoluciones.</p>
    </div>

    <div class="row g-4 justify-content-center">
        
        <!-- Tarjeta de Libros -->
        <div class="col-md-5 col-lg-4">
            <div class="card h-100 login-box-transparente text-center p-4 border-0 shadow">
                <div class="card-body">
                    <i class="bi bi-book fs-1 mb-3 d-block text-primary"></i>
                    <h5 class="card-title fw-bold">Libros</h5>
                    <p class="card-text text-muted">Añadir, editar y catalogar títulos.</p>
                    <a href="libros.php" class="btn btn-primary w-100 shadow-sm">Entrar</a>
                </div>
            </div>
        </div>

        <!-- Tarjeta de Autores -->
        <div class="col-md-5 col-lg-4">
            <div class="card h-100 login-box-transparente text-center p-4 border-0 shadow">
                <div class="card-body">
                    <i class="bi bi-person-lines-fill fs-1 mb-3 d-block text-primary"></i>
                    <h5 class="card-title fw-bold">Autores</h5>
                    <p class="card-text text-muted">Listado y registro de escritores.</p>
                    <a href="autores.php" class="btn btn-primary w-100 shadow-sm">Entrar</a>
                </div>
            </div>
        </div>

        <!-- Salto de línea para separar filas -->
        <div class="w-100"></div>

        <!-- Tarjeta de Préstamos -->
        <div class="col-md-5 col-lg-4">
            <div class="card h-100 login-box-transparente text-center p-4 border-0 shadow">
                <div class="card-body">
                    <i class="bi bi-arrow-left-right fs-1 mb-3 d-block text-success"></i>
                    <h5 class="card-title fw-bold text-success">Préstamos</h5>
                    <p class="card-text text-muted">Asignar libros a lectores registrados.</p>
                    <a href="prestamos.php" class="btn btn-success w-100 shadow-sm">Ver Préstamos</a>
                </div>
            </div>
        </div>

        <!-- Tarjeta de Devoluciones -->
        <div class="col-md-5 col-lg-4">
            <div class="card h-100 login-box-transparente text-center p-4 border-0 shadow">
                <div class="card-body">
                    <i class="bi bi-calendar-check fs-1 mb-3 d-block text-warning"></i>
                    <h5 class="card-title fw-bold text-warning">Devoluciones</h5>
                    <p class="card-text text-muted">Registro de entregas y penalizaciones.</p>
                    <a href="devoluciones.php" class="btn btn-warning w-100 shadow-sm">Gestionar</a>
                </div>
            </div>
        </div>

    </div>
</main>

<footer class="mt-auto py-3 text-center text-white-50">
    <div class="container">
        <small>© 2026 Biblioteca Central - Panel Administrativo</small>
    </div>
</footer>

</body>
</html>

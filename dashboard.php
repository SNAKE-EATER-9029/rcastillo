<?php
session_start();

// 1. Protección de ruta: si no hay sesión, mandarlo al login
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
    <title>Dashboard - Sistema de Biblioteca</title>
    <!-- Bootstrap CSS -->
    <link href="./wwwroot/css/bootstrap.min.css" rel="stylesheet">
    <!-- Tu CSS con el fondo y la transparencia -->
    <link href="./wwwroot/css/style.css" rel="stylesheet">
    <!-- Iconos de Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow">
    <div class="container">
        <a class="navbar-brand" href="#">📚 Mi Biblioteca</a>
        <div class="navbar-text text-white">
            Bienvenido, <strong><?php echo htmlspecialchars($username); ?></strong>
        </div>
        <a href="logout.php" class="btn btn-outline-danger btn-sm ms-3">Cerrar Sesión</a>
    </div>
</nav>

<main id="main" class="container py-5">
    <div class="row g-4 justify-content-center">
        
        <!-- Tarjeta de Libros -->
        <div class="col-md-4 col-sm-6">
            <div class="card h-100 login-box-transparente text-center p-4 border-0">
                <div class="card-body">
                    <i class="bi bi-journal-bookmark fs-1 mb-3 d-block text-primary"></i>
                    <h5 class="card-title">Libros</h5>
                    <p class="card-text text-muted small">Gestionar catálogo, títulos y páginas.</p>
                    <a href="libros.php" class="btn btn-primary w-100">Entrar</a>
                </div>
            </div>
        </div>

        <!-- Tarjeta de Usuarios -->
        <div class="col-md-4 col-sm-6">
            <div class="card h-100 login-box-transparente text-center p-4 border-0">
                <div class="card-body">
                    <i class="bi bi-people fs-1 mb-3 d-block text-primary"></i>
                    <h5 class="card-title">Usuarios</h5>
                    <p class="card-text text-muted small">Administrar lectores y sus correos.</p>
                    <a href="usuarios.php" class="btn btn-primary w-100">Entrar</a>
                </div>
            </div>
        </div>

        <!-- Tarjeta de Autores -->
        <div class="col-md-4 col-sm-6">
            <div class="card h-100 login-box-transparente text-center p-4 border-0">
                <div class="card-body">
                    <i class="bi bi-person-badge fs-1 mb-3 d-block text-primary"></i>
                    <h5 class="card-title">Autores</h5>
                    <p class="card-text text-muted small">Registrar y listar autores del sistema.</p>
                    <a href="autores.php" class="btn btn-primary w-100">Entrar</a>
                </div>
            </div>
        </div>

        <!-- Tarjeta de Préstamos -->
        <div class="col-md-4 col-sm-6">
            <div class="card h-100 login-box-transparente text-center p-4 border-0">
                <div class="card-body">
                    <i class="bi bi-arrow-left-right fs-1 mb-3 d-block text-success"></i>
                    <h5 class="card-title">Préstamos</h5>
                    <p class="card-text text-muted small">Control de libros prestados a usuarios.</p>
                    <a href="prestamos.php" class="btn btn-success w-100">Ver Préstamos</a>
                </div>
            </div>
        </div>

        <!-- Tarjeta de Devoluciones -->
        <div class="col-md-4 col-sm-6">
            <div class="card h-100 login-box-transparente text-center p-4 border-0">
                <div class="card-body">
                    <i class="bi bi-arrow-return-left fs-1 mb-3 d-block text-warning"></i>
                    <h5 class="card-title">Devoluciones</h5>
                    <p class="card-text text-muted small">Registro de libros regresados y estado.</p>
                    <a href="devoluciones.php" class="btn btn-warning w-100 text-dark">Gestionar</a>
                </div>
            </div>
        </div>

    </div>
</main>

<footer class="mt-auto py-3 text-center text-white">
    <small>© 2026 Sistema de Biblioteca - Panel de Administración</small>
</footer>

</body>
</html>

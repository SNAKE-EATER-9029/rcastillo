<?php
session_start();
require_once 'db.php'; // Tu archivo de conexión con PDO

// Protección de sesión
if (!isset($_SESSION['id'])) {
    header("Location: index.html");
    exit();
}

$db = conectarDB();
$mensaje = "";

// 1. Lógica para Insertar Libro
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registrar'])) {
    $titulo = $_POST['titulo'];
    $paginas = $_POST['paginas'];

    if (!empty($titulo) && !empty($paginas)) {
        try {
            $sql = "INSERT INTO libros (titulo, numero_de_paginas) VALUES (:titulo, :paginas)";
            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':titulo' => $titulo,
                ':paginas' => $paginas
            ]);
            $mensaje = "<div class='alert alert-success'>Libro registrado con éxito.</div>";
        } catch (PDOException $e) {
            $mensaje = "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
        }
    }
}

// 2. Obtener todos los libros para la tabla
$query = $db->query("SELECT * FROM libros ORDER BY id_libro DESC");
$libros = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Libros - Biblioteca</title>
    <link href="./wwwroot/css/bootstrap.min.css" rel="stylesheet">
    <link href="./wwwroot/css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">← Volver al Panel</a>
        <span class="navbar-text text-white">Gestión de Inventario</span>
    </div>
</nav>

<div class="container">
    <?php echo $mensaje; ?>

    <div class="row g-4">
        <!-- Formulario de Registro -->
        <div class="col-md-4">
            <div class="card login-box-transparente p-4 border-0 shadow text-white">
                <h4 class="mb-3"><i class="bi bi-plus-circle"></i> Nuevo Libro</h4>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Título del Libro</label>
                        <input type="text" name="titulo" class="form-control" placeholder="Ej: Cien años de soledad" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Número de Páginas</label>
                        <input type="number" name="paginas" class="form-control" placeholder="0" required>
                    </div>
                    <button type="submit" name="registrar" class="btn btn-primary w-100">
                        <i class="bi bi-save"></i> Guardar Libro
                    </button>
                </form>
            </div>
        </div>

        <!-- Tabla de Libros Registrados -->
        <div class="col-md-8">
            <div class="card login-box-transparente p-4 border-0 shadow text-white">
                <h4 class="mb-3"><i class="bi bi-table"></i> Libros en Inventario</h4>
                <div class="table-responsive">
                    <table class="table table-hover table-dark mt-2">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Título</th>
                                <th>Páginas</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($libros as $libro): ?>
                            <tr>
                                <td><?php echo $libro['id_libro']; ?></td>
                                <td><?php echo htmlspecialchars($libro['titulo']); ?></td>
                                <td><?php echo $libro['numero_de_paginas']; ?></td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-info"><i class="bi bi-pencil"></i></button>
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($libros)): ?>
                            <tr>
                                <td colspan="4" class="text-center">No hay libros registrados aún.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>

<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['id'])) {
    header("Location: index.html");
    exit();
}

$db = conectarDB();
$mensaje = "";

// 1. Lógica para Insertar Libro (Ahora con ID de Autor)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registrar'])) {
    $titulo = $_POST['titulo'];
    $paginas = $_POST['paginas'];
    $id_autor = $_POST['id_autor']; // Capturamos el autor seleccionado

    if (!empty($titulo) && !empty($paginas) && !empty($id_autor)) {
        try {
            // Insertamos el libro
            $sql = "INSERT INTO libros (titulo, numero_de_paginas) VALUES (:titulo, :paginas)";
            $stmt = $db->prepare($sql);
            $stmt->execute([':titulo' => $titulo, ':paginas' => $paginas]);
            
            // Obtenemos el ID del libro que se acaba de crear
            $id_libro = $db->lastInsertId();

            // Registramos la relación en la tabla intermedia autor_libro
            $sql_relacion = "INSERT INTO autor_libro (id_autor, id_libro) VALUES (:id_autor, :id_libro)";
            $stmt_rel = $db->prepare($sql_relacion);
            $stmt_rel->execute([':id_autor' => $id_autor, ':id_libro' => $id_libro]);

            $mensaje = "<div class='alert alert-success'>Libro y autor vinculados con éxito.</div>";
        } catch (PDOException $e) {
            $mensaje = "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
        }
    }
}

// 2. Obtener lista de autores para el SELECT del formulario
$query_autores = $db->query("SELECT id_autor, nombre FROM autores ORDER BY nombre ASC");
$lista_autores = $query_autores->fetchAll(PDO::FETCH_ASSOC);

// 3. Obtener libros con sus autores para la tabla (Usando JOIN)
$sql_tabla = "SELECT l.id_libro, l.titulo, l.numero_de_paginas, a.nombre AS autor 
              FROM libros l
              LEFT JOIN autor_libro al ON l.id_libro = al.id_libro
              LEFT JOIN autores a ON al.id_autor = a.id_autor
              ORDER BY l.id_libro DESC";
$query_libros = $db->query($sql_tabla);
$libros = $query_libros->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Libros</title>
    <link href="./wwwroot/css/bootstrap.min.css" rel="stylesheet">
    <link href="./wwwroot/css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">← Volver</a>
        <span class="navbar-text text-white">Inventario de Libros</span>
    </div>
</nav>

<div class="container">
    <?php echo $mensaje; ?>

    <div class="row g-4">
        <!-- Formulario -->
        <div class="col-md-4">
            <div class="card login-box-transparente p-4 border-0 shadow text-white">
                <h4 class="mb-3">Nuevo Libro</h4>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Título</label>
                        <input type="text" name="titulo" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Páginas</label>
                        <input type="number" name="paginas" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Seleccionar Autor</label>
                        <select name="id_autor" class="form-select" required>
                            <option value="">-- Seleccione un autor --</option>
                            <?php foreach ($lista_autores as $autor): ?>
                                <option value="<?php echo $autor['id_autor']; ?>">
                                    <?php echo htmlspecialchars($autor['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" name="registrar" class="btn btn-primary w-100">Guardar</button>
                </form>
            </div>
        </div>

        <!-- Tabla -->
        <div class="col-md-8">
            <div class="card login-box-transparente p-4 border-0 shadow text-white">
                <h4 class="mb-3">Libros Registrados</h4>
                <div class="table-responsive">
                    <table class="table table-hover table-dark mt-2">
                        <thead>
                            <tr>
                                <th>Título</th>
                                <th>Autor</th>
                                <th>Páginas</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($libros as $libro): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($libro['titulo']); ?></td>
                                <td><span class="badge bg-info text-dark"><?php echo htmlspecialchars($libro['autor'] ?? 'Sin autor'); ?></span></td>
                                <td><?php echo $libro['numero_de_paginas']; ?></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>

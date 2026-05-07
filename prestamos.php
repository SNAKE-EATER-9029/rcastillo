<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['id'])) {
    header("Location: index.html");
    exit();
}

$db = conectarDB();
$mensaje = "";

// 1. Registrar el préstamo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registrar_prestamo'])) {
    $id_libro = $_POST['id_libro'];
    $id_usuario = $_POST['id'];

    try {
        $sql = "INSERT INTO prestamos (id_libro, id_usuario) VALUES (:lib, :usu)";
        $stmt = $db->prepare($sql);
        $stmt->execute([':lib' => $id_libro, ':usu' => $id_usuario]);
        $mensaje = "<div class='alert alert-success shadow-sm'>✅ Préstamo registrado con éxito.</div>";
    } catch (PDOException $e) {
        $mensaje = "<div class='alert alert-danger'>❌ Error: " . $e->getMessage() . "</div>";
    }
}

// 2. Cargar Libros y Usuarios para los selectores
$libros = $db->query("SELECT id_libro, titulo FROM libros ORDER BY titulo ASC")->fetchAll(PDO::FETCH_ASSOC);
$usuarios = $db->query("SELECT id, nombre FROM usuarios ORDER BY nombre ASC")->fetchAll(PDO::FETCH_ASSOC);

// 3. Consultar historial con Nombres (Usando JOIN)
$sql_h = "SELECT p.id_prestamo, l.titulo, u.nombre, p.fecha_prestamo 
          FROM prestamos p
          JOIN libros l ON p.id_libro = l.id_libro
          JOIN usuarios u ON p.id_usuario = u.id
          ORDER BY p.fecha_prestamo DESC";
$historial = $db->query($sql_h)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Préstamos - Biblioteca</title>
    <link href="./wwwroot/css/bootstrap.min.css" rel="stylesheet">
    <link href="./wwwroot/css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body>

<nav class="navbar navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">← Panel Principal</a>
    </div>
</nav>

<div class="container">
    <?php echo $mensaje; ?>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card login-box-transparente p-4 border-0 text-white shadow">
                <h4 class="mb-3">Nuevo Préstamo</h4>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Libro</label>
                        <select name="id_libro" class="form-select bg-dark text-white border-secondary" required>
                            <option value="">-- Selecciona libro --</option>
                            <?php foreach ($libros as $l): ?>
                                <option value="<?= $l['id_libro'] ?>"><?= htmlspecialchars($l['titulo']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Usuario / Lector</label>
                        <select name="id_usuario" class="form-select bg-dark text-white border-secondary" required>
                            <option value="">-- Selecciona usuario --</option>
                            <?php foreach ($usuarios as $u): ?>
                                <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" name="registrar_prestamo" class="btn btn-primary w-100 fw-bold">Prestar Libro</button>
                </form>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card login-box-transparente p-4 border-0 text-white shadow text-center">
                <h4 class="mb-3">Libros Prestados Activos</h4>
                <div class="table-responsive">
                    <table class="table table-dark table-hover">
                        <thead>
                            <tr>
                                <th>Libro</th>
                                <th>Usuario</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($historial as $h): ?>
                            <tr>
                                <td><?= htmlspecialchars($h['titulo']) ?></td>
                                <td><?= htmlspecialchars($h['nombre']) ?></td>
                                <td class="small"><?= date('d/m/Y', strtotime($h['fecha_prestamo'])) ?></td>
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

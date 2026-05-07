<?php
// Reporte de errores para ver qué pasa si algo falla
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'db.php';

if (!isset($_SESSION['id'])) {
    header("Location: index.html");
    exit();
}

$db = conectarDB();
$mensaje = "";

// 1. PROCESAR EL REGISTRO DEL PRÉSTAMO
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registrar_prestamo'])) {
    $libro = $_POST['libro_id'];
    $usuario = $_POST['usuario_id'];

    if (!empty($libro) && !empty($usuario)) {
        try {
            // Insertamos usando los nombres exactos de tu captura: id_libro e id_usuario
            $sql = "INSERT INTO prestamos (id_libro, id_usuario) VALUES (:lib, :usu)";
            $stmt = $db->prepare($sql);
            $stmt->execute([':lib' => $libro, ':usu' => $usuario]);
            $mensaje = "<div class='alert alert-success shadow-sm'>✅ Préstamo registrado correctamente.</div>";
        } catch (PDOException $e) {
            $mensaje = "<div class='alert alert-danger'>❌ Error en la base de datos: " . $e->getMessage() . "</div>";
        }
    }
}

// 2. CARGAR DATOS PARA LOS SELECTORES
$usuarios_lista = $db->query("SELECT id, nombre FROM usuarios ORDER BY nombre ASC")->fetchAll(PDO::FETCH_ASSOC);
$libros_lista = $db->query("SELECT id_libro, titulo FROM libros ORDER BY titulo ASC")->fetchAll(PDO::FETCH_ASSOC);

// 3. CARGAR EL HISTORIAL (JOIN)
// Nota: p.id es la llave de prestamos según tu captura
$sql_h = "SELECT p.id, l.titulo, u.nombre, p.fecha_prestamo 
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

<nav class="navbar navbar-dark bg-dark mb-4 shadow">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">← Volver al Menú</a>
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
                        <label class="form-label">Seleccionar Libro</label>
                        <select name="libro_id" class="form-select bg-dark text-white border-secondary" required>
                            <option value="">-- Elige un libro --</option>
                            <?php foreach ($libros_lista as $l): ?>
                                <option value="<?= $l['id_libro'] ?>"><?= htmlspecialchars($l['titulo']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Seleccionar Lector</label>
                        <select name="usuario_id" class="form-select bg-dark text-white border-secondary" required>
                            <option value="">-- Elige un usuario --</option>
                            <?php foreach ($usuarios_lista as $u): ?>
                                <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <button type="submit" name="registrar_prestamo" class="btn btn-success w-100 fw-bold">Registrar Salida</button>
                </form>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card login-box-transparente p-4 border-0 text-white shadow">
                <h4 class="mb-3">Préstamos Realizados</h4>
                <div class="table-responsive">
                    <table class="table table-dark table-hover align-middle text-center">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Libro</th>
                                <th>Lector</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($historial as $h): ?>
                            <tr>
                                <td><?= $h['id'] ?></td>
                                <td class="text-start"><?= htmlspecialchars($h['titulo']) ?></td>
                                <td><?= htmlspecialchars($h['nombre']) ?></td>
                                <td class="small"><?= date('d/m/Y H:i', strtotime($h['fecha_prestamo'])) ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($historial)): ?>
                            <tr>
                                <td colspan="4" class="text-muted">No hay registros aún.</td>
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

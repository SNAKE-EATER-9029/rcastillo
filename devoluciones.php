<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['id'])) {
    header("Location: index.html");
    exit();
}

$db = conectarDB();
$mensaje = "";

// 1. PROCESAR LA DEVOLUCIÓN
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['procesar_devolucion'])) {
    $id_p = $_POST['id_prestamo'];
    $obs = $_POST['observaciones'];

    try {
        $db->beginTransaction();

        // A. Insertar en la tabla 'devoluciones' según tu captura
        $sql_ins = "INSERT INTO devoluciones (id_prestamo, observaciones) VALUES (:idp, :obs)";
        $stmt_ins = $db->prepare($sql_ins);
        $stmt_ins->execute([':idp' => $id_p, ':obs' => $obs]);

        // B. OPCIONAL: Borrar de la tabla préstamos para que el libro esté disponible
        // Si prefieres mantenerlo en préstamos, cambia esto por un UPDATE prestamos SET estado='Devuelto'
        $sql_del = "DELETE FROM prestamos WHERE id = :idp";
        $stmt_del = $db->prepare($sql_del);
        $stmt_del->execute([':idp' => $id_p]);

        $db->commit();
        $mensaje = "<div class='alert alert-success'>✅ Devolución registrada y libro liberado.</div>";
    } catch (Exception $e) {
        $db->rollBack();
        $mensaje = "<div class='alert alert-danger'>❌ Error: " . $e->getMessage() . "</div>";
    }
}

// 2. OBTENER PRÉSTAMOS ACTUALES (Para saber qué se puede devolver)
$sql_activos = "SELECT p.id, l.titulo, u.nombre 
                FROM prestamos p
                JOIN libros l ON p.id_libro = l.id_libro
                JOIN usuarios u ON p.id_usuario = u.id";
$prestamos = $db->query($sql_activos)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Devoluciones - Sistema Biblioteca</title>
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
        <div class="col-md-5">
            <div class="card login-box-transparente p-4 text-white border-0 shadow">
                <h4><i class="bi bi-arrow-down-left-circle"></i> Registrar Devolución</h4>
                <hr>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Libro Prestado</label>
                        <select name="id_prestamo" class="form-select bg-dark text-white border-secondary" required>
                            <option value="">-- Selecciona el préstamo --</option>
                            <?php foreach ($prestamos as $p): ?>
                                <option value="<?= $p['id'] ?>">
                                    <?= htmlspecialchars($p['titulo']) ?> (Prestado a: <?= htmlspecialchars($p['nombre']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Observaciones (Opcional)</label>
                        <textarea name="observaciones" class="form-control bg-dark text-white border-secondary" rows="3" placeholder="Ej: El libro regresó en buen estado."></textarea>
                    </div>

                    <button type="submit" name="procesar_devolucion" class="btn btn-warning w-100 fw-bold">
                        <i class="bi bi-check2-square"></i> Confirmar Devolución
                    </button>
                </form>
            </div>
        </div>

        <div class="col-md-7">
            <div class="card login-box-transparente p-4 text-white border-0 shadow">
                <h4><i class="bi bi-journal-check"></i> Últimas Devoluciones</h4>
                <div class="table-responsive mt-3">
                    <table class="table table-dark table-hover">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Observaciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $historial = $db->query("SELECT fecha_devolucion, observaciones FROM devoluciones ORDER BY id_devolucion DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($historial as $h): ?>
                            <tr>
                                <td class="small"><?= date('d/m/Y H:i', strtotime($h['fecha_devolucion'])) ?></td>
                                <td><?= htmlspecialchars($h['observaciones']) ?></td>
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

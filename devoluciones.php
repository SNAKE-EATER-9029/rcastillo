<?php
// Reporte de errores por si algo sale mal
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

// 1. PROCESAR LA DEVOLUCIÓN
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['procesar_devolucion'])) {
    $id_p = $_POST['id_prestamo'];
    $obs = $_POST['observaciones'];

    if (!empty($id_p)) {
        try {
            // Iniciamos una transacción para que se hagan ambas cosas o ninguna
            $db->beginTransaction();

            // A. Insertamos en la tabla 'devoluciones'
            $sql_ins = "INSERT INTO devoluciones (id_prestamo, observaciones) VALUES (:idp, :obs)";
            $stmt_ins = $db->prepare($sql_ins);
            $stmt_ins->execute([':idp' => $id_p, ':obs' => $obs]);

            // B. Marcamos el préstamo como 'Devuelto' (NO lo borramos para evitar el error 1451)
            // Asegúrate de haber ejecutado: ALTER TABLE prestamos ADD COLUMN estado VARCHAR(20) DEFAULT 'Activo';
            $sql_upd = "UPDATE prestamos SET estado = 'Devuelto' WHERE id = :idp";
            $stmt_upd = $db->prepare($sql_upd);
            $stmt_upd->execute([':idp' => $id_p]);

            $db->commit();
            $mensaje = "<div class='alert alert-success shadow-sm'>✅ Devolución registrada con éxito. El libro ya está disponible.</div>";
        } catch (Exception $e) {
            $db->rollBack();
            $mensaje = "<div class='alert alert-danger'>❌ Error: " . $e->getMessage() . "</div>";
        }
    }
}

// 2. OBTENER PRÉSTAMOS ACTIVOS (Solo los que no se han devuelto aún)
$sql_activos = "SELECT p.id, l.titulo, u.nombre 
                FROM prestamos p
                JOIN libros l ON p.id_libro = l.id_libro
                JOIN usuarios u ON p.id_usuario = u.id
                WHERE p.estado = 'Activo' OR p.estado IS NULL"; 
$prestamos = $db->query($sql_activos)->fetchAll(PDO::FETCH_ASSOC);

// 3. OBTENER HISTORIAL DE DEVOLUCIONES
$sql_historial = "SELECT d.fecha_devolucion, d.observaciones, l.titulo 
                  FROM devoluciones d
                  JOIN prestamos p ON d.id_prestamo = p.id
                  JOIN libros l ON p.id_libro = l.id_libro
                  ORDER BY d.id_devolucion DESC LIMIT 10";
$historial = $db->query($sql_historial)->fetchAll(PDO::FETCH_ASSOC);
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

<nav class="navbar navbar-dark bg-dark mb-4 shadow">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">← Volver al Dashboard</a>
    </div>
</nav>

<div class="container">
    <?php echo $mensaje; ?>
    
    <div class="row g-4">
        <div class="col-md-5">
            <div class="card login-box-transparente p-4 text-white border-0 shadow">
                <h4 class="mb-3 text-warning"><i class="bi bi-arrow-down-left-circle"></i> Retorno de Libro</h4>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Libro a devolver (Préstamos Activos)</label>
                        <select name="id_prestamo" class="form-select bg-dark text-white border-secondary" required>
                            <option value="">-- Selecciona el préstamo --</option>
                            <?php foreach ($prestamos as $p): ?>
                                <option value="<?= $p['id'] ?>">
                                    <?= htmlspecialchars($p['titulo']) ?> - (<?= htmlspecialchars($p['nombre']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Observaciones de la devolución</label>
                        <textarea name="observaciones" class="form-control bg-dark text-white border-secondary" rows="3" placeholder="Ej: Sin daños físicos, completo."></textarea>
                    </div>

                    <button type="submit" name="procesar_devolucion" class="btn btn-warning w-100 fw-bold">
                        Confirmar Recepción
                    </button>
                </form>
            </div>
        </div>

        <div class="col-md-7">
            <div class="card login-box-transparente p-4 text-white border-0 shadow">
                <h4 class="mb-3"><i class="bi bi-clock-history"></i> Últimos ingresos</h4>
                <div class="table-responsive">
                    <table class="table table-dark table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Libro</th>
                                <th>Notas</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($historial as $h): ?>
                            <tr>
                                <td class="small text-info"><?= date('d/m/Y H:i', strtotime($h['fecha_devolucion'])) ?></td>
                                <td><?= htmlspecialchars($h['titulo']) ?></td>
                                <td class="small italic"><?= htmlspecialchars($h['observaciones']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($historial)): ?>
                            <tr>
                                <td colspan="3" class="text-center text-muted">No hay devoluciones registradas.</td>
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

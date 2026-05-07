<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['id'])) { header("Location: index.html"); exit(); }

$db = conectarDB();
$mensaje = "";

// INSERTAR AUTOR
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registrar_autor'])) {
    $nombre = trim($_POST['nombre_autor']);
    if (!empty($nombre)) {
        try {
            $sql = "INSERT INTO autores (nombre) VALUES (:nom)";
            $stmt = $db->prepare($sql);
            $stmt->execute([':nom' => $nombre]);
            $mensaje = "<div class='alert alert-success shadow-sm'>✅ Autor registrado: $nombre</div>";
        } catch (PDOException $e) {
            $mensaje = "<div class='alert alert-danger'>❌ Error: " . $e->getMessage() . "</div>";
        }
    }
}

// OBTENER LISTA
$autores = $db->query("SELECT * FROM autores ORDER BY id_autor DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Autores | Sistema Biblioteca</title>
    <link href="./wwwroot/css/bootstrap.min.css" rel="stylesheet">
    <link href="./wwwroot/css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body>
    <nav class="navbar navbar-dark bg-dark mb-4 shadow">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">← Volver al Panel</a>
        </div>
    </nav>

    <div class="container">
        <?php echo $mensaje; ?>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card login-box-transparente p-4 border-0 text-white shadow">
                    <h4 class="mb-3">Registrar Escritor</h4>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Nombre Completo</label>
                            <input type="text" name="nombre_autor" class="form-control" required>
                        </div>
                        <button type="submit" name="registrar_autor" class="btn btn-primary w-100">Guardar Autor</button>
                    </form>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card login-box-transparente p-4 border-0 text-white shadow">
                    <h4 class="mb-3">Lista de Autores</h4>
                    <table class="table table-dark table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($autores as $a): ?>
                            <tr>
                                <td><?php echo $a['id_autor']; ?></td>
                                <td><?php echo htmlspecialchars($a['nombre']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['id'])) {
    header("Location: index.html");
    exit();
}

$db = conectarDB();
$mensaje = "";

// 1. REGISTRAR PRÉSTAMO
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registrar_prestamo'])) {
    // Estas son las variables que vienen del FORMULARIO (name="...")
    $id_libro_form = $_POST['libro_seleccionado'];
    $id_usuario_form = $_POST['usuario_seleccionado'];

    try {
        // En el INSERT usamos los nombres de tu tabla 'prestamos'
        $sql = "INSERT INTO prestamos (id_libro, id_usuario) VALUES (:lib, :usu)";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':lib' => $id_libro_form,
            ':usu' => $id_usuario_form
        ]);
        $mensaje = "<div class='alert alert-success'>✅ Préstamo realizado.</div>";
    } catch (PDOException $e) {
        // Esto te dirá el error real si vuelve a fallar
        $mensaje = "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
    }
}

// 2. CONSULTAS PARA LOS SELECTORES
// Aquí usamos 'id' porque así está en tu captura de la tabla usuarios
$usuarios = $db->query("SELECT id, nombre FROM usuarios")->fetchAll(PDO::FETCH_ASSOC);
$libros = $db->query("SELECT id_libro, titulo FROM libros")->fetchAll(PDO::FETCH_ASSOC);

// 3. CONSULTA PARA LA TABLA (JOIN)
$sql_tabla = "SELECT p.id_prestamo, l.titulo, u.nombre, p.fecha_prestamo 
              FROM prestamos p
              JOIN libros l ON p.id_libro = l.id_libro
              JOIN usuarios u ON p.id_usuario = u.id"; 
$historial = $db->query($sql_tabla)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Préstamos</title>
    <link href="./wwwroot/css/bootstrap.min.css" rel="stylesheet">
    <link href="./wwwroot/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="container py-5">
        <?php echo $mensaje; ?>
        
        <div class="row">
            <div class="col-md-4">
                <div class="card login-box-transparente p-4 text-white">
                    <form method="POST">
                        <label>Libro:</label>
                        <select name="libro_seleccionado" class="form-control mb-3" required>
                            <?php foreach($libros as $l): ?>
                                <option value="<?php echo $l['id_libro']; ?>"><?php echo $l['titulo']; ?></option>
                            <?php endforeach; ?>
                        </select>

                        <label>Usuario:</label>
                        <select name="usuario_seleccionado" class="form-control mb-3" required>
                            <?php foreach($usuarios as $u): ?>
                                <option value="<?php echo $u['id']; ?>"><?php echo $u['nombre']; ?></option>
                            <?php endforeach; ?>
                        </select>

                        <button type="submit" name="registrar_prestamo" class="btn btn-primary w-100">Prestar</button>
                    </form>
                </div>
            </div>

            <div class="col-md-8 text-white">
                <table class="table table-dark">
                    <thead>
                        <tr>
                            <th>Libro</th>
                            <th>Lector</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($historial as $h): ?>
                        <tr>
                            <td><?php echo $h['titulo']; ?></td>
                            <td><?php echo $h['nombre']; ?></td>
                            <td><?php echo $h['fecha_prestamo']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>

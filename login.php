<?php
// 1. Lógica de validación (PHP)
require_once 'db.php'; 

$error = ""; // Variable para mostrar mensajes de error

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $pwd = $_POST['pwd'];
    
    $db = conectarDB();

    try {
        $sql = "SELECT id, password, email FROM usuarios WHERE email = :email";
        $query = $db->prepare($sql);
        $query->execute(['email' => $email]);
        $usuario = $query->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {
            $verify = password_verify($pwd, $usuario['password']);
            if ($verify) {
                session_start();
                $_SESSION['username'] = $usuario['email'];
                $_SESSION['id'] = $usuario['id'];
                header("Location: dashboard.php");
                exit();
            } else {
                $error = "La contraseña es incorrecta.";
            }
        } else {
            $error = "No se encontró ninguna cuenta con ese correo.";
        }
    } catch (PDOException $e) {
        $error = "Error en la base de datos: " . $e->getMessage();
    }
}
?>

<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Biblioteca Digital - Iniciar Sesión</title>
    
    <link href="./wwwroot/css/bootstrap.min.css" rel="stylesheet">
    <link href="./wwwroot/css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="./wwwroot/css/bootstrap-icons.min.css">
    
    <script src="./wwwroot/js/jquery-4.0.0.min.js"></script>
    <script src="./wwwroot/js/script.js"></script>
</head>
<body>
    <main id="main">
        <div class="container-lg d-flex flex-column min-vh-100 justify-content-center">
            <div class="row align-items-center justify-content-center">
                <div class="col-md-7 col-lg-5">
                    
                    <div class="login-box-transparente p-5">
                        <div class="w-100 text-center mb-4">
                            <h1 class="display-6 fw-bold">Biblioteca Digital</h1>
                            <p class="subtitle">Inicia sesión para continuar</p>
                        </div>
                        
                        <?php if($error): ?>
                            <div class="alert alert-danger py-2 small"><?php echo $error; ?></div>
                        <?php endif; ?>

                        <form action="index.php" method="POST" class="text-start">
                            <div class="mb-3">
                                <label for="email" class="form-label">Correo electrónico</label>
                                <input type="email" class="form-control" name="email" id="email" placeholder="nombre@ejemplo.com" required>
                            </div>

                            <div class="mb-3">
                                <label for="pwd" class="form-label">Contraseña</label>
                                <input type="password" class="form-control" name="pwd" id="pwd" placeholder="••••••••" required>
                            </div>

                            <div class="d-grid gap-2 py-3">
                                <button type="submit" class="btn-principal">Inicia sesión</button>
                            </div>

                            <div class="text-center mt-2">
                                <span class="small text-muted">¿Aún no tienes cuenta?</span>
                                <a class="footer-link fw-bold" href="registro.php"> Crea tu cuenta</a>
                            </div>
                        </form>
                    </div> 
                    
                </div>
            </div>
        </div>
    </main>
</body>
</html>

<?php
// 1. Lógica de validación (PHP)
require_once 'db.php'; 

$error = ""; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Asegúrate de que estos nombres coincidan con los 'name' del formulario abajo
    $email = $_POST['email_form'];
    $pwd = $_POST['pwd_form'];
    
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
            $error = "No se encontró el correo electrónico.";
        }
    } catch (PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>

<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Biblioteca Digital - Login</title>
    <link href="./wwwroot/css/bootstrap.min.css" rel="stylesheet">
    <link href="./wwwroot/css/style.css" rel="stylesheet">
</head>
<body>
    <main id="main" class="d-flex align-items-center min-vh-100">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-md-6 col-lg-4">
                    
                    <div class="login-box-transparente p-5 text-center">
                        <h1 class="fw-bold mb-2">Biblioteca Digital</h1>
                        <p class="text-muted mb-4">Inicia sesión para continuar</p>

                        <?php if($error): ?>
                            <div class="alert alert-danger p-2 small"><?php echo $error; ?></div>
                        <?php endif; ?>

                        <form action="index.php" method="POST" class="text-start">
                            <div class="mb-3">
                                <label class="form-label">Correo electrónico</label>
                                <input type="email" name="email_form" class="form-control" placeholder="ejemplo@correo.com" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Contraseña</label>
                                <input type="password" name="pwd_form" class="form-control" placeholder="••••••••" required>
                            </div>

                            <button type="submit" class="btn-principal w-100 mt-3">Inicia sesión</button>
                        </form>

                        <div class="mt-4">
                            <p class="small">¿Aún no tienes cuenta? 
                                <a href="registro.php" class="fw-bold text-decoration-none">Crea tu cuenta</a>
                            </p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </main>
</body>
</html>

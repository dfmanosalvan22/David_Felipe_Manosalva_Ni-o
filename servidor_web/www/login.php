<?php
require_once 'config/bd.php';

if (session_status() === PHP_SESSION_NONE) session_start();

// Si ya está logueado, redirigir al dashboard
if (isset($_SESSION['usuario'])) {
    header("Location: dashboard.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    $pdo  = conectar();
    $stmt = $pdo->prepare("SELECT * FROM CLIENTES WHERE EMAIL_CLI = ?");
    $stmt->execute([$email]);
    $cliente = $stmt->fetch();

    if ($cliente && password_verify($password, $cliente['PASSWORD_HASH'])) {
        // Guardar solo el nombre en sesión, no toda la fila
        $_SESSION['usuario']    = $cliente['NOMBRE_CLI'];
        $_SESSION['id_cliente'] = $cliente['ID_CLIENTE'];
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Email o contraseña incorrectos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión - LogiTrans</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="CSS/style.css">
</head>
<body class="bg-light">

<?php include("header.php"); ?>

<div class="container mt-5 d-flex justify-content-center">
    <div class="card p-4 shadow-lg form-box" style="width:500px">

        <h2 class="fw-bold text-center mb-4 titulo-animado">Iniciar sesión</h2>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" required class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label">Contraseña</label>
                <input type="password" name="password" required class="form-control">
            </div>

            <button type="submit" class="btn btn-danger w-100 btn-animado">
                Entrar
            </button>

        </form>

        <p class="mt-3 text-center">
            ¿No tienes cuenta? <a href="registro.php">Regístrate</a>
        </p>

    </div>
</div>

</body>
</html>
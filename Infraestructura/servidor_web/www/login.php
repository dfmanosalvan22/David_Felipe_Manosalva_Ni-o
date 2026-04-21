<?php
require_once 'config/bd.php';
if (session_status() === PHP_SESSION_NONE) session_start();

// Si ya está logueado redirigir al dashboard
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="CSS/style.css">
</head>
<body class="bg-light">

<!-- ── NAVBAR ─────────────────────────────────────────── -->
<nav class="navbar navbar-expand-lg navbar-logitrans">
    <div class="container">

        <a class="navbar-brand" href="index.php">
            <img src="Imagenes/logo_sinfond.png" alt="LogiTrans S.A." height="35"
                 class="d-inline-block align-text-top me-2">LogiTrans
        </a>

        <button class="navbar-toggler border-secondary" type="button"
                data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon" style="filter: invert(1)"></span>
        </button>

        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav mx-auto gap-1">
                <li class="nav-item"><a class="nav-link" href="index.php#nosotros">Nosotros</a></li>
                <li class="nav-item"><a class="nav-link" href="index.php#servicios">Servicios</a></li>
                <li class="nav-item"><a class="nav-link" href="index.php#flota">Flota</a></li>
                <li class="nav-item"><a class="nav-link" href="index.php#contacto">Contacto</a></li>
            </ul>
            <div class="d-flex gap-2 align-items-center">
                <a href="registro.php" class="btn-nav-register">Registrarse</a>
            </div>
        </div>

    </div>
</nav>

<!-- ── FORMULARIO ─────────────────────────────────────── -->
<div class="container d-flex justify-content-center align-items-center" style="min-height: calc(100vh - 70px); padding: 40px 16px;">
    <div class="card p-4 shadow-lg form-box w-100" style="max-width: 460px;">

        <div class="text-center mb-4">
            <i class="bi bi-person-circle fs-1 text-danger"></i>
            <h2 class="fw-bold mt-2 titulo-animado">Iniciar sesión</h2>
            <p class="text-muted small">Accede con tu cuenta de LogiTrans</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-circle me-2"></i><?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <div class="mb-3">
                <label class="form-label fw-semibold">Email</label>
                <div class="input-group">
                    <span class="input-group-text bg-white">
                        <i class="bi bi-envelope text-danger"></i>
                    </span>
                    <input type="email" name="email" required class="form-control"
                           placeholder="tucorreo@ejemplo.com">
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Contraseña</label>
                <div class="input-group">
                    <span class="input-group-text bg-white">
                        <i class="bi bi-lock text-danger"></i>
                    </span>
                    <input type="password" name="password" required class="form-control"
                           placeholder="Tu contraseña">
                </div>
            </div>

            <button type="submit" class="btn btn-danger w-100 btn-animado fw-bold py-2">
                <i class="bi bi-box-arrow-in-right me-2"></i>Entrar
            </button>

        </form>

        <hr class="my-3">

        <p class="text-center mb-0 small">
            ¿No tienes cuenta?
            <a href="registro.php" class="text-danger fw-semibold">Regístrate aquí</a>
        </p>

        <p class="text-center mt-2 mb-0 small">
            <a href="index.php" class="text-muted">
                <i class="bi bi-arrow-left me-1"></i>Volver al inicio
            </a>
        </p>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
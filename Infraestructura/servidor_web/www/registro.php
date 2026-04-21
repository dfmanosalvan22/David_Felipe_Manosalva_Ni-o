<?php
require_once 'config/bd.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if (isset($_SESSION['usuario'])) {
    header("Location: dashboard.php");
    exit();
}

$error = '';
$exito = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre    = trim($_POST['nombre']);
    $direccion = trim($_POST['direccion']);
    $email     = trim($_POST['email']);
    $telefono  = trim($_POST['telefono']);
    $password  = $_POST['password'];

    if (!preg_match('/^[0-9]{9}$/', $telefono)) {
        $error = "El teléfono debe tener exactamente 9 números.";
    } else {
        $pdo  = conectar();
        $stmt = $pdo->prepare("SELECT ID_CLIENTE FROM CLIENTES WHERE EMAIL_CLI = ?");
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $error = "Ese email ya está registrado.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare(
                "INSERT INTO CLIENTES (NOMBRE_CLI, DIRECCION_CLI, EMAIL_CLI, TELEFONO_CLI, PASSWORD_HASH)
                 VALUES (?, ?, ?, ?, ?)"
            );
            $stmt->execute([$nombre, $direccion, $email, $telefono, $hash]);
            $exito = "Cuenta creada correctamente. Ya puedes iniciar sesión.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - LogiTrans</title>
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
                <a href="login.php" class="btn-nav-login">Iniciar sesión</a>
            </div>
        </div>

    </div>
</nav>

<!-- ── FORMULARIO ─────────────────────────────────────── -->
<div class="container d-flex justify-content-center align-items-center" style="min-height: calc(100vh - 70px); padding: 40px 16px;">
    <div class="card p-4 shadow-lg form-box w-100" style="max-width: 500px;">

        <div class="text-center mb-4">
            <i class="bi bi-person-plus fs-1 text-danger"></i>
            <h2 class="fw-bold mt-2 titulo-animado">Crear cuenta</h2>
            <p class="text-muted small">Regístrate para solicitar nuestros servicios</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-circle me-2"></i><?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if ($exito): ?>
            <div class="alert alert-success">
                <i class="bi bi-check-circle me-2"></i><?php echo $exito; ?>
            </div>
            <a href="login.php" class="btn btn-danger w-100 btn-animado fw-bold py-2">
                <i class="bi bi-box-arrow-in-right me-2"></i>Ir al inicio de sesión
            </a>
            <p class="text-center mt-3 mb-0 small">
                <a href="index.php" class="text-muted">
                    <i class="bi bi-arrow-left me-1"></i>Volver al inicio
                </a>
            </p>
        <?php else: ?>

            <form method="POST">

                <div class="mb-3">
                    <label class="form-label fw-semibold">Nombre completo</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="bi bi-person text-danger"></i>
                        </span>
                        <input type="text" name="nombre" required class="form-control"
                               placeholder="Tu nombre completo">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Dirección</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="bi bi-geo-alt text-danger"></i>
                        </span>
                        <input type="text" name="direccion" required class="form-control"
                               placeholder="Calle, número, ciudad">
                    </div>
                </div>

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

                <div class="mb-3">
                    <label class="form-label fw-semibold">Teléfono <span class="text-muted fw-normal">(9 dígitos)</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="bi bi-telephone text-danger"></i>
                        </span>
                        <input type="text" name="telefono" required class="form-control"
                               pattern="[0-9]{9}" maxlength="9" placeholder="600123456">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Contraseña</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="bi bi-lock text-danger"></i>
                        </span>
                        <input type="password" name="password" required class="form-control"
                               placeholder="Mínimo 6 caracteres">
                    </div>
                </div>

                <button type="submit" class="btn btn-danger w-100 btn-animado fw-bold py-2">
                    <i class="bi bi-person-check me-2"></i>Registrarse
                </button>

            </form>

            <hr class="my-3">

            <p class="text-center mb-0 small">
                ¿Ya tienes cuenta?
                <a href="login.php" class="text-danger fw-semibold">Inicia sesión aquí</a>
            </p>

            <p class="text-center mt-2 mb-0 small">
                <a href="index.php" class="text-muted">
                    <i class="bi bi-arrow-left me-1"></i>Volver al inicio
                </a>
            </p>

        <?php endif; ?>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
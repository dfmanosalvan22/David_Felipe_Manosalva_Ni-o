<?php
session_start();
require_once '../config/bd.php';

// Si ya está logueado como empleado, ir al panel
if (isset($_SESSION['empleado'])) {
    header("Location: index.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    $pdo  = conectar();

    // Buscar el empleado por email y comprobar que su puesto
    // sea uno de los autorizados: 9=Jefe Logistica, 10=Mozo Almacen,
    // 11=Transportista, 20=Jefe Conductores
    $stmt = $pdo->prepare(
        "SELECT ID_EMPLEADO, NOMBRE_EMP, APELLIDOS_EMP, PASSWORD_HASH, ID_PUESTO, EMAIL_EMP
         FROM EMPLEADOS
         WHERE EMAIL_EMP = ?
         AND ID_PUESTO IN (9, 10, 11, 20)"
    );
    $stmt->execute([$email]);
    $empleado = $stmt->fetch();

    if ($empleado && password_verify($password, $empleado['PASSWORD_HASH'])) {

        $_SESSION['empleado']    = $empleado['NOMBRE_EMP'] . ' ' . $empleado['APELLIDOS_EMP'];
        $_SESSION['id_empleado'] = $empleado['ID_EMPLEADO'];
        $_SESSION['id_puesto']   = $empleado['ID_PUESTO'];
	$_SESSION['email_empleado'] = $empleado['EMAIL_EMP'];

        header("Location: index.php");
        exit();

    } else {
        $error = "Credenciales incorrectas o no tienes permiso para acceder.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso empleados - LogiTrans</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body style="background: linear-gradient(135deg, #0d0d0d 0%, #1a1a2e 50%, #dc3545 100%); min-height: 100vh;">

<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh; padding: 40px 16px;">
    <div class="card shadow-lg p-4 form-box w-100" style="max-width: 440px;">

        <div class="text-center mb-4">
            <i class="bi bi-shield-lock fs-1 text-danger"></i>
            <h2 class="fw-bold mt-2 titulo-animado">LogiTrans S.A.</h2>
            <p class="text-muted small">Acceso exclusivo para empleados</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-circle me-2"></i><?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <div class="mb-3">
                <label class="form-label fw-semibold">Email corporativo</label>
                <div class="input-group">
                    <span class="input-group-text bg-white">
                        <i class="bi bi-envelope text-danger"></i>
                    </span>
                    <input type="email" name="email" required class="form-control"
                           placeholder="usuario@logitrans.local"
                           autocomplete="username">
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Contraseña</label>
                <div class="input-group">
                    <span class="input-group-text bg-white">
                        <i class="bi bi-lock text-danger"></i>
                    </span>
                    <input type="password" name="password" required class="form-control"
                           autocomplete="current-password">
                </div>
            </div>

            <button type="submit" class="btn btn-danger w-100 btn-animado fw-bold py-2">
                <i class="bi bi-box-arrow-in-right me-2"></i>Entrar
            </button>

        </form>

        <hr class="my-3">

        <p class="text-center mb-0 small text-muted">
            <a href="../index.php" class="text-muted">
                <i class="bi bi-arrow-left me-1"></i>Volver al inicio
            </a>
        </p>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

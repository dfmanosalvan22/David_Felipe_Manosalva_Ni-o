<?php
require_once 'config/bd.php';

if (session_status() === PHP_SESSION_NONE) session_start();

$error = '';
$exito = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre    = trim($_POST['nombre']);
    $direccion = trim($_POST['direccion']);
    $email     = trim($_POST['email']);
    $telefono  = trim($_POST['telefono']);
    $password  = $_POST['password'];

    // Validaciones antes de tocar la BD
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
    <link rel="stylesheet" href="CSS/style.css">
</head>
<body class="bg-light">

<?php include("header.php"); ?>

<div class="container mt-5 d-flex justify-content-center">
    <div class="card p-4 shadow-lg form-box" style="width:500px">

        <h2 class="fw-bold text-center mb-4 titulo-animado">Crear cuenta</h2>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($exito): ?>
            <div class="alert alert-success"><?php echo $exito; ?></div>
            <a href="login.php" class="btn btn-success w-100">Ir al login</a>
        <?php else: ?>

            <form method="POST">

                <div class="mb-3">
                    <label class="form-label">Nombre</label>
                    <input type="text" name="nombre" required class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Dirección</label>
                    <input type="text" name="direccion" required class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" required class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Teléfono (9 dígitos)</label>
                    <input type="text" name="telefono" required class="form-control"
                           pattern="[0-9]{9}" maxlength="9">
                </div>

                <div class="mb-3">
                    <label class="form-label">Contraseña</label>
                    <input type="password" name="password" required class="form-control">
                </div>

                <button type="submit" class="btn btn-danger w-100 btn-animado">
                    Registrarse
                </button>

            </form>

            <p class="mt-3 text-center">
                ¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a>
            </p>

        <?php endif; ?>

    </div>
</div>

</body>
</html>
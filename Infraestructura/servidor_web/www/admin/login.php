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
        "SELECT ID_EMPLEADO, NOMBRE_EMP, APELLIDOS_EMP, PASSWORD_HASH, ID_PUESTO
         FROM EMPLEADOS
         WHERE EMAIL_EMP = ?
         AND ID_PUESTO IN (9, 10, 11, 20)"
    );
    $stmt->execute([$email]);
    $empleado = $stmt->fetch();

    if ($empleado && password_verify($password, $empleado['PASSWORD_HASH'])) {

        // Guardar datos del empleado en sesion
        $_SESSION['empleado']    = $empleado['NOMBRE_EMP'] . ' ' . $empleado['APELLIDOS_EMP'];
        $_SESSION['id_empleado'] = $empleado['ID_EMPLEADO'];
        $_SESSION['id_puesto']   = $empleado['ID_PUESTO'];

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
    <link rel="stylesheet" href="../CSS/style.css">
</head>
<body class="bg-dark">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-lg p-4 form-box">

                <h2 class="fw-bold text-center mb-1">LogiTrans S.A.</h2>
                <p class="text-muted text-center mb-4">Acceso exclusivo para empleados</p>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST">

                    <div class="mb-3">
                        <label class="form-label">Email corporativo</label>
                        <input type="email" name="email" required class="form-control"
                               placeholder="usuario@logitrans.local"
                               autocomplete="username">
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Contrasena</label>
                        <input type="password" name="password" required class="form-control"
                               autocomplete="current-password">
                    </div>

                    <button type="submit" class="btn btn-danger w-100">
                        Entrar
                    </button>

                </form>

            </div>
        </div>
    </div>
</div>

</body>
</html>
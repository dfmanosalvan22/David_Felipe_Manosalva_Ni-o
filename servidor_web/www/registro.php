<?php
session_start();
require_once 'config/bd.php';

$error = '';
$exito = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre   = trim($_POST['nombre']);
    $email    = trim($_POST['email']);
    $telefono = trim($_POST['telefono']);
    $password = $_POST['password'];

    // Comprobar que no existe ese email ya
    $pdo  = conectar();
    $stmt = $pdo->prepare("SELECT ID_CLIENTE FROM CLIENTES WHERE EMAIL_CLI = ?");
    $stmt->execute([$email]);

    if ($stmt->fetch()) {
        $error = "Ese email ya está registrado.";
    } else {
        // Cifrar la contraseña antes de guardarla
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO CLIENTES (NOMBRE_CLI, EMAIL_CLI, TELEFONO_CLI, PASSWORD_HASH) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nombre, $email, $telefono, $hash]);

        $exito = "Cuenta creada correctamente. Ya puedes iniciar sesion.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro - LogiTrans</title>

</head>
<body class="bg-light">
    <?php include("header.php");?>

    <div class="container mt-5 d-flex justify-content-center formulario">
        <div class="card p-4 bg-white" style="width: 600px;">
            <h2 class="fw-bold fs-2">Crear cuenta</h2>

            <?php if ($error): ?>
                <p style="color:red"><?php echo $error; ?></p>
            <?php endif; ?>

            <?php if ($exito): ?>
                <p style="color:green"><?php echo $exito; ?></p>
                <a href="login.php">Ir al login</a>
            <?php else: ?>
                <form method="POST" action="registro.php">
                    <label class="form-label">Nombre:</label><br>
                    <input type="text" name="nombre" required class="form-control"><br><br>
                    
                    <label 
                    class="form-label">Dirección:</label><br>
                    <input type="text" name="telefono" class="form-control"><br><br>

                    <label class="form-label">Email:</label><br>
                    <input type="email" name="email" required class="form-control"><br><br>

                    <label class="form-label">Telefono:</label><br>
                    <input type="text" name="telefono" class="form-control"><br><br>

                    <label class="form-label">Contraseña:</label><br>
                    <input type="password" name="password" required class="form-control"><br><br>

                    <button type="submit" class="form-control bg-danger text-white">Registrarse</button>
                </form>
                <p>¿Ya tienes cuenta? <a href="login.php">Inicia sesion</a></p>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>
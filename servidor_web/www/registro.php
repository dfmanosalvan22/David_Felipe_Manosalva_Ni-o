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
<style>
    .formulario{
        text-align: center;
        margin-top: 50px;
    }
</style>
</head>
<body>
    <?php include("header.php");?>

    <div class="formulario">    
        <h1>LogiTrans S.A.</h1>
        <h2>Crear cuenta</h2>

        <?php if ($error): ?>
            <p style="color:red"><?php echo $error; ?></p>
        <?php endif; ?>

        <?php if ($exito): ?>
            <p style="color:green"><?php echo $exito; ?></p>
            <a href="login.php">Ir al login</a>
        <?php else: ?>
            <form method="POST" action="registro.php">
                <label>Nombre:</label><br>
                <input type="text" name="nombre" required><br><br>

                <label>Email:</label><br>
                <input type="email" name="email" required><br><br>

                <label>Telefono:</label><br>
                <input type="text" name="telefono"><br><br>

                <label>Contraseña:</label><br>
                <input type="password" name="password" required><br><br>

                <button type="submit">Registrarse</button>
            </form>
            <p>¿Ya tienes cuenta? <a href="login.php">Inicia sesion</a></p>
        <?php endif; ?>
    </div>
</body>
</html>
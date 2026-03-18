<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navbar</title>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include("header.php");?>

<?php
// Página principal de LogiTrans
// Comprueba la conexión con la base de datos y muestra información del servidor

require_once 'config/bd.php';

// Intentar conectar a la base de datos
$estado_bd = '';
$color_bd  = '';

try {
    $pdo = conectar();
    $estado_bd = 'Conexion correcta';
    $color_bd  = 'green';
} catch (Exception $e) {
    $estado_bd = 'Error: ' . $e->getMessage();
    $color_bd  = 'red';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>LogiTrans S.A.</title>
</head>
<body>
    <h1>LogiTrans S.A.</h1>
    <p>Bienvenido al sistema web de LogiTrans.</p>

    <h2>Estado del sistema</h2>
    <p>Servidor: <?php echo gethostname(); ?></p>
    <p>Base de datos: <span style="color:<?php echo $color_bd ?>">
        <?php echo $estado_bd; ?>
    </span></p>

</body>
</html>

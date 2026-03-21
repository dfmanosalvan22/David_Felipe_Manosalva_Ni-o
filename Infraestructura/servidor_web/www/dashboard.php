<?php
require_once 'config/bd.php';

if (session_status() === PHP_SESSION_NONE) session_start();

// Si no está logueado, redirigir al login
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

// Obtener los envíos del cliente
$pdo  = conectar();
$stmt = $pdo->prepare(
    "SELECT E.ID_ENVIO, E.ORIGEN, E.DESTINO, E.ESTADO_ENVIO, E.FECHA_ENVIO
     FROM ENVIOS E
     JOIN DETALLE_ENVIO DE ON E.ID_ENVIO = DE.ID_ENVIO
     JOIN MERCANCIA M ON DE.ID_MERCANCIA = M.ID_MERCANCIA
     WHERE M.ID_CLIENTE = ?
     ORDER BY E.FECHA_ENVIO DESC"
);
$stmt->execute([$_SESSION['id_cliente']]);
$envios = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi panel - LogiTrans</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="CSS/style.css">
</head>
<body class="bg-light">

<?php include("header.php"); ?>

<div class="container mt-5">

    <h2 class="fw-bold mb-4">
        Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario']); ?>
    </h2>

    <h4 class="mb-3">Tus envíos</h4>

    <?php if (empty($envios)): ?>
        <div class="alert alert-info">
            Aún no tienes envíos registrados.
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover shadow-sm">
                <thead class="table-dark">
                    <tr>
                        <th>Nº Envío</th>
                        <th>Origen</th>
                        <th>Destino</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($envios as $envio): ?>
                    <tr>
                        <td><?php echo $envio['ID_ENVIO']; ?></td>
                        <td><?php echo htmlspecialchars($envio['ORIGEN']); ?></td>
                        <td><?php echo htmlspecialchars($envio['DESTINO']); ?></td>
                        <td>
                            <?php
                            // Color según el estado
                            $estado = $envio['ESTADO_ENVIO'];
                            $color  = match($estado) {
                                'ENTREGADO'    => 'success',
                                'EN_TRANSITO'  => 'primary',
                                'PENDIENTE'    => 'warning',
                                'CANCELADO'    => 'danger',
                                default        => 'secondary'
                            };
                            ?>
                            <span class="badge bg-<?php echo $color; ?>">
                                <?php echo $estado; ?>
                            </span>
                        </td>
                        <td><?php echo $envio['FECHA_ENVIO']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

</div>

</body>
</html>
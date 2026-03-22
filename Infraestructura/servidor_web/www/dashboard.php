<?php
session_start();
require_once 'config/bd.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$pdo = conectar();

// Recoger las solicitudes del cliente
$stmt = $pdo->prepare(
    "SELECT ID_SOLICITUD, TIPO_SERVICIO, TIPO_MERCANCIA, 
            ORIGEN, DESTINO, ESTADO, CREATED_AT
     FROM SOLICITUDES
     WHERE ID_CLIENTE = ?
     ORDER BY CREATED_AT DESC"
);
$stmt->execute([$_SESSION['id_cliente']]);
$solicitudes = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi panel - LogiTrans</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="CSS/style.css">
</head>
<body class="bg-light">

<?php include("header.php"); ?>

<div class="container mt-5 mb-5">

    <h2 class="fw-bold mb-4">
        Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario']); ?>
    </h2>

    <!-- Botones de servicios -->
    <div class="row g-3 mb-5">
        <div class="col-md-3">
            <a href="solicitar.php?servicio=transporte" class="btn btn-outline-danger w-100">
                <i class="bi bi-truck me-2"></i>Transporte
            </a>
        </div>
        <div class="col-md-3">
            <a href="solicitar.php?servicio=almacenamiento" class="btn btn-outline-danger w-100">
                <i class="bi bi-building me-2"></i>Almacenamiento
            </a>
        </div>
        <div class="col-md-3">
            <a href="solicitar.php?servicio=urgente" class="btn btn-outline-danger w-100">
                <i class="bi bi-lightning-charge me-2"></i>Urgente
            </a>
        </div>
        <div class="col-md-3">
            <a href="solicitar.php?servicio=integral" class="btn btn-outline-danger w-100">
                <i class="bi bi-boxes me-2"></i>Integral
            </a>
        </div>
    </div>

    <h4 class="mb-3">Mis solicitudes</h4>

    <?php if (empty($solicitudes)): ?>
        <div class="alert alert-info">
            Aun no tienes solicitudes. Selecciona un servicio para empezar.
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover shadow-sm">
                <thead class="table-dark">
                    <tr>
                        <th>N° Solicitud</th>
                        <th>Servicio</th>
                        <th>Mercancia</th>
                        <th>Origen</th>
                        <th>Destino</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($solicitudes as $s): ?>
                    <tr>
                        <td>#<?php echo $s['ID_SOLICITUD']; ?></td>
                        <td><?php echo $s['TIPO_SERVICIO']; ?></td>
                        <td><?php echo htmlspecialchars($s['TIPO_MERCANCIA']); ?></td>
                        <td><?php echo htmlspecialchars($s['ORIGEN'] ?? '—'); ?></td>
                        <td><?php echo htmlspecialchars($s['DESTINO'] ?? '—'); ?></td>
                        <td>
                            <?php
                            $color = match($s['ESTADO']) {
                                'PENDIENTE'  => 'warning',
                                'REVISANDO'  => 'info',
                                'ACEPTADA'   => 'success',
                                'RECHAZADA'  => 'danger',
                                default      => 'secondary'
                            };
                            ?>
                            <span class="badge bg-<?php echo $color; ?>">
                                <?php echo $s['ESTADO']; ?>
                            </span>
                        </td>
                        <td><?php echo date('d/m/Y', strtotime($s['CREATED_AT'])); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

</div>

</body>
</html>

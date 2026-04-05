<?php
session_start();
require_once '../config/bd.php';

if (!isset($_SESSION['empleado'])) {
    header("Location: login.php");
    exit();
}

$pdo = conectar();

// Recoger el filtro de la URL si existe
$filtro = $_GET['filtro'] ?? '';

// Construir la consulta segun el filtro
if ($filtro && in_array($filtro, ['PENDIENTE', 'REVISANDO', 'ACEPTADA', 'RECHAZADA'])) {
    $stmt = $pdo->prepare(
        "SELECT S.ID_SOLICITUD, S.TIPO_SERVICIO, S.TIPO_MERCANCIA,
                S.DESCRIPCION, S.PESO_KG, S.VOLUMEN_M3,
                S.ORIGEN, S.DESTINO, S.OBSERVACIONES,
                S.ESTADO, S.CREATED_AT,
                C.NOMBRE_CLI, C.EMAIL_CLI, C.TELEFONO_CLI
         FROM SOLICITUDES S
         JOIN CLIENTES C ON S.ID_CLIENTE = C.ID_CLIENTE
         WHERE S.ESTADO = ?
         ORDER BY S.CREATED_AT DESC"
    );
    $stmt->execute([$filtro]);
} else {
    $stmt = $pdo->prepare(
        "SELECT S.ID_SOLICITUD, S.TIPO_SERVICIO, S.TIPO_MERCANCIA,
                S.DESCRIPCION, S.PESO_KG, S.VOLUMEN_M3,
                S.ORIGEN, S.DESTINO, S.OBSERVACIONES,
                S.ESTADO, S.CREATED_AT,
                C.NOMBRE_CLI, C.EMAIL_CLI, C.TELEFONO_CLI
         FROM SOLICITUDES S
         JOIN CLIENTES C ON S.ID_CLIENTE = C.ID_CLIENTE
         ORDER BY S.CREATED_AT DESC"
    );
    $stmt->execute();
}

$solicitudes = $stmt->fetchAll();

// Titulo segun filtro
$titulos = [
    'PENDIENTE' => 'Solicitudes pendientes',
    'REVISANDO' => 'Solicitudes en revision',
    'ACEPTADA'  => 'Solicitudes aceptadas',
    'RECHAZADA' => 'Solicitudes rechazadas',
    ''          => 'Todas las solicitudes',
];
$titulo = $titulos[$filtro] ?? 'Todas las solicitudes';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel empleados - LogiTrans</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body class="bg-light">

<!-- Botón para abrir sidebar -->
<div class="d-flex align-items-center bg-dark px-3 py-2">
    <button onclick="toggleSidebar()" class="btn btn-dark border-0 me-3">
        <i class="bi bi-list fs-4 text-white"></i>
    </button>
    <span class="text-white fw-bold">LogiTrans — Panel de Gestion</span>
</div>

<!-- Sidebar -->
<?php include("sidebar.php"); ?>

<!-- Contenido principal -->
<div class="contenido-principal">

    <h4 class="fw-bold mb-4"><?php echo $titulo; ?></h4>

    <?php if (empty($solicitudes)): ?>
        <div class="alert alert-info">No hay solicitudes en esta categoria.</div>
    <?php else: ?>

        <?php foreach ($solicitudes as $s): ?>
        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <div class="row">

                    <!-- Datos de la solicitud -->
                    <div class="col-md-8">

                        <div class="d-flex align-items-center gap-2 mb-2">
                            <h5 class="fw-bold mb-0">
                                #<?php echo $s['ID_SOLICITUD']; ?>
                                — <?php echo $s['TIPO_SERVICIO']; ?>
                            </h5>
                            <?php
                            $color = match($s['ESTADO']) {
                                'PENDIENTE' => 'warning',
                                'REVISANDO' => 'info',
                                'ACEPTADA'  => 'success',
                                'RECHAZADA' => 'danger',
                                default     => 'secondary'
                            };
                            ?>
                            <span class="badge bg-<?php echo $color; ?>">
                                <?php echo $s['ESTADO']; ?>
                            </span>
                        </div>

                        <p class="mb-1">
                            <strong>Mercancia:</strong>
                            <?php echo htmlspecialchars($s['TIPO_MERCANCIA']); ?>
                            <?php if ($s['DESCRIPCION']): ?>
                                — <?php echo htmlspecialchars($s['DESCRIPCION']); ?>
                            <?php endif; ?>
                        </p>

                        <?php if ($s['PESO_KG']): ?>
                        <p class="mb-1">
                            <strong>Peso:</strong> <?php echo $s['PESO_KG']; ?> kg
                            <?php if ($s['VOLUMEN_M3']): ?>
                                | <strong>Volumen:</strong> <?php echo $s['VOLUMEN_M3']; ?> m3
                            <?php endif; ?>
                        </p>
                        <?php endif; ?>

                        <?php if ($s['ORIGEN'] || $s['DESTINO']): ?>
                        <p class="mb-1">
                            <strong>Origen:</strong>
                            <?php echo htmlspecialchars($s['ORIGEN'] ?? '—'); ?>
                            | <strong>Destino:</strong>
                            <?php echo htmlspecialchars($s['DESTINO'] ?? '—'); ?>
                        </p>
                        <?php endif; ?>

                        <?php if ($s['OBSERVACIONES']): ?>
                        <p class="mb-1 text-muted small">
                            <strong>Observaciones:</strong>
                            <?php echo htmlspecialchars($s['OBSERVACIONES']); ?>
                        </p>
                        <?php endif; ?>

                        <p class="mb-0 text-muted small">
                            <?php echo date('d/m/Y H:i', strtotime($s['CREATED_AT'])); ?>
                        </p>

                    </div>

                    <!-- Datos del cliente y botones -->
                    <div class="col-md-4 d-flex flex-column justify-content-between">

                        <div class="mb-3">
                            <p class="mb-1">
                                <i class="bi bi-person me-1"></i>
                                <strong><?php echo htmlspecialchars($s['NOMBRE_CLI']); ?></strong>
                            </p>
                            <p class="mb-1 small text-muted">
                                <?php echo htmlspecialchars($s['EMAIL_CLI']); ?>
                            </p>
                            <p class="mb-0 small text-muted">
                                <?php echo htmlspecialchars($s['TELEFONO_CLI'] ?? '—'); ?>
                            </p>
                        </div>

                        <?php if (in_array($s['ESTADO'], ['PENDIENTE', 'REVISANDO'])): ?>
                        <div class="d-flex gap-2">
                            <a href="gestionar.php?id=<?php echo $s['ID_SOLICITUD']; ?>&accion=aceptar"
                               class="btn btn-success btn-sm w-100"
                               onclick="return confirm('Aceptar esta solicitud?')">
                                <i class="bi bi-check-lg me-1"></i>Aceptar
                            </a>
                            <a href="gestionar.php?id=<?php echo $s['ID_SOLICITUD']; ?>&accion=rechazar"
                               class="btn btn-danger btn-sm w-100"
                               onclick="return confirm('Rechazar esta solicitud?')">
                                <i class="bi bi-x-lg me-1"></i>Rechazar
                            </a>
                        </div>
                        <?php endif; ?>

                    </div>

                </div>
            </div>
        </div>
        <?php endforeach; ?>

    <?php endif; ?>

</div>

<script>
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');
    sidebar.classList.toggle('abierto');
    overlay.classList.toggle('visible');
}
</script>

</body>
</html>
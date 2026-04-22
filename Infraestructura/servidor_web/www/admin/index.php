<?php
session_start();
date_default_timezone_set('Europe/Madrid');
require_once '../config/bd.php';

if (!isset($_SESSION['empleado'])) {
    header("Location: login.php");
    exit();
}

$pdo = conectar();

// Recoger el filtro de la URL si existe
$filtro = $_GET['filtro'] ?? '';

// Construir la consulta segun el filtro
if ($filtro && in_array($filtro, ['PENDIENTE', 'ACEPTADA', 'RECHAZADA'])) {
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

// Mensajes agrupados por solicitud
$mensajes = [];
if (!empty($solicitudes)) {
    $ids  = implode(',', array_column($solicitudes, 'ID_SOLICITUD'));
    $msgs = $pdo->query(
        "SELECT ID_SOLICITUD, REMITENTE, MENSAJE, CREATED_AT
         FROM MENSAJES
         WHERE ID_SOLICITUD IN ($ids)
         ORDER BY CREATED_AT ASC"
    )->fetchAll();
    foreach ($msgs as $m) {
        $mensajes[$m['ID_SOLICITUD']][] = $m;
    }
}

$titulos = [
    'PENDIENTE' => 'Solicitudes pendientes',
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
    <style>
        .chat-box {
            max-height: 200px;
            overflow-y: auto;
            background: #f8f9fa;
            border-radius: 8px;
            padding: 12px;
        }
        .burbuja {
            max-width: 75%;
            padding: 8px 12px;
            border-radius: 12px;
            font-size: 0.88rem;
            margin-bottom: 8px;
        }
        .burbuja-empleado {
            background: #1a1a2e;
            color: #fff;
            margin-left: auto;
            border-bottom-right-radius: 2px;
        }
        .burbuja-cliente {
            background: #fff;
            border: 1px solid #dee2e6;
            color: #333;
            border-bottom-left-radius: 2px;
        }
        .burbuja-fecha {
            font-size: 0.72rem;
            opacity: 0.7;
            margin-top: 2px;
        }
    </style>
</head>
<body class="bg-light">

<!-- ── NAVBAR ─────────────────────────────────────────── -->
<nav class="navbar-logitrans d-flex align-items-center justify-content-between px-3 py-2">
    <div class="d-flex align-items-center gap-3">
        <button onclick="toggleSidebar()" class="btn btn-dark border-0 p-1">
            <i class="bi bi-list fs-4 text-white"></i>
        </button>
        <span class="text-white fw-bold">
            <img src="../Imagenes/logo_sinfond.png" alt="LogiTrans" height="35"
                 class="d-inline-block align-text-top me-2">LogiTrans — Panel de Gestión
        </span>
    </div>
    <div class="d-flex align-items-center gap-2">
        <span class="text-white-50 small d-none d-md-inline">
            <i class="bi bi-person-circle me-1"></i><?php echo htmlspecialchars($_SESSION['empleado']); ?>
        </span>
        <a href="logout.php" class="btn-nav-login">
            <i class="bi bi-box-arrow-right me-1"></i>Salir
        </a>
    </div>
</nav>

<!-- ── SIDEBAR ────────────────────────────────────────── -->
<?php include("sidebar.php"); ?>

<!-- ── CONTENIDO PRINCIPAL ────────────────────────────── -->
<div class="contenido-principal">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="fw-bold mb-0"><?php echo $titulo; ?></h4>
        <span class="badge bg-secondary"><?php echo count($solicitudes); ?> solicitudes</span>
    </div>

    <?php if (empty($solicitudes)): ?>
        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>No hay solicitudes en esta categoría.
        </div>
    <?php else: ?>

        <?php foreach ($solicitudes as $s): ?>
        <div class="card shadow-sm mb-3 solicitud-card" id="chat-<?php echo $s['ID_SOLICITUD']; ?>">
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
                            <strong>Mercancía:</strong>
                            <?php echo htmlspecialchars($s['TIPO_MERCANCIA']); ?>
                            <?php if ($s['DESCRIPCION']): ?>
                                — <?php echo htmlspecialchars($s['DESCRIPCION']); ?>
                            <?php endif; ?>
                        </p>

                        <?php if ($s['PESO_KG']): ?>
                        <p class="mb-1">
                            <strong>Peso:</strong> <?php echo $s['PESO_KG']; ?> kg
                            <?php if ($s['VOLUMEN_M3']): ?>
                                | <strong>Volumen:</strong> <?php echo $s['VOLUMEN_M3']; ?> m³
                            <?php endif; ?>
                        </p>
                        <?php endif; ?>

                        <?php if ($s['ORIGEN'] || $s['DESTINO']): ?>
                        <p class="mb-1">
                            <i class="bi bi-geo-alt me-1 text-danger"></i>
                            <strong>Origen:</strong>
                            <?php echo htmlspecialchars($s['ORIGEN'] ?? '—'); ?>
                            | <strong>Destino:</strong>
                            <?php echo htmlspecialchars($s['DESTINO'] ?? '—'); ?>
                        </p>
                        <?php endif; ?>

                        <?php if ($s['OBSERVACIONES']): ?>
                        <p class="mb-1 text-muted small">
                            <i class="bi bi-chat-left-text me-1"></i>
                            <strong>Observaciones:</strong>
                            <?php echo htmlspecialchars($s['OBSERVACIONES']); ?>
                        </p>
                        <?php endif; ?>

                        <p class="mb-0 text-muted small">
                            <i class="bi bi-calendar me-1"></i>
                            <?php echo date('d/m/Y H:i', strtotime($s['CREATED_AT'])); ?>
                        </p>

                    </div>

                    <!-- Datos del cliente y botones -->
                    <div class="col-md-4 d-flex flex-column justify-content-between">

                        <div class="cliente-info mb-3 p-3 rounded">
                            <p class="mb-1">
                                <i class="bi bi-person-fill me-1 text-danger"></i>
                                <strong><?php echo htmlspecialchars($s['NOMBRE_CLI']); ?></strong>
                            </p>
                            <p class="mb-1 small text-muted">
                                <i class="bi bi-envelope me-1"></i>
                                <?php echo htmlspecialchars($s['EMAIL_CLI']); ?>
                            </p>
                            <p class="mb-0 small text-muted">
                                <i class="bi bi-telephone me-1"></i>
                                <?php echo htmlspecialchars($s['TELEFONO_CLI'] ?? '—'); ?>
                            </p>
                        </div>

                        <?php if ($s['ESTADO'] === 'PENDIENTE'): ?>
                        <div class="d-flex gap-2">
                            <a href="gestionar.php?id=<?php echo $s['ID_SOLICITUD']; ?>&accion=aceptar"
                               class="btn btn-success btn-sm w-100"
                               onclick="return confirm('¿Aceptar esta solicitud?')">
                                <i class="bi bi-check-lg me-1"></i>Aceptar
                            </a>
                            <a href="gestionar.php?id=<?php echo $s['ID_SOLICITUD']; ?>&accion=rechazar"
                               class="btn btn-danger btn-sm w-100"
                               onclick="return confirm('¿Rechazar esta solicitud?')">
                                <i class="bi bi-x-lg me-1"></i>Rechazar
                            </a>
                        </div>
                        <?php endif; ?>

                    </div>

                </div>

                <!-- ── CHAT ──────────────────────────────── -->
                <hr class="mt-3 mb-2">
                <p class="small fw-semibold mb-2">
                    <i class="bi bi-chat-dots me-1 text-danger"></i>
                    Chat con <?php echo htmlspecialchars($s['NOMBRE_CLI']); ?>
                </p>

                <div class="chat-box mb-2">
                    <?php if (empty($mensajes[$s['ID_SOLICITUD']])): ?>
                        <p class="text-muted small text-center mb-0">
                            Sin mensajes. Puedes escribir al cliente desde aquí.
                        </p>
                    <?php else: ?>
                        <?php foreach ($mensajes[$s['ID_SOLICITUD']] as $m): ?>
                        <div class="d-flex <?php echo $m['REMITENTE'] === 'empleado' ? 'justify-content-end' : 'justify-content-start'; ?>">
                            <div class="burbuja <?php echo $m['REMITENTE'] === 'empleado' ? 'burbuja-empleado' : 'burbuja-cliente'; ?>">
                                <div><?php echo htmlspecialchars($m['MENSAJE']); ?></div>
                                <div class="burbuja-fecha">
                                    <?php echo $m['REMITENTE'] === 'empleado' ? 'Tú' : htmlspecialchars($s['NOMBRE_CLI']); ?>
                                    · <?php echo date('d/m H:i', strtotime($m['CREATED_AT'])); ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <form method="POST" action="../enviar_mensaje.php" class="d-flex gap-2">
                    <input type="hidden" name="id_solicitud" value="<?php echo $s['ID_SOLICITUD']; ?>">
                    <input type="text" name="mensaje" class="form-control form-control-sm"
                           placeholder="Escribe un mensaje al cliente..." required maxlength="500">
                    <button type="submit" class="btn btn-sm px-3"
                            style="background:#1a1a2e; color:#fff;">
                        <i class="bi bi-send"></i>
                    </button>
                </form>

            </div>
        </div>
        <?php endforeach; ?>

    <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('abierto');
    document.getElementById('overlay').classList.toggle('visible');
}
document.querySelectorAll('.chat-box').forEach(box => {
    box.scrollTop = box.scrollHeight;
});
</script>

</body>
</html>
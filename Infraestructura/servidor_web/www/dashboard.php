<?php
session_start();
require_once 'config/bd.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$pdo = conectar();

// Recoger solicitudes del cliente
$stmt = $pdo->prepare(
    "SELECT ID_SOLICITUD, TIPO_SERVICIO, TIPO_MERCANCIA,
            ORIGEN, DESTINO, ESTADO, CREATED_AT
     FROM SOLICITUDES
     WHERE ID_CLIENTE = ?
     ORDER BY CREATED_AT DESC"
);
$stmt->execute([$_SESSION['id_cliente']]);
$solicitudes = $stmt->fetchAll();

// Recoger envios del cliente (solo los que tienen solicitud aceptada)
$stmt2 = $pdo->prepare(
    "SELECT E.ID_ENVIO, E.ORIGEN, E.DESTINO, E.ESTADO_ENVIO, E.FECHA_ENVIO,
            EMP.NOMBRE_EMP, EMP.APELLIDOS_EMP,
            V.MATRICULA_VEHI, V.MARCA_VEHI, V.MODELO_VEHI,
            S.TIPO_SERVICIO, S.TIPO_MERCANCIA, S.ID_SOLICITUD
     FROM ENVIOS E
     JOIN SOLICITUDES S  ON E.ID_SOLICITUD = S.ID_SOLICITUD
     JOIN EMPLEADOS EMP  ON E.ID_EMPLEADO  = EMP.ID_EMPLEADO
     JOIN VEHICULOS V    ON E.ID_VEHICULO  = V.ID_VEHICULO
     WHERE S.ID_CLIENTE = ?
     ORDER BY E.FECHA_ENVIO DESC"
);
$stmt2->execute([$_SESSION['id_cliente']]);
$envios = $stmt2->fetchAll();

// Seccion activa del sidebar
$seccion = $_GET['seccion'] ?? 'solicitudes';
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
    <link rel="stylesheet" href="CSS/dashboard.css">
</head>
<body class="bg-light">

<!-- Navbar superior -->
<div class="d-flex align-items-center bg-dark px-3 py-2">
    <button onclick="toggleSidebar()" class="btn btn-dark border-0 me-3">
        <i class="bi bi-list fs-4 text-white"></i>
    </button>
    <span class="text-white fw-bold">LogiTrans — Mi panel</span>
</div>

<!-- Sidebar del cliente -->
<div class="sidebar" id="sidebar">

    <div class="sidebar-header">
        <span class="fw-bold">Mi cuenta</span>
        <button class="btn-cerrar" onclick="toggleSidebar()">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    <div class="sidebar-empleado">
        <i class="bi bi-person-circle fs-4"></i>
        <span class="small"><?php echo htmlspecialchars($_SESSION['usuario']); ?></span>
    </div>

    <hr class="sidebar-divider">

    <p class="sidebar-titulo">Mis servicios</p>

    <a href="dashboard.php?seccion=solicitudes"
       class="sidebar-link <?php echo $seccion === 'solicitudes' ? 'activo' : ''; ?>">
        <i class="bi bi-list-ul"></i> Mis solicitudes
        <?php if (count($solicitudes) > 0): ?>
            <span class="badge bg-secondary ms-auto"><?php echo count($solicitudes); ?></span>
        <?php endif; ?>
    </a>

    <a href="dashboard.php?seccion=envios"
       class="sidebar-link <?php echo $seccion === 'envios' ? 'activo' : ''; ?>">
        <i class="bi bi-truck"></i> Mis envios
        <?php if (count($envios) > 0): ?>
            <span class="badge bg-secondary ms-auto"><?php echo count($envios); ?></span>
        <?php endif; ?>
    </a>

    <hr class="sidebar-divider">

    <p class="sidebar-titulo">Solicitar servicio</p>

    <a href="solicitar.php?servicio=transporte" class="sidebar-link">
        <i class="bi bi-truck-front"></i> Transporte
    </a>
    <a href="solicitar.php?servicio=almacenamiento" class="sidebar-link">
        <i class="bi bi-building"></i> Almacenamiento
    </a>
    <a href="solicitar.php?servicio=urgente" class="sidebar-link">
        <i class="bi bi-lightning-charge"></i> Urgente
    </a>
    <a href="solicitar.php?servicio=integral" class="sidebar-link">
        <i class="bi bi-boxes"></i> Integral
    </a>

    <hr class="sidebar-divider">

    <p class="sidebar-titulo">Cuenta</p>

    <a href="perfil.php" class="sidebar-link <?php echo basename($_SERVER['PHP_SELF']) === 'perfil.php' ? 'activo' : ''; ?>">
        <i class="bi bi-person"></i> Mis datos
    </a>

    <a href="logout.php" class="sidebar-link text-danger">
        <i class="bi bi-box-arrow-left"></i> Cerrar sesion
    </a>

</div>

<div class="sidebar-overlay" id="overlay" onclick="toggleSidebar()"></div>

<!-- Contenido principal -->
<div class="contenido-principal">

    <?php if ($seccion === 'solicitudes'): ?>

        <h4 class="fw-bold mb-4">Mis solicitudes</h4>

        <?php if (empty($solicitudes)): ?>
            <div class="alert alert-info">
                Aun no tienes solicitudes.
                <a href="index.php#servicios" class="alert-link">Ver servicios disponibles</a>
            </div>
        <?php else: ?>
            <?php foreach ($solicitudes as $s): ?>
            <div class="card shadow-sm mb-3">
                <div class="card-body">

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
                    </p>

                    <?php if ($s['ORIGEN'] || $s['DESTINO']): ?>
                    <p class="mb-1">
                        <strong>Origen:</strong>
                        <?php echo htmlspecialchars($s['ORIGEN'] ?? '—'); ?>
                        | <strong>Destino:</strong>
                        <?php echo htmlspecialchars($s['DESTINO'] ?? '—'); ?>
                    </p>
                    <?php endif; ?>

                    <p class="mb-0 text-muted small">
                        <?php echo date('d/m/Y H:i', strtotime($s['CREATED_AT'])); ?>
                    </p>

                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>

    <?php elseif ($seccion === 'envios'): ?>

        <h4 class="fw-bold mb-4">Mis envios</h4>

        <?php if (empty($envios)): ?>
            <div class="alert alert-info">
                Aun no tienes envios activos. Cuando una solicitud sea aceptada
                aparecera aqui con toda la informacion.
            </div>
        <?php else: ?>
            <?php foreach ($envios as $e): ?>
            <div class="card shadow-sm mb-3">
                <div class="card-body">

                    <div class="d-flex align-items-center gap-2 mb-3">
                        <h5 class="fw-bold mb-0">Envio #<?php echo $e['ID_ENVIO']; ?></h5>
                        <?php
                        $color = match($e['ESTADO_ENVIO']) {
                            'PENDIENTE'   => 'warning',
                            'EN_TRANSITO' => 'primary',
                            'ENTREGADO'   => 'success',
                            'CANCELADO'   => 'danger',
                            default       => 'secondary'
                        };
                        ?>
                        <span class="badge bg-<?php echo $color; ?>">
                            <?php echo $e['ESTADO_ENVIO']; ?>
                        </span>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-1">
                                <strong>Servicio:</strong> <?php echo $e['TIPO_SERVICIO']; ?>
                            </p>
                            <p class="mb-1">
                                <strong>Mercancia:</strong>
                                <?php echo htmlspecialchars($e['TIPO_MERCANCIA']); ?>
                            </p>
                            <p class="mb-1">
                                <strong>Origen:</strong>
                                <?php echo htmlspecialchars($e['ORIGEN'] ?? '—'); ?>
                            </p>
                            <p class="mb-1">
                                <strong>Destino:</strong>
                                <?php echo htmlspecialchars($e['DESTINO'] ?? '—'); ?>
                            </p>
                            <p class="mb-0 text-muted small">
                                <strong>Fecha:</strong>
                                <?php echo date('d/m/Y', strtotime($e['FECHA_ENVIO'])); ?>
                            </p>
                        </div>

                        <div class="col-md-6">
                            <p class="mb-1">
                                <i class="bi bi-person me-1 text-danger"></i>
                                <strong>Conductor:</strong>
                                <?php echo htmlspecialchars($e['NOMBRE_EMP']); ?>
                                <?php echo htmlspecialchars($e['APELLIDOS_EMP']); ?>
                            </p>
                            <p class="mb-0">
                                <i class="bi bi-truck me-1 text-danger"></i>
                                <strong>Vehiculo:</strong>
                                <?php echo htmlspecialchars($e['MATRICULA_VEHI']); ?>
                                — <?php echo htmlspecialchars($e['MARCA_VEHI']); ?>
                                <?php echo htmlspecialchars($e['MODELO_VEHI']); ?>
                            </p>
                        </div>
                    </div>

                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>

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
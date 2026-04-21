<?php
session_start();
require_once '../config/bd.php';

if (!isset($_SESSION['empleado'])) {
    header("Location: login.php");
    exit();
}

$pdo = conectar();

// Filtros
$filtro_estado = $_GET['filtro'] ?? '';
$busqueda      = $_GET['buscar'] ?? '';
$filtro_fecha  = $_GET['fecha']  ?? '';

// Construir consulta con filtros dinamicos
$where  = [];
$params = [];

if ($filtro_estado && in_array($filtro_estado, ['PENDIENTE', 'EN_TRANSITO', 'ENTREGADO', 'CANCELADO'])) {
    $where[]  = "E.ESTADO_ENVIO = ?";
    $params[] = $filtro_estado;
}

if ($busqueda) {
    $where[]  = "(C.NOMBRE_CLI LIKE ? OR E.ORIGEN LIKE ? OR E.DESTINO LIKE ?)";
    $params[] = "%$busqueda%";
    $params[] = "%$busqueda%";
    $params[] = "%$busqueda%";
}

if ($filtro_fecha) {
    $where[]  = "E.FECHA_ENVIO = ?";
    $params[] = $filtro_fecha;
}

$condicion = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

$stmt = $pdo->prepare(
    "SELECT E.ID_ENVIO, E.ORIGEN, E.DESTINO, E.ESTADO_ENVIO, E.FECHA_ENVIO,
            EMP.NOMBRE_EMP, EMP.APELLIDOS_EMP,
            V.MATRICULA_VEHI, V.MARCA_VEHI, V.MODELO_VEHI,
            S.TIPO_SERVICIO, S.TIPO_MERCANCIA,
            C.NOMBRE_CLI, C.EMAIL_CLI
     FROM ENVIOS E
     JOIN EMPLEADOS EMP  ON E.ID_EMPLEADO  = EMP.ID_EMPLEADO
     JOIN VEHICULOS V    ON E.ID_VEHICULO  = V.ID_VEHICULO
     JOIN SOLICITUDES S  ON E.ID_SOLICITUD = S.ID_SOLICITUD
     JOIN CLIENTES C     ON S.ID_CLIENTE   = C.ID_CLIENTE
     $condicion
     ORDER BY E.FECHA_ENVIO DESC"
);
$stmt->execute($params);
$envios = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Envíos - LogiTrans</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body class="bg-light">

<!-- ── NAVBAR ─────────────────────────────────────────── -->
<nav class="navbar-logitrans d-flex align-items-center justify-content-between px-3 py-2">
    <div class="d-flex align-items-center gap-3">
        <button onclick="toggleSidebar()" class="btn btn-dark border-0 p-1">
            <i class="bi bi-list fs-4 text-white"></i>
        </button>
        <span class="text-white fw-bold">
            <img src="../Imagenes/logo_sinfond.png" alt="LogiTrans S.A." height="35" class="d-inline-block align-text-top me-2">LogiTrans — Gestión de Envíos
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
        <h4 class="fw-bold mb-0">Gestión de envíos</h4>
        <span class="badge bg-secondary"><?php echo count($envios); ?> envíos</span>
    </div>

    <!-- ── FILTROS ─────────────────────────────────────── -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-end">

                <div class="col-md-4">
                    <label class="form-label small fw-semibold mb-1">Buscar</label>
                    <input type="text" name="buscar" class="form-control form-control-sm"
                           placeholder="Cliente, origen o destino"
                           value="<?php echo htmlspecialchars($busqueda); ?>">
                </div>

                <div class="col-md-3">
                    <label class="form-label small fw-semibold mb-1">Estado</label>
                    <select name="filtro" class="form-select form-select-sm">
                        <option value="">Todos los estados</option>
                        <option value="PENDIENTE"   <?php echo $filtro_estado === 'PENDIENTE'   ? 'selected' : ''; ?>>Pendiente</option>
                        <option value="EN_TRANSITO" <?php echo $filtro_estado === 'EN_TRANSITO' ? 'selected' : ''; ?>>En tránsito</option>
                        <option value="ENTREGADO"   <?php echo $filtro_estado === 'ENTREGADO'   ? 'selected' : ''; ?>>Entregado</option>
                        <option value="CANCELADO"   <?php echo $filtro_estado === 'CANCELADO'   ? 'selected' : ''; ?>>Cancelado</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label small fw-semibold mb-1">Fecha</label>
                    <input type="date" name="fecha" class="form-control form-control-sm"
                           value="<?php echo htmlspecialchars($filtro_fecha); ?>">
                </div>

                <div class="col-md-1">
                    <button type="submit" class="btn btn-danger btn-sm w-100">
                        <i class="bi bi-search"></i>
                    </button>
                </div>

                <div class="col-md-1">
                    <a href="envios.php" class="btn btn-outline-secondary btn-sm w-100">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>

            </form>
        </div>
    </div>

    <!-- ── LISTA DE ENVÍOS ─────────────────────────────── -->
    <?php if (empty($envios)): ?>
        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>No hay envíos con esos criterios.
        </div>
    <?php else: ?>

        <?php foreach ($envios as $e): ?>
        <div class="card shadow-sm mb-3 solicitud-card">
            <div class="card-body">
                <div class="row">

                    <!-- Info del envío -->
                    <div class="col-md-8">

                        <div class="d-flex align-items-center gap-2 mb-2">
                            <h5 class="fw-bold mb-0">Envío #<?php echo $e['ID_ENVIO']; ?></h5>
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

                        <p class="mb-1">
                            <i class="bi bi-person me-1 text-danger"></i>
                            <strong>Cliente:</strong>
                            <?php echo htmlspecialchars($e['NOMBRE_CLI']); ?>
                            — <?php echo htmlspecialchars($e['EMAIL_CLI']); ?>
                        </p>

                        <p class="mb-1">
                            <i class="bi bi-box me-1 text-danger"></i>
                            <strong>Servicio:</strong> <?php echo $e['TIPO_SERVICIO']; ?>
                            | <strong>Mercancía:</strong>
                            <?php echo htmlspecialchars($e['TIPO_MERCANCIA']); ?>
                        </p>

                        <p class="mb-1">
                            <i class="bi bi-geo-alt me-1 text-danger"></i>
                            <strong>Origen:</strong>
                            <?php echo htmlspecialchars($e['ORIGEN'] ?? '—'); ?>
                            | <strong>Destino:</strong>
                            <?php echo htmlspecialchars($e['DESTINO'] ?? '—'); ?>
                        </p>

                        <p class="mb-1">
                            <i class="bi bi-person-badge me-1 text-danger"></i>
                            <strong>Conductor:</strong>
                            <?php echo htmlspecialchars($e['NOMBRE_EMP']); ?>
                            <?php echo htmlspecialchars($e['APELLIDOS_EMP']); ?>
                            | <i class="bi bi-truck me-1 text-danger"></i>
                            <strong>Vehículo:</strong>
                            <?php echo htmlspecialchars($e['MATRICULA_VEHI']); ?>
                            — <?php echo htmlspecialchars($e['MARCA_VEHI']); ?>
                            <?php echo htmlspecialchars($e['MODELO_VEHI']); ?>
                        </p>

                        <p class="mb-0 text-muted small">
                            <i class="bi bi-calendar me-1"></i>
                            <strong>Fecha:</strong>
                            <?php echo date('d/m/Y', strtotime($e['FECHA_ENVIO'])); ?>
                        </p>

                    </div>

                    <!-- Cambiar estado -->
                    <div class="col-md-4 d-flex flex-column justify-content-center">

                        <div class="cliente-info p-3 rounded">
                            <p class="fw-bold small mb-2">
                                <i class="bi bi-arrow-repeat me-1 text-danger"></i>Cambiar estado:
                            </p>

                            <form method="POST" action="cambiar_estado.php">
                                <input type="hidden" name="id_envio"
                                       value="<?php echo $e['ID_ENVIO']; ?>">

                                <select name="nuevo_estado" class="form-select form-select-sm mb-2">
                                    <option value="PENDIENTE"
                                        <?php echo $e['ESTADO_ENVIO'] === 'PENDIENTE'   ? 'selected' : ''; ?>>
                                        Pendiente
                                    </option>
                                    <option value="EN_TRANSITO"
                                        <?php echo $e['ESTADO_ENVIO'] === 'EN_TRANSITO' ? 'selected' : ''; ?>>
                                        En tránsito
                                    </option>
                                    <option value="ENTREGADO"
                                        <?php echo $e['ESTADO_ENVIO'] === 'ENTREGADO'   ? 'selected' : ''; ?>>
                                        Entregado
                                    </option>
                                    <option value="CANCELADO"
                                        <?php echo $e['ESTADO_ENVIO'] === 'CANCELADO'   ? 'selected' : ''; ?>>
                                        Cancelado
                                    </option>
                                </select>

                                <button type="submit" class="btn btn-danger btn-sm w-100 btn-animado">
                                    <i class="bi bi-check-lg me-1"></i>Actualizar estado
                                </button>

                            </form>
                        </div>

                    </div>

                </div>
            </div>
        </div>
        <?php endforeach; ?>

    <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
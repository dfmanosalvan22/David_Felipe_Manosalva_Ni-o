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
    <title>Envios - LogiTrans</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../CSS/style.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body class="bg-light">

<div class="d-flex align-items-center bg-dark px-3 py-2">
    <button onclick="toggleSidebar()" class="btn btn-dark border-0 me-3">
        <i class="bi bi-list fs-4 text-white"></i>
    </button>
    <span class="text-white fw-bold">LogiTrans — Envios</span>
</div>

<?php include("sidebar.php"); ?>

<div class="contenido-principal">

    <h4 class="fw-bold mb-4">Gestion de envios</h4>

    <!-- Buscador y filtros -->
    <form method="GET" class="row g-2 mb-4">

        <div class="col-md-4">
            <input type="text" name="buscar" class="form-control"
                   placeholder="Buscar por cliente, origen o destino"
                   value="<?php echo htmlspecialchars($busqueda); ?>">
        </div>

        <div class="col-md-3">
            <select name="filtro" class="form-select">
                <option value="">Todos los estados</option>
                <option value="PENDIENTE"   <?php echo $filtro_estado === 'PENDIENTE'   ? 'selected' : ''; ?>>Pendiente</option>
                <option value="EN_TRANSITO" <?php echo $filtro_estado === 'EN_TRANSITO' ? 'selected' : ''; ?>>En transito</option>
                <option value="ENTREGADO"   <?php echo $filtro_estado === 'ENTREGADO'   ? 'selected' : ''; ?>>Entregado</option>
                <option value="CANCELADO"   <?php echo $filtro_estado === 'CANCELADO'   ? 'selected' : ''; ?>>Cancelado</option>
            </select>
        </div>

        <div class="col-md-3">
            <input type="date" name="fecha" class="form-control"
                   value="<?php echo htmlspecialchars($filtro_fecha); ?>">
        </div>

        <div class="col-md-1">
            <button type="submit" class="btn btn-danger w-100">
                <i class="bi bi-search"></i>
            </button>
        </div>

        <div class="col-md-1">
            <a href="envios.php" class="btn btn-outline-secondary w-100">
                <i class="bi bi-x-lg"></i>
            </a>
        </div>

    </form>

    <?php if (empty($envios)): ?>
        <div class="alert alert-info">No hay envios con esos criterios.</div>
    <?php else: ?>

        <?php foreach ($envios as $e): ?>
        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <div class="row">

                    <!-- Info del envio -->
                    <div class="col-md-8">

                        <div class="d-flex align-items-center gap-2 mb-2">
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

                        <p class="mb-1">
                            <strong>Cliente:</strong>
                            <?php echo htmlspecialchars($e['NOMBRE_CLI']); ?>
                            — <?php echo htmlspecialchars($e['EMAIL_CLI']); ?>
                        </p>

                        <p class="mb-1">
                            <strong>Servicio:</strong> <?php echo $e['TIPO_SERVICIO']; ?>
                            | <strong>Mercancia:</strong>
                            <?php echo htmlspecialchars($e['TIPO_MERCANCIA']); ?>
                        </p>

                        <p class="mb-1">
                            <strong>Origen:</strong>
                            <?php echo htmlspecialchars($e['ORIGEN'] ?? '—'); ?>
                            | <strong>Destino:</strong>
                            <?php echo htmlspecialchars($e['DESTINO'] ?? '—'); ?>
                        </p>

                        <p class="mb-1">
                            <strong>Conductor:</strong>
                            <?php echo htmlspecialchars($e['NOMBRE_EMP']); ?>
                            <?php echo htmlspecialchars($e['APELLIDOS_EMP']); ?>
                            | <strong>Vehiculo:</strong>
                            <?php echo htmlspecialchars($e['MATRICULA_VEHI']); ?>
                            — <?php echo htmlspecialchars($e['MARCA_VEHI']); ?>
                            <?php echo htmlspecialchars($e['MODELO_VEHI']); ?>
                        </p>

                        <p class="mb-0 text-muted small">
                            <strong>Fecha:</strong>
                            <?php echo date('d/m/Y', strtotime($e['FECHA_ENVIO'])); ?>
                        </p>

                    </div>

                    <!-- Cambiar estado -->
                    <div class="col-md-4 d-flex flex-column justify-content-center">

                        <p class="fw-bold small mb-2">Cambiar estado:</p>

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
                                    En transito
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

                            <button type="submit" class="btn btn-dark btn-sm w-100">
                                Actualizar estado
                            </button>

                        </form>

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
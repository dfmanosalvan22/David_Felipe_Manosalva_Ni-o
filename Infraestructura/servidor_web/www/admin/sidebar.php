<?php
$contadores = $pdo->query(
    "SELECT ESTADO, COUNT(*) as total
     FROM SOLICITUDES
     GROUP BY ESTADO"
)->fetchAll(PDO::FETCH_KEY_PAIR);

$pendientes = $contadores['PENDIENTE'] ?? 0;
$revisando  = $contadores['REVISANDO'] ?? 0;
$aceptadas  = $contadores['ACEPTADA']  ?? 0;
$rechazadas = $contadores['RECHAZADA'] ?? 0;

$pagina_actual = basename($_SERVER['PHP_SELF']);
$filtro_actual = $_GET['filtro'] ?? '';
?>

<div class="sidebar" id="sidebar">

    <div class="sidebar-header">
        <span class="fw-bold">LogiTrans</span>
        <button class="btn-cerrar" onclick="toggleSidebar()">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    <div class="sidebar-empleado">
        <i class="bi bi-person-circle fs-4"></i>
        <span class="small"><?php echo htmlspecialchars($_SESSION['empleado']); ?></span>
    </div>

    <hr class="sidebar-divider">

    <!-- SOLICITUDES -->
    <p class="sidebar-titulo">Solicitudes</p>

    <a href="index.php" class="sidebar-link <?php echo ($pagina_actual === 'index.php' && !$filtro_actual) ? 'activo' : ''; ?>">
        <i class="bi bi-list-ul"></i> Todas
    </a>

    <a href="index.php?filtro=PENDIENTE" class="sidebar-link <?php echo $filtro_actual === 'PENDIENTE' ? 'activo' : ''; ?>">
        <i class="bi bi-clock"></i> Pendientes
        <?php if ($pendientes > 0): ?>
            <span class="badge bg-warning text-dark ms-auto"><?php echo $pendientes; ?></span>
        <?php endif; ?>
    </a>

    <a href="index.php?filtro=ACEPTADA" class="sidebar-link <?php echo $filtro_actual === 'ACEPTADA' ? 'activo' : ''; ?>">
        <i class="bi bi-check-circle"></i> Aceptadas
        <?php if ($aceptadas > 0): ?>
            <span class="badge bg-success ms-auto"><?php echo $aceptadas; ?></span>
        <?php endif; ?>
    </a>

    <a href="index.php?filtro=RECHAZADA" class="sidebar-link <?php echo $filtro_actual === 'RECHAZADA' ? 'activo' : ''; ?>">
        <i class="bi bi-x-circle"></i> Rechazadas
        <?php if ($rechazadas > 0): ?>
            <span class="badge bg-danger ms-auto"><?php echo $rechazadas; ?></span>
        <?php endif; ?>
    </a>

    <hr class="sidebar-divider">

    <!-- ENVIOS -->
    <p class="sidebar-titulo">Envios</p>

    <a href="envios.php" class="sidebar-link <?php echo $pagina_actual === 'envios.php' ? 'activo' : ''; ?>">
        <i class="bi bi-truck"></i> Todos los envios
    </a>

    <hr class="sidebar-divider">

</div>

<div class="sidebar-overlay" id="overlay" onclick="toggleSidebar()"></div>

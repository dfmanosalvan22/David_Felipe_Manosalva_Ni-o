<?php
session_start();
require_once 'config/bd.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$pdo   = conectar();
$exito = '';
$error = '';

$stmt = $pdo->prepare(
    "SELECT NOMBRE_CLI, DIRECCION_CLI, EMAIL_CLI, TELEFONO_CLI
     FROM CLIENTES WHERE ID_CLIENTE = ?"
);
$stmt->execute([$_SESSION['id_cliente']]);
$cliente = $stmt->fetch();

if (!$cliente) {
    header("Location: logout.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre    = trim($_POST['nombre']);
    $direccion = trim($_POST['direccion']);
    $email     = trim($_POST['email']);
    $telefono  = trim($_POST['telefono']);
    $pass_actual    = $_POST['password_actual']    ?? '';
    $pass_nueva     = $_POST['password_nueva']     ?? '';
    $pass_confirmar = $_POST['password_confirmar'] ?? '';

    if (empty($nombre) || empty($email)) {
        $error = "El nombre y el email son obligatorios.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "El formato del email no es válido.";

    } else {

        $check = $pdo->prepare(
            "SELECT ID_CLIENTE FROM CLIENTES WHERE EMAIL_CLI = ? AND ID_CLIENTE != ?"
        );
        $check->execute([$email, $_SESSION['id_cliente']]);

        if ($check->fetch()) {
            $error = "Ese email ya está en uso por otra cuenta.";

        } elseif (!empty($pass_nueva)) {

            if (empty($pass_actual)) {
                $error = "Debes introducir tu contraseña actual para cambiarla.";
            } elseif (strlen($pass_nueva) < 6) {
                $error = "La contraseña nueva debe tener al menos 6 caracteres.";
            } elseif ($pass_nueva !== $pass_confirmar) {
                $error = "La contraseña nueva y la confirmación no coinciden.";
            } else {
                $stmtHash = $pdo->prepare("SELECT PASSWORD_HASH FROM CLIENTES WHERE ID_CLIENTE = ?");
                $stmtHash->execute([$_SESSION['id_cliente']]);
                $hash_actual = $stmtHash->fetchColumn();

                if (!password_verify($pass_actual, $hash_actual)) {
                    $error = "La contraseña actual no es correcta.";
                } else {
                    $nuevo_hash = password_hash($pass_nueva, PASSWORD_BCRYPT);
                    $pdo->prepare(
                        "UPDATE CLIENTES SET NOMBRE_CLI=?, DIRECCION_CLI=?, EMAIL_CLI=?, TELEFONO_CLI=?, PASSWORD_HASH=? WHERE ID_CLIENTE=?"
                    )->execute([$nombre, $direccion, $email, $telefono, $nuevo_hash, $_SESSION['id_cliente']]);
                    $_SESSION['usuario'] = $nombre;
                    $exito = "Datos y contraseña actualizados correctamente.";
                    $cliente = ['NOMBRE_CLI' => $nombre, 'DIRECCION_CLI' => $direccion, 'EMAIL_CLI' => $email, 'TELEFONO_CLI' => $telefono];
                }
            }

        } else {
            $pdo->prepare(
                "UPDATE CLIENTES SET NOMBRE_CLI=?, DIRECCION_CLI=?, EMAIL_CLI=?, TELEFONO_CLI=? WHERE ID_CLIENTE=?"
            )->execute([$nombre, $direccion, $email, $telefono, $_SESSION['id_cliente']]);
            $_SESSION['usuario'] = $nombre;
            $exito = "Datos actualizados correctamente.";
            $cliente = ['NOMBRE_CLI' => $nombre, 'DIRECCION_CLI' => $direccion, 'EMAIL_CLI' => $email, 'TELEFONO_CLI' => $telefono];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis datos - LogiTrans</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="CSS/style.css">
    <link rel="stylesheet" href="CSS/dashboard.css">
</head>
<body class="bg-light">

<!-- NAVBAR -->
<nav class="navbar-logitrans d-flex align-items-center justify-content-between px-3 py-2">
    <div class="d-flex align-items-center gap-3">
        <button onclick="toggleSidebar()" class="btn btn-dark border-0 p-1">
            <i class="bi bi-list fs-4 text-white"></i>
        </button>
        <a href="index.php" class="text-white fw-bold text-decoration-none">
            <img src="Imagenes/logo_sinfond.png" alt="LogiTrans" height="32"
                 class="d-inline-block align-text-top me-1">LogiTrans
        </a>
    </div>
    <div class="d-flex align-items-center gap-2">
        <span class="text-white-50 small d-none d-md-inline">
            <i class="bi bi-person-circle me-1"></i><?php echo htmlspecialchars($_SESSION['usuario']); ?>
        </span>
        <a href="logout.php" class="btn-nav-login">
            <i class="bi bi-box-arrow-right me-1"></i>Salir
        </a>
    </div>
</nav>

<!-- SIDEBAR -->
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

    <a href="dashboard.php?seccion=solicitudes" class="sidebar-link">
        <i class="bi bi-list-ul"></i> Mis solicitudes
    </a>
    <a href="dashboard.php?seccion=envios" class="sidebar-link">
        <i class="bi bi-truck"></i> Mis envíos
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

    <a href="perfil.php" class="sidebar-link activo">
        <i class="bi bi-person"></i> Mis datos
    </a>
    <a href="logout.php" class="sidebar-link text-danger">
        <i class="bi bi-box-arrow-left"></i> Cerrar sesión
    </a>

</div>

<div class="sidebar-overlay" id="overlay" onclick="toggleSidebar()"></div>

<!-- CONTENIDO -->
<div class="contenido-principal">

    <h4 class="fw-bold mb-4">Mis datos</h4>

    <?php if ($exito): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle me-2"></i><?php echo $exito; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-circle me-2"></i><?php echo $error; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">

        <!-- Formulario -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white fw-bold border-0 pt-3">
                    <i class="bi bi-person me-2 text-danger"></i>Datos personales
                </div>
                <div class="card-body">
                    <form method="POST">

                        <div class="row g-3">

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Nombre completo <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="nombre" class="form-control"
                                       value="<?php echo htmlspecialchars($cliente['NOMBRE_CLI']); ?>"
                                       required maxlength="30">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Teléfono</label>
                                <input type="tel" name="telefono" class="form-control"
                                       value="<?php echo htmlspecialchars($cliente['TELEFONO_CLI'] ?? ''); ?>"
                                       maxlength="15" placeholder="600123456">
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">
                                    Email <span class="text-danger">*</span>
                                </label>
                                <input type="email" name="email" class="form-control"
                                       value="<?php echo htmlspecialchars($cliente['EMAIL_CLI']); ?>"
                                       required maxlength="50">
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Dirección</label>
                                <input type="text" name="direccion" class="form-control"
                                       value="<?php echo htmlspecialchars($cliente['DIRECCION_CLI'] ?? ''); ?>"
                                       maxlength="50" placeholder="Calle, número, ciudad">
                            </div>

                        </div>

                        <hr class="my-4">

                        <h6 class="fw-bold mb-1">
                            <i class="bi bi-lock me-2 text-danger"></i>Cambiar contraseña
                        </h6>
                        <p class="text-muted small mb-3">Déjalo vacío si no quieres cambiarla.</p>

                        <div class="row g-3">

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Contraseña actual</label>
                                <input type="password" name="password_actual" class="form-control"
                                       placeholder="Tu contraseña actual">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Contraseña nueva</label>
                                <input type="password" name="password_nueva" class="form-control"
                                       placeholder="Mínimo 6 caracteres">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Confirmar nueva</label>
                                <input type="password" name="password_confirmar" class="form-control"
                                       placeholder="Repite la nueva">
                            </div>

                        </div>

                        <div class="mt-4 d-flex gap-2">
                            <button type="submit" class="btn btn-danger px-4 btn-animado">
                                <i class="bi bi-save me-2"></i>Guardar cambios
                            </button>
                            <a href="dashboard.php" class="btn btn-outline-secondary px-4">
                                Cancelar
                            </a>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        <!-- Resumen -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white fw-bold border-0 pt-3">
                    <i class="bi bi-person-circle me-2 text-danger"></i>Resumen
                </div>
                <div class="card-body text-center">

                    <div class="rounded-circle bg-danger d-inline-flex align-items-center
                                justify-content-center text-white mb-3"
                         style="width:72px; height:72px; font-size:1.8rem;">
                        <?php echo strtoupper(substr($cliente['NOMBRE_CLI'], 0, 1)); ?>
                    </div>

                    <h5 class="fw-bold mb-0">
                        <?php echo htmlspecialchars($cliente['NOMBRE_CLI']); ?>
                    </h5>
                    <p class="text-muted small mb-3">
                        <?php echo htmlspecialchars($cliente['EMAIL_CLI']); ?>
                    </p>

                    <hr>

                    <ul class="list-unstyled text-start small">
                        <li class="mb-2">
                            <i class="bi bi-telephone me-2 text-danger"></i>
                            <?php echo $cliente['TELEFONO_CLI']
                                ? htmlspecialchars($cliente['TELEFONO_CLI'])
                                : '<span class="text-muted">Sin teléfono</span>'; ?>
                        </li>
                        <li>
                            <i class="bi bi-geo-alt me-2 text-danger"></i>
                            <?php echo $cliente['DIRECCION_CLI']
                                ? htmlspecialchars($cliente['DIRECCION_CLI'])
                                : '<span class="text-muted">Sin dirección</span>'; ?>
                        </li>
                    </ul>

                    <hr>

                    <a href="dashboard.php" class="btn btn-outline-danger w-100 btn-sm">
                        <i class="bi bi-arrow-left me-1"></i>Volver al panel
                    </a>

                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('abierto');
    document.getElementById('overlay').classList.toggle('visible');
}
</script>

</body>
</html>
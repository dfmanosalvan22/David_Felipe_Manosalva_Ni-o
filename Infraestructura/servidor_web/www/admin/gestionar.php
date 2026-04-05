<?php
session_start();
require_once '../config/bd.php';

// Si no está logueado como empleado, redirigir al login
if (!isset($_SESSION['empleado'])) {
    header("Location: login.php");
    exit();
}

$id     = $_GET['id']     ?? null;
$accion = $_GET['accion'] ?? null;

// Validar que llegaron los parametros correctos
if (!$id || !in_array($accion, ['aceptar', 'rechazar'])) {
    header("Location: index.php");
    exit();
}

$pdo = conectar();

// Recoger los datos de la solicitud
$stmt = $pdo->prepare(
    "SELECT * FROM SOLICITUDES WHERE ID_SOLICITUD = ?"
);
$stmt->execute([$id]);
$solicitud = $stmt->fetch();

// Si no existe la solicitud, volver al panel
if (!$solicitud) {
    header("Location: index.php");
    exit();
}

// Si ya fue procesada, volver al panel
if (in_array($solicitud['ESTADO'], ['ACEPTADA', 'RECHAZADA'])) {
    header("Location: index.php");
    exit();
}

if ($accion === 'rechazar') {

    // Simplemente cambiar el estado a RECHAZADA
    $stmt = $pdo->prepare(
        "UPDATE SOLICITUDES SET ESTADO = 'RECHAZADA' WHERE ID_SOLICITUD = ?"
    );
    $stmt->execute([$id]);
    header("Location: index.php");
    exit();
}

// Si es aceptar, mostramos un formulario para completar los datos del envio
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id_vehiculo   = $_POST['id_vehiculo'];
    $id_conductor  = $_POST['id_conductor'];
    $fecha_envio   = $_POST['fecha_envio'];

    if (empty($id_vehiculo) || empty($id_conductor) || empty($fecha_envio)) {
        $error = "Todos los campos son obligatorios.";
    } else {

        // 1. Crear el envio en la tabla ENVIOS
        $stmt = $pdo->prepare(
            "INSERT INTO ENVIOS (DESTINO, ORIGEN, ESTADO_ENVIO, FECHA_ENVIO, ID_EMPLEADO, ID_VEHICULO, ID_SOLICITUD)
             VALUES (?, ?, 'PENDIENTE', ?, ?, ?, ?)"
        );
        $stmt->execute([
            $solicitud['DESTINO'],
            $solicitud['ORIGEN'],
            $fecha_envio,
            $id_conductor,
            $id_vehiculo,
            $id
        ]);

        // 2. Actualizar la solicitud a ACEPTADA
        $stmt = $pdo->prepare(
            "UPDATE SOLICITUDES SET ESTADO = 'ACEPTADA' WHERE ID_SOLICITUD = ?"
        );
        $stmt->execute([$id]);

        header("Location: index.php");
        exit();
    }
}

// Recoger vehiculos disponibles
$vehiculos = $pdo->query(
    "SELECT ID_VEHICULO, MATRICULA_VEHI, MARCA_VEHI, MODELO_VEHI, CAPACIDAD_VEHI
     FROM VEHICULOS
     WHERE ESTADO_MANTENIMIENTO_VEHI = 'Operativo'"
)->fetchAll();

// Recoger conductores disponibles (puesto 19 = Conductor, 20 = Jefe Conductores)
$conductores = $pdo->query(
    "SELECT ID_EMPLEADO, NOMBRE_EMP, APELLIDOS_EMP
     FROM EMPLEADOS
     WHERE ID_PUESTO IN (19, 20)"
)->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar solicitud - LogiTrans</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/style.css">
</head>
<body class="bg-light">

<nav class="navbar bg-dark py-2">
    <div class="container-fluid d-flex align-items-center">
        <a href="index.php" class="text-white fw-bold fs-5 text-decoration-none">
            LogiTrans — Panel de Gestion
        </a>
        <div class="ms-auto d-flex align-items-center gap-3">
            <span class="text-white small">
                <?php echo htmlspecialchars($_SESSION['empleado']); ?>
            </span>
            <a href="logout.php" class="btn btn-warning btn-sm">Cerrar sesion</a>
        </div>
    </div>
</nav>

<div class="container mt-4 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-7">

            <a href="index.php" class="btn btn-outline-secondary btn-sm mb-3">
                Volver al panel
            </a>

            <div class="card shadow p-4">

                <h4 class="fw-bold mb-4">
                    Aceptar solicitud #<?php echo $solicitud['ID_SOLICITUD']; ?>
                </h4>

                <!-- Resumen de la solicitud -->
                <div class="alert alert-light border mb-4">
                    <p class="mb-1">
                        <strong>Servicio:</strong> <?php echo $solicitud['TIPO_SERVICIO']; ?>
                    </p>
                    <p class="mb-1">
                        <strong>Mercancia:</strong>
                        <?php echo htmlspecialchars($solicitud['TIPO_MERCANCIA']); ?>
                        <?php if ($solicitud['DESCRIPCION']): ?>
                            — <?php echo htmlspecialchars($solicitud['DESCRIPCION']); ?>
                        <?php endif; ?>
                    </p>
                    <?php if ($solicitud['PESO_KG']): ?>
                    <p class="mb-1">
                        <strong>Peso:</strong> <?php echo $solicitud['PESO_KG']; ?> kg
                    </p>
                    <?php endif; ?>
                    <p class="mb-1">
                        <strong>Origen:</strong>
                        <?php echo htmlspecialchars($solicitud['ORIGEN'] ?? '—'); ?>
                    </p>
                    <p class="mb-0">
                        <strong>Destino:</strong>
                        <?php echo htmlspecialchars($solicitud['DESTINO'] ?? '—'); ?>
                    </p>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <!-- Formulario para completar el envio -->
                <form method="POST">

                    <div class="mb-3">
                        <label class="form-label">Vehiculo asignado</label>
                        <select name="id_vehiculo" required class="form-select">
                            <option value="">Selecciona un vehiculo</option>
                            <?php foreach ($vehiculos as $v): ?>
                            <option value="<?php echo $v['ID_VEHICULO']; ?>">
                                <?php echo $v['MATRICULA_VEHI']; ?>
                                — <?php echo $v['MARCA_VEHI']; ?>
                                <?php echo $v['MODELO_VEHI']; ?>
                                (<?php echo $v['CAPACIDAD_VEHI']; ?> kg)
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Conductor asignado</label>
                        <select name="id_conductor" required class="form-select">
                            <option value="">Selecciona un conductor</option>
                            <?php foreach ($conductores as $c): ?>
                            <option value="<?php echo $c['ID_EMPLEADO']; ?>">
                                <?php echo htmlspecialchars($c['NOMBRE_EMP']); ?>
                                <?php echo htmlspecialchars($c['APELLIDOS_EMP']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Fecha del envio</label>
                        <input type="date" name="fecha_envio" required class="form-control"
                               min="<?php echo date('Y-m-d'); ?>">
                    </div>

                    <button type="submit" class="btn btn-success w-100">
                        Confirmar y crear envio
                    </button>

                </form>

            </div>
        </div>
    </div>
</div>

</body>
</html>
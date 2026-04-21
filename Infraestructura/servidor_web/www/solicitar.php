<?php
session_start();
require_once 'config/bd.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$servicio = $_GET['servicio'] ?? '';

$servicios_validos = ['transporte', 'almacenamiento', 'urgente', 'integral'];
if (!in_array($servicio, $servicios_validos)) {
    header("Location: index.php");
    exit();
}

$nombres = [
    'transporte'     => 'Transporte de mercancías',
    'almacenamiento' => 'Almacenamiento en bodega',
    'urgente'        => 'Transporte urgente',
    'integral'       => 'Logística integral',
];

$error = '';
$exito = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $tipo_mercancia = trim($_POST['tipo_mercancia']);
    $descripcion    = trim($_POST['descripcion']);
    $origen         = trim($_POST['origen']);
    $destino        = trim($_POST['destino']);
    $observaciones  = trim($_POST['observaciones']);

    $peso   = !empty($_POST['peso_kg'])    ? $_POST['peso_kg']    : null;
    $volumen = !empty($_POST['volumen_m3']) ? $_POST['volumen_m3'] : null;

    if (empty($tipo_mercancia)) {
        $error = "El tipo de mercancía es obligatorio.";
    } else {
        $pdo  = conectar();
        $stmt = $pdo->prepare(
            "INSERT INTO SOLICITUDES (
                ID_CLIENTE, TIPO_SERVICIO, TIPO_MERCANCIA,
                DESCRIPCION, PESO_KG, VOLUMEN_M3,
                ORIGEN, DESTINO, OBSERVACIONES
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $_SESSION['id_cliente'],
            strtoupper($servicio),
            $tipo_mercancia,
            $descripcion,
            $peso,
            $volumen,
            $origen,
            $destino,
            $observaciones,
        ]);
        $exito = "Solicitud enviada correctamente. Nos pondremos en contacto contigo.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Solicitar - LogiTrans</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Iconos -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <!-- TU CSS -->
    <link rel="stylesheet" href="CSS/style.css">
</head>
<body class="bg-light">

<?php include("header.php"); ?>

<!-- CONTENEDOR IGUAL QUE LOGIN -->
<div class="container d-flex justify-content-center align-items-center"
     style="min-height: calc(100vh - 70px); padding: 40px 16px;">

    <div class="card shadow p-4 form-box w-100" style="max-width: 600px;">

        <!-- CABECERA MEJORADA -->
        <div class="text-center mb-4">
            <i class="bi bi-truck fs-1 text-danger"></i>
            <h2 class="fw-bold mt-2 titulo-animado">
                <?php echo $nombres[$servicio]; ?>
            </h2>
            <p class="text-muted small">Completa los datos de tu solicitud</p>
        </div>

        <?php if ($exito): ?>
            <div class="alert alert-success">
                <i class="bi bi-check-circle me-2"></i><?php echo $exito; ?>
            </div>
            <a href="dashboard.php" class="btn btn-danger w-100 btn-animado">
                Ver mis solicitudes
            </a>

        <?php else: ?>

            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-circle me-2"></i><?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST">

                <div class="mb-3">
                    <label class="form-label fw-semibold">Tipo de mercancía *</label>
                    <select name="tipo_mercancia" required class="form-select">
                        <option value="">Selecciona una opción</option>
                        <option value="General">Mercancía general</option>
                        <option value="Fragil">Mercancía frágil</option>
                        <option value="Perecedero">Productos perecederos</option>
                        <option value="Maquinaria">Maquinaria y equipos</option>
                        <option value="Otro">Otro</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Descripción</label>
                    <textarea name="descripcion" class="form-control" rows="2"
                        placeholder="Describe qué necesitas"></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Peso aproximado (kg)</label>
                    <input type="number" name="peso_kg" class="form-control"
                           min="1" step="0.01" placeholder="Ej: 500">
                </div>

                <?php if (in_array($servicio, ['almacenamiento', 'integral'])): ?>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Volumen aproximado (m3)</label>
                    <input type="number" name="volumen_m3" class="form-control"
                           min="0.1" step="0.001" placeholder="Ej: 2.5">
                </div>
                <?php endif; ?>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Origen</label>
                    <input type="text" name="origen" class="form-control"
                           placeholder="Ciudad o dirección de origen">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Destino</label>
                    <input type="text" name="destino" class="form-control"
                           placeholder="Ciudad o dirección de destino">
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Observaciones</label>
                    <textarea name="observaciones" class="form-control" rows="2"
                        placeholder="Cualquier detalle adicional"></textarea>
                </div>

                <button type="submit" class="btn btn-danger w-100 btn-animado fw-bold py-2">
                    <i class="bi bi-send me-2"></i>Enviar solicitud
                </button>

            </form>

            <p class="mt-3 text-center small">
                <a href="index.php" class="text-muted">
                    <i class="bi bi-arrow-left me-1"></i>Volver al inicio
                </a>
            </p>

        <?php endif; ?>

    </div>
</div>

</body>
</html>
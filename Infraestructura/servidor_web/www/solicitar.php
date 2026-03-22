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
    'transporte'     => 'Transporte de mercancias',
    'almacenamiento' => 'Almacenamiento en bodega',
    'urgente'        => 'Transporte urgente',
    'integral'       => 'Logistica integral',
];

$error = '';
$exito = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $tipo_mercancia = trim($_POST['tipo_mercancia']);
    $descripcion    = trim($_POST['descripcion']);
    $origen         = trim($_POST['origen']);
    $destino        = trim($_POST['destino']);
    $observaciones  = trim($_POST['observaciones']);

    if (empty($tipo_mercancia)) {
        $error = "El tipo de mercancia es obligatorio.";
    } else {
        $pdo  = conectar();
        $stmt = $pdo->prepare(
            "INSERT INTO SOLICITUDES (ID_CLIENTE, TIPO_SERVICIO, TIPO_MERCANCIA, DESCRIPCION, ORIGEN, DESTINO, OBSERVACIONES)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $_SESSION['id_cliente'],
            strtoupper($servicio),
            $tipo_mercancia,
            $descripcion,
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitar - LogiTrans</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="CSS/style.css">
</head>
<body class="bg-light">

<?php include("header.php"); ?>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow p-4 form-box">

                <h2 class="fw-bold text-center mb-4">
                    <?php echo $nombres[$servicio]; ?>
                </h2>

                <?php if ($exito): ?>
                    <div class="alert alert-success"><?php echo $exito; ?></div>
                    <a href="dashboard.php" class="btn btn-danger w-100">Ver mis solicitudes</a>

                <?php else: ?>

                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <form method="POST">

                        <div class="mb-3">
                            <label class="form-label">Tipo de mercancia</label>
                            <select name="tipo_mercancia" required class="form-select">
                                <option value="">Selecciona una opcion</option>
                                <option value="General">Mercancia general</option>
                                <option value="Fragil">Mercancia fragil</option>
                                <option value="Perecedero">Productos perecederos</option>
                                <option value="Maquinaria">Maquinaria y equipos</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Descripcion</label>
                            <textarea name="descripcion" class="form-control" rows="3"
                                placeholder="Describe que necesitas"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Origen</label>
                            <input type="text" name="origen" class="form-control"
                                   placeholder="Ciudad o direccion de origen">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Destino</label>
                            <input type="text" name="destino" class="form-control"
                                   placeholder="Ciudad o direccion de destino">
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Observaciones</label>
                            <textarea name="observaciones" class="form-control" rows="2"
                                placeholder="Cualquier detalle adicional"></textarea>
                        </div>

                        <button type="submit" class="btn btn-danger w-100">
                            Enviar solicitud
                        </button>

                    </form>

                    <p class="mt-3 text-center">
                        <a href="index.php">Volver al inicio</a>
                    </p>

                <?php endif; ?>

            </div>
        </div>
    </div>
</div>

</body>
</html>
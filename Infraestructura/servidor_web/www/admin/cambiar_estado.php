<?php
session_start();
require_once '../config/bd.php';

if (!isset($_SESSION['empleado'])) {
    header("Location: login.php");
    exit();
}

$id_envio     = $_POST['id_envio']      ?? null;
$nuevo_estado = $_POST['nuevo_estado']  ?? null;

$estados_validos = ['PENDIENTE', 'EN_TRANSITO', 'ENTREGADO', 'CANCELADO'];

if ($id_envio && in_array($nuevo_estado, $estados_validos)) {
    $pdo  = conectar();
    $stmt = $pdo->prepare(
        "UPDATE ENVIOS SET ESTADO_ENVIO = ? WHERE ID_ENVIO = ?"
    );
    $stmt->execute([$nuevo_estado, $id_envio]);
}

header("Location: envios.php");
exit();
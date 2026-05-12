<?php
session_start();
date_default_timezone_set('Europe/Madrid');
require_once 'config/bd.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit();
}

$id_solicitud = (int) ($_POST['id_solicitud'] ?? 0);
$mensaje      = trim($_POST['mensaje'] ?? '');

// Comprobar empleado PRIMERO, luego cliente
// (importante el orden para evitar conflictos si hay dos pestañas abiertas)
if (isset($_SESSION['empleado'])) {
    $remitente = 'empleado';
    $email_remitente = $_SESSION['email_empleado'] ?? '';
    $redirigir = "admin/index.php";

} elseif (isset($_SESSION['usuario'])) {
    $remitente = 'cliente';
    $email_remitente = $_SESSION['email_cliente'] ?? '';
    $redirigir = "dashboard.php?seccion=solicitudes";

} else {
    header("Location: login.php");
    exit();
}

if ($id_solicitud > 0 && !empty($mensaje)) {
    $pdo  = conectar();
    $stmt = $pdo->prepare(
        "INSERT INTO MENSAJES (ID_SOLICITUD, REMITENTE, EMAIL_REMITENTE, MENSAJE)
         VALUES (?, ?, ?, ?)"
    );
    $stmt->execute([$id_solicitud, $remitente, $email_remitente, $mensaje]);
}

header("Location: {$redirigir}#chat-{$id_solicitud}");
exit();

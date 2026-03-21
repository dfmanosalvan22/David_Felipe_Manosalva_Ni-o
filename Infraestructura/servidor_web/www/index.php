<?php
session_start();
require_once 'config/bd.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LogiTrans S.A.</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="CSS/style.css">
</head>
<body>

<?php include("header.php"); ?>

<!-- ── HERO ──────────────────────────────────────────────────── -->
<section class="hero d-flex align-items-center justify-content-center text-white text-center">
    <div>
        <h1 class="display-4 fw-bold">Movemos lo que importa</h1>
        <p class="fs-5 mt-3">Soluciones de Logística y Transporte en Extremadura y toda España</p>
        <div class="mt-4 d-flex gap-3 justify-content-center">
            <a href="#servicios" class="btn btn-danger btn-lg">Ver servicios</a>
            <a href="registro.php" class="btn btn-outline-light btn-lg">Crear cuenta</a>
        </div>
    </div>
</section>

<!-- ── SOBRE NOSOTROS ─────────────────────────────────────────── -->
<section class="py-5 bg-light" id="nosotros">
    <div class="container">

        <div class="text-center mb-5">
            <h2 class="fw-bold">Sobre LogiTrans S.A.</h2>
            <p class="text-muted fs-5 mt-2">
                Empresa de logística y transporte con sede en Mérida, Extremadura.
                Llevamos años conectando empresas y particulares con soluciones
                de transporte fiables, seguras y a medida.
            </p>
        </div>

        <div class="row g-4 justify-content-center">

            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm text-center p-4">
                    <i class="bi bi-bullseye fs-1 text-danger mb-3"></i>
                    <h4 class="fw-bold">Misión</h4>
                    <p class="text-muted">
                        Ofrecer soluciones logísticas eficientes y personalizadas,
                        garantizando la entrega segura y puntual de cada mercancía,
                        con un servicio cercano y profesional.
                    </p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm text-center p-4">
                    <i class="bi bi-eye fs-1 text-danger mb-3"></i>
                    <h4 class="fw-bold">Visión</h4>
                    <p class="text-muted">
                        Convertirnos en la empresa de referencia en logística
                        y transporte de la región, expandiendo nuestra flota
                        y capacidad para atender a clientes a nivel nacional.
                    </p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm text-center p-4">
                    <i class="bi bi-stars fs-1 text-danger mb-3"></i>
                    <h4 class="fw-bold">Valores</h4>
                    <p class="text-muted">
                        Compromiso, puntualidad, transparencia y seguridad.
                        Cada envío es tratado con la misma dedicación,
                        independientemente de su tamaño o destino.
                    </p>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- ── SERVICIOS ─────────────────────────────────────────────── -->
<section class="py-5 bg-light" id="servicios">
    <div class="container">

        <div class="text-center mb-5">
            <h2 class="fw-bold">Nuestros servicios</h2>
            <p class="text-muted">Selecciona el servicio que necesitas para continuar</p>
        </div>

        <div class="row g-4">

            <div class="col-md-6 col-lg-3">
                <div class="card h-100 shadow-sm text-center p-4 servicio-card">
                    <i class="bi bi-truck fs-1 text-danger mb-3"></i>
                    <h5 class="fw-bold">Transporte de mercancías</h5>
                    <p class="text-muted small">
                        Traslado de mercancías entre cualquier punto de España.
                        Flota propia con capacidad de hasta 20.000 kg.
                    </p>
                    <a href="<?php echo isset($_SESSION['usuario']) ? 'solicitar.php?servicio=transporte' : 'login.php?redir=transporte'; ?>"
                       class="btn btn-danger mt-auto">
                        Solicitar
                    </a>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card h-100 shadow-sm text-center p-4 servicio-card">
                    <i class="bi bi-building fs-1 text-danger mb-3"></i>
                    <h5 class="fw-bold">Almacenamiento en bodega</h5>
                    <p class="text-muted small">
                        Nave propia en Mérida para el almacenamiento
                        temporal o prolongado de tu mercancía con total seguridad.
                    </p>
                    <a href="<?php echo isset($_SESSION['usuario']) ? 'solicitar.php?servicio=almacenamiento' : 'login.php?redir=almacenamiento'; ?>"
                       class="btn btn-danger mt-auto">
                        Solicitar
                    </a>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card h-100 shadow-sm text-center p-4 servicio-card">
                    <i class="bi bi-lightning-charge fs-1 text-danger mb-3"></i>
                    <h5 class="fw-bold">Transporte urgente</h5>
                    <p class="text-muted small">
                        Entrega prioritaria en el menor tiempo posible.
                        Ideal para envíos de última hora o mercancía sensible.
                    </p>
                    <a href="<?php echo isset($_SESSION['usuario']) ? 'solicitar.php?servicio=urgente' : 'login.php?redir=urgente'; ?>"
                       class="btn btn-danger mt-auto">
                        Solicitar
                    </a>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card h-100 shadow-sm text-center p-4 servicio-card">
                    <i class="bi bi-boxes fs-1 text-danger mb-3"></i>
                    <h5 class="fw-bold">Logística integral</h5>
                    <p class="text-muted small">
                        Combinamos almacenamiento y transporte en un único servicio.
                        Tu mercancía llega a bodega y se distribuye cuando lo necesitas.
                    </p>
                    <a href="<?php echo isset($_SESSION['usuario']) ? 'solicitar.php?servicio=integral' : 'login.php?redir=integral'; ?>"
                       class="btn btn-danger mt-auto">
                        Solicitar
                    </a>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- ── POR QUÉ ELEGIRNOS ──────────────────────────────────────── -->
<section class="py-5 bg-white">
    <div class="container">

        <div class="text-center mb-5">
            <h2 class="fw-bold">¿Por qué elegirnos?</h2>
        </div>

        <div class="row g-4 justify-content-center">

            <div class="col-md-3 text-center">
                <i class="bi bi-truck-front fs-1 text-danger"></i>
                <h5 class="fw-bold mt-2">Flota propia</h5>
                <p class="text-muted small">
                    8 vehículos propios de distintas capacidades
                    para adaptarnos a cualquier envío.
                </p>
            </div>

            <div class="col-md-3 text-center">
                <i class="bi bi-geo-alt fs-1 text-danger"></i>
                <h5 class="fw-bold mt-2">Cobertura nacional</h5>
                <p class="text-muted small">
                    Operamos en toda España con base
                    en Mérida, Extremadura.
                </p>
            </div>

            <div class="col-md-3 text-center">
                <i class="bi bi-shield-check fs-1 text-danger"></i>
                <h5 class="fw-bold mt-2">Seguridad garantizada</h5>
                <p class="text-muted small">
                    Toda la mercancía está controlada
                    y asegurada durante el transporte.
                </p>
            </div>

            <div class="col-md-3 text-center">
                <i class="bi bi-graph-up fs-1 text-danger"></i>
                <h5 class="fw-bold mt-2">Seguimiento online</h5>
                <p class="text-muted small">
                    Consulta el estado de tus envíos
                    en tiempo real desde tu perfil.
                </p>
            </div>

        </div>
    </div>
</section>

<!-- ── CONTACTO ───────────────────────────────────────────────── -->
<section class="py-5 bg-dark text-white" id="contacto">
    <div class="container">

        <div class="text-center mb-4">
            <h2 class="fw-bold">Contacto</h2>
            <p class="text-white-50">¿Tienes alguna pregunta? Estamos aquí para ayudarte.</p>
        </div>

        <div class="row justify-content-center g-4 text-center">

            <div class="col-md-3">
                <i class="bi bi-geo-alt-fill fs-2 text-danger"></i>
                <h6 class="fw-bold mt-2">Dirección</h6>
                <p class="text-white small">Mérida, Extremadura, España</p>
            </div>

            <div class="col-md-3">
                <i class="bi bi-telephone-fill fs-2 text-danger"></i>
                <h6 class="fw-bold mt-2">Teléfono</h6>
                <p class="text-white small">+34 924 000 000</p>
            </div>

            <div class="col-md-3">
                <i class="bi bi-envelope-fill fs-2 text-danger"></i>
                <h6 class="fw-bold mt-2">Email</h6>
                <p class="text-white small">contacto@logitrans.es</p>
            </div>

            <div class="col-md-3">
                <i class="bi bi-clock-fill fs-2 text-danger"></i>
                <h6 class="fw-bold mt-2">Horario</h6>
                <p class="text-white small">Lunes a Viernes: 8:00 - 18:00</p>
            </div>

        </div>
    </div>
</section>

<!-- ── PIE DE PÁGINA ──────────────────────────────────────────── -->
<footer class="bg-black text-white text-center py-3">
    <small>© 2025 LogiTrans S.A. — Todos los derechos reservados</small>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
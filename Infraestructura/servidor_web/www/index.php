<?php
session_start();
require_once 'config/bd.php';

// Sacar los vehiculos operativos de la BD para la seccion de flota
try {
    $pdo = conectar();
    $vehiculos = $pdo->query(
        "SELECT MATRICULA_VEHI, MARCA_VEHI, MODELO_VEHI, CAPACIDAD_VEHI, ESTADO_MANTENIMIENTO_VEHI
         FROM VEHICULOS
         ORDER BY CAPACIDAD_VEHI DESC"
    )->fetchAll();
} catch (Exception $e) {
    $vehiculos = [];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LogiTrans S.A. — Transporte y Logística</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="CSS/style.css">
</head>
<body>

<!-- ── NAVBAR ─────────────────────────────────────────────────── -->
<nav class="navbar navbar-expand-lg navbar-logitrans">
    <div class="container">

        <a class="navbar-brand" href="index.php">
            <img src="Imagenes/logo_sinfond.png" alt="LogiTrans S.A." height="35" class="d-inline-block align-text-top me-2">LogiTrans
        </a>

        <button class="navbar-toggler border-secondary" type="button"
                data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon" style="filter: invert(1)"></span>
        </button>

        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav mx-auto gap-1">
                <li class="nav-item"><a class="nav-link" href="#nosotros">Nosotros</a></li>
                <li class="nav-item"><a class="nav-link" href="#servicios">Servicios</a></li>
                <li class="nav-item"><a class="nav-link" href="#flota">Flota</a></li>
                <li class="nav-item"><a class="nav-link" href="#contacto">Contacto</a></li>
            </ul>

            <div class="d-flex gap-2 align-items-center">
                <?php if (isset($_SESSION['usuario'])): ?>
                    <a href="dashboard.php" class="btn-nav-login text-decoration-none">
                        <i class="bi bi-person-circle me-1"></i><?php echo htmlspecialchars($_SESSION['usuario']); ?>
                    </a>
                    <a href="logout.php" class="btn-nav-login text-decoration-none">Salir</a>
                <?php else: ?>
                    <a href="login.php" class="btn-nav-login text-decoration-none">Iniciar sesión</a>
                    <a href="registro.php" class="btn-nav-register text-decoration-none">Registrarse</a>
                <?php endif; ?>
            </div>
        </div>

    </div>
</nav>

<!-- ── HERO ──────────────────────────────────────────────────── -->
<section class="hero text-white">
    <div>
        <h1 class="fw-bold titulo-animado">Tu Logística, sin complicaciones</h1>
        <p class="mt-3">
            Transporte y Gestión de mercancías adaptados a empresas y particulares en toda España
        </p>
        <div class="mt-4 d-flex gap-3 justify-content-center flex-wrap">
            <a href="#servicios" class="btn btn-danger btn-lg px-4">Ver servicios</a>
            <?php if (!isset($_SESSION['usuario'])): ?>
                <a href="registro.php" class="btn btn-outline-light btn-lg px-4">Crear cuenta</a>
            <?php else: ?>
                <a href="dashboard.php" class="btn btn-outline-light btn-lg px-4">Mi panel</a>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- ── BARRA DE ESTADÍSTICAS ─────────────────────────────────── -->
<div class="stats-bar">
    <div class="container">
        <div class="row g-3">
            <div class="col-6 col-md-3">
                <div class="stat-item">
                    <div class="stat-num">+50</div>
                    <div class="stat-label">Empleados</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-item">
                    <div class="stat-num">8</div>
                    <div class="stat-label">Vehículos propios</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-item">
                    <div class="stat-num">4</div>
                    <div class="stat-label">Servicios disponibles</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-item">
                    <div class="stat-num">Nacional</div>
                    <div class="stat-label">Cobertura en España</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ── SOBRE NOSOTROS ─────────────────────────────────────────── -->
<section class="py-5 bg-light" id="nosotros">
    <div class="container">

        <div class="seccion-titulo">
            <h2>Sobre LogiTrans S.A.</h2>
            <div class="linea"></div>
            <p class="mt-3">
                Somos una empresa de logística ubicada en Mérida especializada en el transporte y almacenamiento de mercancías. Trabajamos con empresas y particulares ofreciendo soluciones prácticas, seguras y adaptadas a cada necesidad
            </p>
        </div>

        <div class="row g-4 justify-content-center">

            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm text-center p-4">
                    <i class="bi bi-bullseye fs-1 text-danger mb-3"></i>
                    <h4 class="fw-bold">Misión</h4>
                    <p class="text-muted">
                        Facilitar el transporte y almacenamiento de mercancías de forma eficiente,  asegurando entregas puntuales y un servicio claro para el cliente
                    </p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm text-center p-4">
                    <i class="bi bi-eye fs-1 text-danger mb-3"></i>
                    <h4 class="fw-bold">Visión</h4>
                    <p class="text-muted">
                        Seguir creciendo como empresa logística a nivel nacional, mejorando nuestra flota y ampliando los servicios ofrecidos
                    </p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm text-center p-4">
                    <i class="bi bi-stars fs-1 text-danger mb-3"></i>
                    <h4 class="fw-bold">Valores</h4>
                    <p class="text-muted">
                        Responsabilidad, puntualidad y transparencia en cada envío, ofreciendo un servicio fiable en todo momento
                    </p>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- ── CARRUSEL ──────────────────────────────────────────────── -->
<section class="py-5 bg-white">
    <div class="container">

        <div class="seccion-titulo">
            <h2>Nuestra flota en acción</h2>
            <div class="linea"></div>
            <p class="mt-3">Vehículos preparados para cualquier tipo de transporte</p>
        </div>

        <div id="carruselFlota" class="carousel slide"
             data-bs-ride="carousel" data-bs-interval="3000">

            <!-- Indicadores -->
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#carruselFlota" data-bs-slide-to="0" class="active"></button>
                <button type="button" data-bs-target="#carruselFlota" data-bs-slide-to="1"></button>
                <button type="button" data-bs-target="#carruselFlota" data-bs-slide-to="2"></button>
                <button type="button" data-bs-target="#carruselFlota" data-bs-slide-to="3"></button>
            </div>

            <!-- Imágenes -->
            <div class="carousel-inner rounded shadow">

                <div class="carousel-item active">
                    <img src="Imagenes/cmon_atar.png" class="d-block w-100"
                         style="height: 420px; object-fit: cover;">
                </div>

                <div class="carousel-item">
                    <img src="Imagenes/carru1.png" class="d-block w-100"
                         style="height: 420px; object-fit: cover;">
                </div>

                <div class="carousel-item">
                    <img src="Imagenes/carru2.png" class="d-block w-100"
                         style="height: 420px; object-fit: cover;">
                </div>

                <div class="carousel-item">
                    <img src="Imagenes/carru3.png" class="d-block w-100"
                         style="height: 420px; object-fit: cover;">
                </div>

            </div>

            <!-- Controles -->
            <button class="carousel-control-prev" type="button"
                    data-bs-target="#carruselFlota" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>

            <button class="carousel-control-next" type="button"
                    data-bs-target="#carruselFlota" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>

        </div>
    </div>
</section>


<!-- ── SERVICIOS ─────────────────────────────────────────────── -->
<section class="py-5 bg-white" id="servicios">
    <div class="container">

        <div class="seccion-titulo">
            <h2>Nuestros servicios</h2>
            <div class="linea"></div>
            <p class="mt-3">Selecciona el servicio que necesitas</p>
        </div>

        <div class="row g-4">

            <div class="col-md-6 col-lg-3">
                <div class="card h-100 shadow-sm text-center p-4 servicio-card">
                    <i class="bi bi-truck fs-1 text-danger mb-3"></i>
                    <h5 class="fw-bold">Transporte de mercancías</h5>
                    <p class="text-muted small">
                        Transporte de mercancías a nivel nacional con vehículos adaptados a distintos volúmenes de carga
                    </p>
                    <a href="<?php echo isset($_SESSION['usuario']) ? 'solicitar.php?servicio=transporte' : 'login.php?redir=transporte'; ?>"
                       class="btn btn-danger mt-auto">Solicitar</a>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card h-100 shadow-sm text-center p-4 servicio-card">
                    <i class="bi bi-building fs-1 text-danger mb-3"></i>
                    <h5 class="fw-bold">Almacenamiento en bodega</h5>
                    <p class="text-muted small">
                        Espacio de almacenamiento en nuestras instalaciones, con control y gestión de mercancía
                    </p>
                    <a href="<?php echo isset($_SESSION['usuario']) ? 'solicitar.php?servicio=almacenamiento' : 'login.php?redir=almacenamiento'; ?>"
                       class="btn btn-danger mt-auto">Solicitar</a>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card h-100 shadow-sm text-center p-4 servicio-card">
                    <i class="bi bi-lightning-charge fs-1 text-danger mb-3"></i>
                    <h5 class="fw-bold">Transporte urgente</h5>
                    <p class="text-muted small">
                        Servicio prioritario para entregas rápidas cuando el tiempo es un factor clave
                    </p>
                    <a href="<?php echo isset($_SESSION['usuario']) ? 'solicitar.php?servicio=urgente' : 'login.php?redir=urgente'; ?>"
                       class="btn btn-danger mt-auto">Solicitar</a>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card h-100 shadow-sm text-center p-4 servicio-card">
                    <i class="bi bi-boxes fs-1 text-danger mb-3"></i>
                    <h5 class="fw-bold">Logística integral</h5>
                    <p class="text-muted small">
                        Gestión completa desde la recogida hasta la entrega final, combinando transporte y almacenamiento.
                    </p>
                    <a href="<?php echo isset($_SESSION['usuario']) ? 'solicitar.php?servicio=integral' : 'login.php?redir=integral'; ?>"
                       class="btn btn-danger mt-auto">Solicitar</a>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- ── FLOTA ──────────────────────────────────────────────────── -->
<section class="py-5 bg-light" id="flota">
    <div class="container">

        <div class="seccion-titulo">
            <h2>Nuestra flota</h2>
            <div class="linea"></div>
            <p class="mt-3">
                Vehículos operativos preparados para distintos tipos de transporte y capacidades de carga
            </p>
        </div>

        <?php if (!empty($vehiculos)): ?>
        <div class="row g-4">
            <?php foreach ($vehiculos as $v): ?>
            <div class="col-md-6 col-lg-3">
                <div class="flota-card p-4 h-100">

                    <div class="text-center mb-3">
                        <?php
                        $imagen = "Imagenes/" . $v['MATRICULA_VEHI'] . ".jpg";
                        if (!file_exists($imagen)) {
                            $imagen = "Imagenes/default.jpg";
                        }
                        ?>
                        <img src="<?php echo $imagen; ?>"
                            class="img-fluid rounded mb-2"
                            style="height:120px; width:100%; object-fit:cover;">
                    </div>

                    <h6 class="fw-bold mb-1">
                        <?php echo htmlspecialchars($v['MARCA_VEHI']); ?>
                        <?php echo htmlspecialchars($v['MODELO_VEHI']); ?>
                    </h6>

                    <p class="text-muted small mb-3">
                        <i class="bi bi-credit-card me-1"></i>
                        <?php echo htmlspecialchars($v['MATRICULA_VEHI']); ?>
                    </p>

                    <div class="d-flex justify-content-between align-items-center">
                        <span class="capacidad-badge">
                            <?php echo number_format($v['CAPACIDAD_VEHI'], 0, ',', '.'); ?> kg
                        </span>
                        <?php
                        $estado = $v['ESTADO_MANTENIMIENTO_VEHI'];
                        $clase  = match($estado) {
                            'Operativo'    => 'bg-success text-white',
                            'Mantenimiento'=> 'bg-warning text-dark',
                            'En revision'  => 'bg-info text-dark',
                            default        => 'bg-secondary text-white'
                        };
                        ?>
                        <span class="estado-badge <?php echo $clase; ?>">
                            <?php echo htmlspecialchars($estado); ?>
                        </span>
                    </div>

                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="text-center text-muted">
            <i class="bi bi-truck fs-1"></i>
            <p class="mt-2">Información de flota no disponible.</p>
        </div>
        <?php endif; ?>

    </div>
</section>

<!-- ── POR QUÉ ELEGIRNOS ──────────────────────────────────────── -->
<section class="py-5 bg-white">
    <div class="container">

        <div class="seccion-titulo">
            <h2>¿Por qué elegirnos?</h2>
            <div class="linea"></div>
        </div>

        <div class="row g-4 justify-content-center">

            <div class="col-md-3">
                <div class="porque-item">
                    <i class="bi bi-truck-front"></i>
                    <h5>Flota propia</h5>
                    <p class="text-muted small">
                        Vehículos propios que permiten adaptarnos a distintos tipos de carga
                    </p>
                </div>
            </div>

            <div class="col-md-3">
                <div class="porque-item">
                    <i class="bi bi-geo-alt"></i>
                    <h5>Cobertura nacional</h5>
                    <p class="text-muted small">
                        Servicio disponible en todo el territorio nacional
                    </p>
                </div>
            </div>

            <div class="col-md-3">
                <div class="porque-item">
                    <i class="bi bi-shield-check"></i>
                    <h5>Seguridad garantizada</h5>
                    <p class="text-muted small">
                        Control del estado de la mercancía durante todo el proceso.
                    </p>
                </div>
            </div>

            <div class="col-md-3">
                <div class="porque-item">
                    <i class="bi bi-graph-up"></i>
                    <h5>Seguimiento online</h5>
                    <p class="text-muted small">
                        Consulta del estado de los envíos desde la plataforma
                    </p>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- ── CONTACTO ───────────────────────────────────────────────── -->
<section class="py-5 bg-dark text-white" id="contacto">
    <div class="container">

        <div class="seccion-titulo">
            <h2 class="text-white">Contacto</h2>
            <div class="linea"></div>
            <p class="text-white-50 mt-3">Puedes ponerte en contacto con nosotros para cualquier consulta
    o solicitud de servicio</p>
        </div>

        <div class="row justify-content-center g-4 text-center">

            <div class="col-md-3">
                <div class="contacto-item">
                    <i class="bi bi-geo-alt-fill"></i>
                    <h6 class="fw-bold mt-2">Dirección</h6>
                    <p class="text-white-50 small">Mérida, Extremadura, España</p>
                </div>
            </div>

            <div class="col-md-3">
                <div class="contacto-item">
                    <i class="bi bi-telephone-fill"></i>
                    <h6 class="fw-bold mt-2">Teléfono</h6>
                    <p class="text-white-50 small">+34 600 000 000</p>
                </div>
            </div>

            <div class="col-md-3">
                <div class="contacto-item">
                    <i class="bi bi-envelope-fill"></i>
                    <h6 class="fw-bold mt-2">Email</h6>
                    <p class="text-white-50 small">contacto@logitrans.es</p>
                </div>
            </div>

            <div class="col-md-3">
                <div class="contacto-item">
                    <i class="bi bi-clock-fill"></i>
                    <h6 class="fw-bold mt-2">Horario</h6>
                    <p class="text-white-50 small">Lunes a Viernes: 8:00 - 18:00</p>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- ── FOOTER ─────────────────────────────────────────────────── -->
<footer class="py-4 text-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start mb-2 mb-md-0">
                <span class="fw-bold">
                    <img src="Imagenes/logo_sinfond.png" alt="LogiTrans S.A." height="35" class="d-inline-block align-text-top me-1"></img>LogiTrans S.A.
                </span>
                <span class="text-white-50 ms-2 small">Mérida, Extremadura</span>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <small class="text-white-50">© 2026 LogiTrans S.A. — Todos los derechos reservados</small>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

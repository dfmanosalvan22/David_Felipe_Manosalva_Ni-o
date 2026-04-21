

 <?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>

<nav class="navbar navbar-expand-lg navbar-logitrans">
    <div class="container">

        <a class="navbar-brand" href="index.php">
            <img src="Imagenes/logo_sinfond.png" height="35" class="me-2">
            LogiTrans
        </a>

        <button class="navbar-toggler border-secondary" type="button"
                data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon" style="filter: invert(1)"></span>
        </button>

        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item"><a class="nav-link" href="index.php#nosotros">Nosotros</a></li>
                <li class="nav-item"><a class="nav-link" href="index.php#servicios">Servicios</a></li>
                <li class="nav-item"><a class="nav-link" href="index.php#flota">Flota</a></li>
                <li class="nav-item"><a class="nav-link" href="index.php#contacto">Contacto</a></li>
            </ul>

            <div class="d-flex gap-2">
                <?php if (isset($_SESSION['usuario'])): ?>
                    <a href="dashboard.php" class="btn-nav-login">
                        <i class="bi bi-person-circle fs-7"></i>
                        <?php echo htmlspecialchars($_SESSION['usuario']); ?>
                    </a>
                    <a href="logout.php" class="btn-nav-login"
                    onclick="return confirm('¿Seguro que quieres cerrar sesión?')">Salir</a>
                <?php else: ?>
                    <a href="login.php" class="btn-nav-login">Iniciar sesión</a>
                    <a href="registro.php" class="btn-nav-register">Registrarse</a>
                <?php endif; ?>
            </div>
        </div>

    </div>
</nav>
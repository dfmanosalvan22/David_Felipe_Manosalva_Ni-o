<?php
// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<nav class="navbar bg-dark py-2">
  <div class="container-fluid d-flex align-items-center">

    <!-- Logo -->
    <a href="index.php" class="navbar-brand">
      <img src="Imagenes/logo.png" height="45">
    </a>

    <!-- Título centrado -->
    <a href="index.php" class="text-white fw-bold fs-4 mx-auto text-decoration-none">
      LogiTrans S.A.
    </a>

    <!-- Menú derecha -->
    <div class="d-flex gap-2 align-items-center">
      <?php if (isset($_SESSION['usuario'])): ?>
        <span class="text-white me-2">
          Hola, <?php echo htmlspecialchars($_SESSION['usuario']); ?>
        </span>
        <a href="dashboard.php" class="btn btn-outline-light btn-sm">Mi panel</a>
        <a href="logout.php" class="btn btn-warning btn-sm">Cerrar sesión</a>
      <?php else: ?>
        <a href="registro.php" class="btn btn-outline-light btn-sm">Registrarse</a>
        <a href="login.php" class="btn btn-danger btn-sm">Iniciar sesión</a>
      <?php endif; ?>
    </div>

  </div>
</nav>
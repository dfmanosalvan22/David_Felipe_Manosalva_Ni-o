<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Header</title>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="CSS/style.css">
</head>
<body>

<nav class="navbar bg-dark">
  <div class="container-fluid d-flex justify-content-between align-items-center">

    <!-- Espacio vacío a la izquierda para balancear -->
    <div style="width: 150px;"></div>

    <!-- Logo a la izquierda -->
    <a href="index.php" class="navbar-brand d-flex align-items-center">
      <img src="logo.png" alt="Logo" height="50" class="me-2">
    </a>

    <!-- Logo centrado -->
    <a href="index.php" class="text-white text-decoration-none fw-bold fst-italic fs-1 mx-auto">
      LogiTrans S.A.
    </a>

    <!-- Botones a la derecha -->
    <div class="d-flex gap-2">
      <a href="registro.php" class="btn btn-danger">Registrarse</a>
      <a href="login.php" class="btn btn-danger">Iniciar Sesión</a>
    </div>

  </div>
</nav>

</body>
</html>
<?php
// ── CONFIGURACIÓN DE CONEXIÓN A LA BASE DE DATOS ─────────────

// Nombre del contenedor MariaDB
// Docker resuelve este nombre a la IP 192.168.40.10 automáticamente
define('BD_HOST',    'logitrans-bd');
define('BD_NOMBRE',  'logitrans_db');
define('BD_USUARIO', 'logitrans_user');
define('BD_CLAVE',   'LogiTrans25*');

// Codificación de caracteres (necesario para tildes y ñ)
define('BD_CHARSET', 'utf8mb4');

// ── FUNCIÓN QUE CREA Y DEVUELVE LA CONEXIÓN ───────────────────
function conectar() {

    // DSN = cadena que identifica la base de datos
    // Formato: tipo:host=...;dbname=...;charset=...
    $dsn = "mysql:host=" . BD_HOST .
           ";dbname=" . BD_NOMBRE .
           ";charset=" . BD_CHARSET;

    try {
        $pdo = new PDO($dsn, BD_USUARIO, BD_CLAVE, [
            // Si algo falla, lanza un error detallado en lugar de fallar en silencio
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            // Los resultados vienen como arrays asociativos
            // Ejemplo: $fila["NOMBRE"] en lugar de $fila[0]
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        return $pdo;

    } catch (PDOException $e) {
        // PDOException es el tipo de error específico de PDO
        // getMessage() devuelve el mensaje de error exacto
        die("Error de conexion: " . $e->getMessage());
    }
}

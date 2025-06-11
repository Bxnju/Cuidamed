<?php
$host = "localhost";          // O el host de tu servidor
$dbname = "cuidamed";         // Nombre de tu base de datos
$username = "root";           // Usuario de la base de datos
$password = "";               // Contraseña (en local normalmente está vacía)

// Crear la conexión usando PDO
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    // Configurar el modo de errores para lanzar excepciones
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
    exit();
}
?>

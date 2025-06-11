<?php
session_start();
require_once '../db/connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = trim($_POST["correo"]);
    $contrasena = trim($_POST["contrasena"]);

    // Verificar credenciales
    $stmt = $pdo->prepare("SELECT id_usuario, nombre, contrasena, rol FROM usuarios WHERE correo = :correo");
    $stmt->bindParam(':correo', $correo);
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($contrasena, $usuario["contrasena"])) {
        // Autenticación correcta
        $_SESSION["id_usuario"] = $usuario["id_usuario"];
        $_SESSION["nombre"] = $usuario["nombre"];
        $_SESSION["rol"] = $usuario["rol"];

        if ($usuario["rol"] === "admin") {
            header("Location: ../pages/admin_dashboard.php");
        } else {
            header("Location: ../pages/dashboard.php");
        }
        exit();
    } else {
        // Credenciales inválidas
        echo "<script>alert('Correo o contraseña incorrectos'); window.location.href = '../pages/login.php';</script>";
        exit();
    }
} else {
    header("Location: ../pages/login.php");
    exit();
}
?>

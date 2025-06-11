<?php
require_once '../db/connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST["nombre"]);
    $correo = trim($_POST["correo"]);
    $telefono = trim($_POST["telefono"]);
    $contrasena = password_hash(trim($_POST["contrasena"]), PASSWORD_DEFAULT);

    try {
        // Verificar si el correo ya está registrado
        $stmt = $pdo->prepare("SELECT id_usuario FROM usuarios WHERE correo = :correo");
        $stmt->bindParam(':correo', $correo);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            echo "<script>alert('El correo ya está registrado.'); window.location.href = '../pages/register.php';</script>";
            exit();
        }

        // Insertar nuevo usuario
        $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, correo, telefono, contrasena, rol) VALUES (:nombre, :correo, :telefono, :contrasena, 'paciente')");
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':correo', $correo);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->bindParam(':contrasena', $contrasena);

        $stmt->execute();

        echo "<script>alert('Registro exitoso. Ahora puedes iniciar sesión.'); window.location.href = '../pages/login.php';</script>";
        exit();
    } catch (PDOException $e) {
        echo "Error al registrar usuario: " . $e->getMessage();
        exit();
    }
} else {
    header("Location: ../pages/register.php");
    exit();
}
?>

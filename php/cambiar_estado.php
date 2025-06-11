<?php
session_start();
require_once '../db/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['id_usuario'])) {
    $id_toma = intval($_POST['id_toma']);
    $nuevo_estado = $_POST['nuevo_estado'];

    $stmt = $pdo->prepare("UPDATE tomas_medicamento SET estado = :estado WHERE id_toma = :id_toma");
    $stmt->execute([':estado' => $nuevo_estado, ':id_toma' => $id_toma]);

    header("Location: ../pages/dashboard.php");
    exit();
} else {
    header("Location: ../pages/login.php");
    exit();
}

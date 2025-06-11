<?php
session_start();
require_once '../db/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['id_usuario'])) {
    $id_recordatorio = intval($_POST['id_recordatorio']);

    // Primero eliminar tomas relacionadas
    $stmt1 = $pdo->prepare("DELETE FROM tomas_medicamento WHERE id_recordatorio = :id_recordatorio");
    $stmt1->execute([':id_recordatorio' => $id_recordatorio]);

    // Luego eliminar el recordatorio
    $stmt2 = $pdo->prepare("DELETE FROM recordatorios WHERE id_recordatorio = :id_recordatorio");
    $stmt2->execute([':id_recordatorio' => $id_recordatorio]);

    header("Location: ../pages/dashboard.php");
    exit();
} else {
    header("Location: ../pages/login.php");
    exit();
}

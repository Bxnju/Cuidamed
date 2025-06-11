<?php
session_start();

// Si hay sesión iniciada, redirigir según el rol
if (isset($_SESSION['id_usuario']) && isset($_SESSION['rol'])) {
    if ($_SESSION['rol'] === 'admin') {
        header('Location: pages/admin_dashboard.php');
        exit();
    } else {
        header('Location: pages/dashboard.php');
        exit();
    }
} else {
    // No hay sesión, mostrar login
    header('Location: pages/login.php');
    exit();
}
?>

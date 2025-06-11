<?php
session_start();
require_once '../db/connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['id_usuario'])) {
    $id_usuario = $_SESSION['id_usuario'];

    $nombre_medicamento = trim($_POST['id_medicamento']);
    $stmt = $pdo->prepare("SELECT id_medicamento FROM medicamentos WHERE nombre = :nombre");
    $stmt->execute([':nombre' => $nombre_medicamento]);
    $medicamento = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$medicamento) {
        echo "El medicamento no existe.";
        exit();
    }

    $id_medicamento = intval($medicamento['id_medicamento']);

    $fecha_inicio_str = $_POST['fecha_inicio'] ?? '';
    $fecha_fin_str = $_POST['fecha_fin'] ?? '';
    $hora = $_POST['hora'] ?? '';
    $dias_semana = $_POST['dias_semana'] ?? [];
    $dosis = trim($_POST['dosis']);

    // Validaciones básicas
    if ($id_medicamento <= 0 || empty($fecha_inicio_str) || empty($fecha_fin_str) || empty($hora) || empty($dosis) || empty($dias_semana)) {
        echo "Datos incompletos o inválidos.";
        exit();
    }

    try {
        $fecha_inicio = new DateTime($fecha_inicio_str);
        $fecha_fin = new DateTime($fecha_fin_str);
        if ($fecha_inicio > $fecha_fin) {
            echo "La fecha de inicio debe ser menor o igual a la fecha final.";
            exit();
        }

        $interval = new DateInterval('P1D');
        $periodo = new DatePeriod($fecha_inicio, $interval, $fecha_fin->modify('+1 day'));

        foreach ($periodo as $fecha) {
            $dia_semana = intval($fecha->format('w')); // 0=domingo ... 6=sábado
            if (in_array($dia_semana, $dias_semana)) {
                $fecha_hora = $fecha->format('Y-m-d') . ' ' . $hora . ':00';

                // Insertar recordatorio
                $stmt = $pdo->prepare("INSERT INTO recordatorios (id_usuario, id_medicamento, fecha_hora, dosis, creado_por_admin) VALUES (:id_usuario, :id_medicamento, :fecha_hora, :dosis, FALSE)");
                $stmt->execute([
                    ':id_usuario' => $id_usuario,
                    ':id_medicamento' => $id_medicamento,
                    ':fecha_hora' => $fecha_hora,
                    ':dosis' => $dosis,
                ]);

                $id_recordatorio = $pdo->lastInsertId();

                // Insertar toma pendiente
                $stmt2 = $pdo->prepare("INSERT INTO tomas_medicamento (id_recordatorio, estado, fecha_registro) VALUES (:id_recordatorio, 'pendiente', NOW())");
                $stmt2->execute([':id_recordatorio' => $id_recordatorio]);
            }
        }

        echo "<script>alert('Recordatorios añadidos con éxito.'); window.location.href = '../pages/dashboard.php';</script>";
        exit();

    } catch (Exception $e) {
        echo "Error al añadir recordatorios: " . $e->getMessage();
        exit();
    }
} else {
    header("Location: ../pages/dashboard.php");
    exit();
}
?>
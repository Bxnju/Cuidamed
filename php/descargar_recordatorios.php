<?php
require_once '../db/connection.php';

if (isset($_GET['id_usuario'])) {
    $id_usuario = intval($_GET['id_usuario']);

    // Consultar los recordatorios del paciente
    $stmt = $pdo->prepare("
        SELECT r.fecha_hora, m.nombre AS medicamento, r.dosis
        FROM recordatorios r
        INNER JOIN medicamentos m ON r.id_medicamento = m.id_medicamento
        WHERE r.id_usuario = :id_usuario
        ORDER BY r.fecha_hora ASC
    ");
    $stmt->execute([':id_usuario' => $id_usuario]);
    $recordatorios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($recordatorios) > 0) {
        // Crear el archivo CSV
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="recordatorios_paciente_' . $id_usuario . '.csv"');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['Fecha y Hora', 'Medicamento', 'Dosis']); // Encabezados

        foreach ($recordatorios as $recordatorio) {
            fputcsv($output, $recordatorio);
        }

        fclose($output);
        exit();
    } else {
        echo "<script>alert('No hay recordatorios para este paciente.'); window.history.back();</script>";
        exit();
    }
} else {
    echo "<script>alert('ID de usuario no proporcionado.'); window.history.back();</script>";
    exit();
}
?>
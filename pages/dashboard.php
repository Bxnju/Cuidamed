<?php
session_start();
require_once '../db/connection.php';

if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'paciente') {
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];

// Próximas 24h
$stmt24h = $pdo->prepare("
    SELECT r.id_recordatorio, m.nombre AS medicamento, r.fecha_hora, r.dosis, tm.estado, tm.id_toma
    FROM recordatorios r
    INNER JOIN medicamentos m ON r.id_medicamento = m.id_medicamento
    LEFT JOIN tomas_medicamento tm ON r.id_recordatorio = tm.id_recordatorio
    WHERE r.id_usuario = :id_usuario
    AND r.fecha_hora BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 1 DAY)
    ORDER BY r.fecha_hora ASC
");
$stmt24h->execute([':id_usuario' => $id_usuario]);
$recordatorios_24h = $stmt24h->fetchAll(PDO::FETCH_ASSOC);

// Recordatorios pasados
$stmtPasados = $pdo->prepare("
    SELECT r.id_recordatorio, m.nombre AS medicamento, r.fecha_hora, r.dosis, tm.estado, tm.id_toma
    FROM recordatorios r
    INNER JOIN medicamentos m ON r.id_medicamento = m.id_medicamento
    LEFT JOIN tomas_medicamento tm ON r.id_recordatorio = tm.id_recordatorio
    WHERE r.id_usuario = :id_usuario
    AND r.fecha_hora < NOW()
    ORDER BY r.fecha_hora DESC
");
$stmtPasados->execute([':id_usuario' => $id_usuario]);
$recordatorios_pasados = $stmtPasados->fetchAll(PDO::FETCH_ASSOC);

// Próximos recordatorios en la próxima semana
$stmtProximos = $pdo->prepare("
    SELECT r.id_recordatorio, m.nombre AS medicamento, r.fecha_hora, r.dosis, tm.estado, tm.id_toma
    FROM recordatorios r
    INNER JOIN medicamentos m ON r.id_medicamento = m.id_medicamento
    LEFT JOIN tomas_medicamento tm ON r.id_recordatorio = tm.id_recordatorio
    WHERE r.id_usuario = :id_usuario
    AND r.fecha_hora > DATE_ADD(NOW(), INTERVAL 1 DAY)
    AND r.fecha_hora <= DATE_ADD(NOW(), INTERVAL 7 DAY)
    ORDER BY r.fecha_hora ASC
");
$stmtProximos->execute([':id_usuario' => $id_usuario]);
$recordatorios_proximos = $stmtProximos->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cuidamed - Dashboard Paciente</title>
    <link rel="icon" href="../assets/icon.png" type="image/png">
    <link rel="stylesheet" href="../styles/style.css" />
    <style>
        h2 {
            text-align: center;
            margin-bottom: 1em;
        }

        .flex-columns {
            display: flex;
            gap: 20px;
            justify-content: space-between;
        }

        .flex-columns>section {
            flex: 1;
            box-shadow: 0 10px 25px rgba(0, 123, 190, 0.2);
            padding: 15px;
            border-radius: 8px;
            max-height: 80vh;
            overflow-y: auto;
        }

        @media (max-width: 900px) {
            .flex-columns {
                flex-direction: column;
            }
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .btn-danger {
            background-color: #dc3545;
        }

        .btn-danger:hover {
            background-color: #a71d2a;
        }

        .estado-pendiente {
            color: orange;
        }

        .estado-tomado {
            color: green;
        }

        .estado-omitido {
            color: red;
        }

        .event-item {
            background: rgb(0, 10, 84);
            padding: 10px;
            border-radius: 5px;
            margin: 1em 0;
            color: white;
            font-weight: bold;
            min-width: 200px;
            max-width: 400px;
            flex-shrink: 0;
            /* Esto evita que el item se reduzca */
        }

        @media screen and (max-width: 900px) {

            .logo {
                width: 100%;
                max-width: 600px;
                margin-bottom: 20px;
            }

            .flex-columns section ul {
                display: flex;
                flex-wrap: nowrap;
                overflow-y: auto;
                gap: 1em;
            }

            .event-item {
                font-size: 1.2em;
                width: 100%;
            }
        }
    </style>
</head>

<body>

    <img class="logo" src="../assets/logo.png" alt="">

    <div class="container">
        <h1>Hola, <?php echo htmlspecialchars($_SESSION['nombre']); ?></h1>

        <div class="flex-columns">
            <!-- Próximas 24 horas -->
            <section>
                <h2>Próximas 24 horas</h2>
                <?php if (count($recordatorios_24h) > 0): ?>
                    <ul>
                        <?php foreach ($recordatorios_24h as $rec): ?>
                            <li class="event-item">
                                <strong>Medicamento:</strong> <?php echo htmlspecialchars($rec['medicamento']); ?><br>
                                <strong>Fecha y hora:</strong>
                                <?php echo date('d/m/Y H:i', strtotime($rec['fecha_hora'])); ?><br>
                                <strong>Dosis:</strong> <?php echo htmlspecialchars($rec['dosis']); ?><br>
                                <strong>Estado:</strong>
                                <?php
                                $estado = $rec['estado'] ?? 'pendiente';
                                $class = 'estado-pendiente';
                                if ($estado === 'tomado')
                                    $class = 'estado-tomado';
                                elseif ($estado === 'omitido')
                                    $class = 'estado-omitido';
                                ?>
                                <span class="<?= $class ?>"><?= htmlspecialchars($estado) ?></span><br>
                                <div style="display: flex; gap: 1em; margin-top: 10px;">
                                    <?php if ($estado === 'pendiente'): ?>
                                        <form method="post" action="../php/cambiar_estado.php" style="display:inline;">
                                            <input type="hidden" name="id_toma" value="<?= $rec['id_toma'] ?>">
                                            <input type="hidden" name="nuevo_estado" value="tomado">
                                            <button class="btn" style="font-size: 0.9em;" type="submit">Marcar como Tomado</button>
                                        </form>
                                    <?php endif; ?>
                                    <form method="post" action="../php/eliminar_recordatorio.php" style="display:inline;">
                                        <input type="hidden" name="id_recordatorio" value="<?= $rec['id_recordatorio'] ?>">
                                        <button style="font-size: 0.9em; height: 100%;" class="btn btn-danger" type="submit"
                                            onclick="return confirm('¿Seguro que quieres eliminar este recordatorio?');">
                                            Eliminar
                                        </button>
                                    </form>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>No tienes recordatorios en las próximas 24 horas.</p>
                <?php endif; ?>
            </section>

            <!-- Recordatorios Pasados -->
            <section>
                <h2>Recordatorios pasados</h2>
                <?php if (count($recordatorios_pasados) > 0): ?>
                    <ul>
                        <?php foreach ($recordatorios_pasados as $rec): ?>
                            <div class="event-item">
                                <strong>Medicamento:</strong> <?= htmlspecialchars($rec['medicamento']) ?><br>
                                <strong>Fecha y hora:</strong> <?= date('d/m/Y H:i', strtotime($rec['fecha_hora'])) ?><br>
                                <strong>Dosis:</strong> <?= htmlspecialchars($rec['dosis']) ?><br>
                                <strong>Estado:</strong>
                                <?php
                                $estado = $rec['estado'] ?? 'pendiente';
                                $class = 'estado-pendiente';
                                if ($estado === 'tomado')
                                    $class = 'estado-tomado';
                                elseif ($estado === 'omitido')
                                    $class = 'estado-omitido';
                                ?>
                                <span class="<?= $class ?>"><?= htmlspecialchars($estado) ?></span><br>
                                <form method="post" action="../php/eliminar_recordatorio.php" style="display:inline;">
                                    <input type="hidden" name="id_recordatorio" value="<?= $rec['id_recordatorio'] ?>">
                                    <button class="btn btn-danger" type="submit"
                                        onclick="return confirm('¿Seguro que quieres eliminar este recordatorio?');">
                                        Eliminar
                                    </button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>No tienes recordatorios pasados.</p>
                <?php endif; ?>
            </section>

            <!-- Próximos recordatorios -->
            <section>
                <h2>Próximos recordatorios (próxima semana)</h2>
                <?php if (count($recordatorios_proximos) > 0): ?>
                    <ul>
                        <?php foreach ($recordatorios_proximos as $rec): ?>
                            <div class="event-item">
                                <strong>Medicamento:</strong> <?= htmlspecialchars($rec['medicamento']) ?><br>
                                <strong>Fecha y hora:</strong> <?= date('d/m/Y H:i', strtotime($rec['fecha_hora'])) ?><br>
                                <strong>Dosis:</strong> <?= htmlspecialchars($rec['dosis']) ?><br>
                                <strong>Estado:</strong>
                                <?php
                                $estado = $rec['estado'] ?? 'pendiente';
                                $class = 'estado-pendiente';
                                if ($estado === 'tomado')
                                    $class = 'estado-tomado';
                                elseif ($estado === 'omitido')
                                    $class = 'estado-omitido';
                                ?>
                                <span class="<?= $class ?>"><?= htmlspecialchars($estado) ?></span><br>
                                <form method="post" action="../php/eliminar_recordatorio.php" style="display:inline;">
                                    <input type="hidden" name="id_recordatorio" value="<?= $rec['id_recordatorio'] ?>">
                                    <button style="font-size: 0.9em;" class="btn btn-danger" type="submit"
                                        onclick="return confirm('¿Seguro que quieres eliminar este recordatorio?');">
                                        Eliminar
                                    </button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>No tienes próximos recordatorios esta semana.</p>
                <?php endif; ?>
            </section>
        </div>

        <br>
        <p>
            <a class="btn" href="add_recordatorio.php">Añadir Recordatorio</a>
            <a class="btn" href="../php/logout.php">Cerrar Sesión</a>
        </p>
    </div>
</body>

</html>
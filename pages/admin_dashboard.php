<?php
session_start();
require_once '../db/connection.php';

if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// --- Botón de filtrar clientes ---
$clientes = [];
$busqueda = '';
if (isset($_GET['buscar'])) {
    $busqueda = trim($_GET['buscar']);
    $stmtClientes = $pdo->prepare("
        SELECT id_usuario, nombre, correo, telefono
        FROM usuarios 
        WHERE rol = 'paciente' 
          AND (nombre LIKE :busqueda OR correo LIKE :busqueda)
        ORDER BY nombre ASC
    ");
    $stmtClientes->execute([':busqueda' => "%$busqueda%"]);
    $clientes = $stmtClientes->fetchAll(PDO::FETCH_ASSOC);
}

// --- Botón de crear medicamento ---
if (isset($_POST['crear_medicamento'])) {
    $nombre_medicamento = trim($_POST['nombre_medicamento']);
    $descripcion_medicamento = trim($_POST['descripcion_medicamento']);

    if (!empty($nombre_medicamento)) {
        $stmtInsert = $pdo->prepare("INSERT INTO medicamentos (nombre, descripcion) VALUES (:nombre, :descripcion)");
        $stmtInsert->execute([
            ':nombre' => $nombre_medicamento,
            ':descripcion' => $descripcion_medicamento
        ]);
        echo "<p style='color: green;'>Medicamento creado correctamente.</p>";
    } else {
        echo "<p style='color: red;'>El nombre del medicamento es obligatorio.</p>";
    }
}

// Pacientes con medicamentos pendientes
$stmtPendientes = $pdo->prepare("
    SELECT u.nombre AS paciente, u.correo, m.nombre AS medicamento, r.fecha_hora, r.dosis, tm.estado
    FROM tomas_medicamento tm
    INNER JOIN recordatorios r ON tm.id_recordatorio = r.id_recordatorio
    INNER JOIN usuarios u ON r.id_usuario = u.id_usuario
    INNER JOIN medicamentos m ON r.id_medicamento = m.id_medicamento
    WHERE tm.estado = 'pendiente' AND r.fecha_hora <= NOW()
    ORDER BY r.fecha_hora ASC
");
$stmtPendientes->execute();
$pendientes = $stmtPendientes->fetchAll(PDO::FETCH_ASSOC);

// Recordatorios próximos
$stmtProximos = $pdo->prepare("
    SELECT u.nombre AS paciente, u.correo, m.nombre AS medicamento, r.fecha_hora, r.dosis, tm.estado, tm.id_toma
    FROM recordatorios r
    INNER JOIN usuarios u ON r.id_usuario = u.id_usuario
    INNER JOIN medicamentos m ON r.id_medicamento = m.id_medicamento
    LEFT JOIN tomas_medicamento tm ON r.id_recordatorio = tm.id_recordatorio
    WHERE r.fecha_hora >= NOW() AND r.fecha_hora <= DATE_ADD(NOW(), INTERVAL 1 DAY)
    ORDER BY r.fecha_hora ASC
");
$stmtProximos->execute();
$proximos = $stmtProximos->fetchAll(PDO::FETCH_ASSOC);

// Historial
$stmtHistorial = $pdo->prepare("
    SELECT u.nombre AS paciente, u.correo, m.nombre AS medicamento, r.fecha_hora, r.dosis, tm.estado
    FROM tomas_medicamento tm
    INNER JOIN recordatorios r ON tm.id_recordatorio = r.id_recordatorio
    INNER JOIN usuarios u ON r.id_usuario = u.id_usuario
    INNER JOIN medicamentos m ON r.id_medicamento = m.id_medicamento
    ORDER BY r.fecha_hora DESC
");
$stmtHistorial->execute();
$historial = $stmtHistorial->fetchAll(PDO::FETCH_ASSOC);
// Invertir la lista de historial para mostrar los más recientes primero
$historial = array_reverse($historial);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cuidamed - Panel de Administración</title>
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
            flex-wrap: wrap;
        }

        .flex-columns>section {
            flex: 1;
            box-shadow: 0 10px 25px rgba(0, 123, 190, 0.2);
            padding: 15px;
            border-radius: 8px;
            max-height: 80vh;
            overflow-y: auto;
            min-width: 280px;
        }

        .acciones {
            margin: 2em 0;
        }

        .acciones form,
        .acciones a {
            display: inline-block;
            margin-right: 10px;
        }

        .mensaje {
            color: green;
            font-weight: bold;
        }

        .cliente-item {
            background: rgb(0, 10, 84);
            padding: 10px;
            border-radius: 5px;
            margin: 1em 0;
            color: white;
            font-weight: bold;
        }

        .event-item {
            background: rgb(0, 10, 84);
            padding: 10px;
            border-radius: 5px;
            margin: 1em 0;
            color: white;
            font-weight: bold;
            min-width: 300px;
            max-width: 400px;
            flex-shrink: 0;
            /* Esto evita que el item se reduzca */
        }

        .medicine {
            max-width:100%;
        }

        @media screen and (max-width: 900px) {

            .container {
                width: 90%;
                box-shadow: 0 10px 25px rgba(0, 123, 190, 0.2);
            }

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
                font-size: 1em;
                width: 100%;
            }

            .medicine {
                min-width: 200px;
            }

            div:has(.medicine) h2{
                font-size: 1.4em;
                margin-top: 2em;
            }
        }
    </style>
</head>

<body>


    <img class="logo" src="../assets/logo.png" alt="Cuidamed Logo">

    <div class="container">

        <h1>Panel de Administración</h1>

        <?php if (!empty($mensaje)): ?>
            <p class="mensaje"><?php echo htmlspecialchars($mensaje); ?></p>
        <?php endif; ?>

        <div class="acciones">
            <a class="btn" href="../php/logout.php">Cerrar Sesión</a>
            <a class="btn" href="?ver=clientes">Ver Clientes</a>
            <a class="btn" href="?ver=medicamentos">Crear Medicamento</a>
            <?php if ((isset($_GET['ver']) && $_GET['ver'] === 'clientes') || (isset($_GET['ver']) && $_GET['ver'] === 'medicamentos')): ?>
                <a class=" btn btn_volver" href="admin_dashboard.php">Volver al Dashboard</a>
            <?php endif; ?>
        </div>

        <?php if (isset($_GET['ver']) && $_GET['ver'] === 'clientes'): ?>
            <section>
                <h2>Lista de Clientes</h2>
                <form method="get">
                    <input type="hidden" name="ver" value="clientes">
                    <div style="display: flex; gap: 1em;" class="form-group">
                        <input type="text" name="buscar" placeholder="Buscar por nombre o correo"
                            value="<?php echo htmlspecialchars($busqueda); ?>">
                        <button type="submit" class="btn">Filtrar</button>
                    </div>
                </form>
                <ul>
                    <?php
                    if (empty($busqueda)) {
                        // Si no hay búsqueda, mostrar todos los pacientes
                        $stmtClientes = $pdo->prepare("
                            SELECT id_usuario, nombre, correo, telefono
                            FROM usuarios 
                            WHERE rol = 'paciente'
                            ORDER BY nombre ASC
                        ");
                        $stmtClientes->execute();
                        $clientes = $stmtClientes->fetchAll(PDO::FETCH_ASSOC);
                    }
                    ?>
                    <?php if (count($clientes) > 0): ?>
                        <?php foreach ($clientes as $cliente): ?>
                            <div class="cliente-item">
                                <strong>Nombre:</strong> <?php echo htmlspecialchars($cliente['nombre']); ?><br>
                                <strong>Correo:</strong> <?php echo htmlspecialchars($cliente['correo']); ?><br>
                                <strong>Teléfono:</strong> <?php echo htmlspecialchars($cliente['telefono']); ?><br>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="text-align: center">No hay clientes registrados o no se encontraron resultados.</p>
                    <?php endif; ?>
                </ul>
            </section>

        <?php elseif (isset($_GET['ver']) && $_GET['ver'] === 'medicamentos'): ?>
            <section>
                <h2>Agregar Nuevo Medicamento</h2><br>
                <form method="post">
                    <input type="text" id="nombre_medicamento" name="nombre_medicamento"
                        placeholder="Nombre del medicamento" required>

                    <br><br>

                    <input type="text" id="descripcion_medicamento" name="descripcion_medicamento"
                        placeholder="Descripción del medicamento" required><br><br>

                    <button name="crear_medicamento" type="submit" class="btn">Agregar</button>
                </form>
            </section>

            <div style="display: flex; flex-direction: column;">
                <h2>Lista de Medicamentos</h2>
                <?php
                $stmtMedicamentos = $pdo->prepare("SELECT id_medicamento, nombre, descripcion FROM medicamentos ORDER BY id_medicamento DESC");
                $stmtMedicamentos->execute();
                $medicamentos = $stmtMedicamentos->fetchAll(PDO::FETCH_ASSOC);
                ?>
                <?php if (count($medicamentos) > 0): ?>
                    <div>
                        <?php foreach ($medicamentos as $medicamento): ?>
                            <div class="event-item medicine">
                                <strong>Nombre:</strong> <?php echo htmlspecialchars($medicamento['nombre']); ?><br>
                                <strong>Descripción:</strong> <?php echo htmlspecialchars($medicamento['descripcion']); ?><br>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p>No hay medicamentos registrados en el sistema.</p>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="flex-columns">
                <!-- SECCIÓN DE MEDICAMENTOS PENDIENTES -->
                <section>
                    <h2>Pacientes con medicamentos pendientes:</h2>
                    <?php if (count($pendientes) > 0): ?>
                        <ul>
                            <?php foreach ($pendientes as $pend): ?>
                                <div class="event-item">
                                    <strong>Paciente:</strong> <?php echo htmlspecialchars($pend['paciente']); ?>
                                    (<?php echo htmlspecialchars($pend['correo']); ?>)<br>
                                    <strong>Medicamento:</strong> <?php echo htmlspecialchars($pend['medicamento']); ?><br>
                                    <strong>Fecha y hora:</strong>
                                    <?php echo date('d/m/Y H:i', strtotime($pend['fecha_hora'])); ?><br>
                                    <strong>Dosis:</strong> <?php echo htmlspecialchars($pend['dosis']); ?><br>
                                    <strong>Estado:</strong> <span
                                        style="color: orange;"><?php echo htmlspecialchars($pend['estado']); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>No hay pacientes con medicamentos pendientes.</p>
                    <?php endif; ?>
                </section>

                <!-- SECCIÓN DE RECORDATORIOS PRÓXIMOS -->
                <section>
                    <h2>Recordatorios próximos (24 horas):</h2>
                    <?php if (count($proximos) > 0): ?>
                        <ul>
                            <?php foreach ($proximos as $prox): ?>
                                <div class="event-item">
                                    <strong>Paciente:</strong> <?php echo htmlspecialchars($prox['paciente']); ?>
                                    (<?php echo htmlspecialchars($prox['correo']); ?>)<br>
                                    <strong>Medicamento:</strong> <?php echo htmlspecialchars($prox['medicamento']); ?><br>
                                    <strong>Fecha y hora:</strong>
                                    <?php echo date('d/m/Y H:i', strtotime($prox['fecha_hora'])); ?><br>
                                    <strong>Dosis:</strong> <?php echo htmlspecialchars($prox['dosis']); ?><br>
                                    <strong>Estado:</strong>
                                    <?php
                                    $estado = $prox['estado'] ?? 'Pendiente';
                                    $color = 'orange';
                                    if ($estado === 'tomado')
                                        $color = 'green';
                                    elseif ($estado === 'omitido')
                                        $color = 'red';
                                    ?>
                                    <span style="color: <?= $color ?>;"><?= htmlspecialchars(ucfirst($estado)) ?></span>
                                </div>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>No hay recordatorios próximos en las próximas 24 horas.</p>
                    <?php endif; ?>
                </section>

                <!-- SECCIÓN DE HISTORIAL -->
                <section>
                    <h2>Historial completo de tomas:</h2>
                    <?php if (count($historial) > 0): ?>
                        <ul>
                            <?php foreach ($historial as $h): ?>
                                <div class="event-item">
                                    <strong>Paciente:</strong> <?php echo htmlspecialchars($h['paciente']); ?>
                                    (<?php echo htmlspecialchars($h['correo']); ?>)<br>
                                    <strong>Medicamento:</strong> <?php echo htmlspecialchars($h['medicamento']); ?><br>
                                    <strong>Fecha y hora:</strong> <?php echo date('d/m/Y H:i', strtotime($h['fecha_hora'])); ?><br>
                                    <strong>Dosis:</strong> <?php echo htmlspecialchars($h['dosis']); ?><br>
                                    <strong>Estado:</strong>
                                    <?php
                                    $estado = $h['estado'] ?? 'Pendiente';
                                    $color = 'orange';
                                    if ($estado === 'tomado')
                                        $color = 'green';
                                    elseif ($estado === 'omitido')
                                        $color = 'red';
                                    ?>
                                    <span style="color: <?= $color ?>;"><?= htmlspecialchars(ucfirst($estado)) ?></span>
                                </div>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>No hay tomas en el historial.</p>
                    <?php endif; ?>
                </section>
            </div>
        <?php endif; ?>

    </div>
</body>

</html>
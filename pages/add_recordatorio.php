<?php
session_start();
require_once '../db/connection.php';

if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'paciente') {
    header("Location: login.php");
    exit();
}

// Traer lista de medicamentos para el select
$stmt = $pdo->query("SELECT id_medicamento, nombre FROM medicamentos ORDER BY nombre ASC");
$medicamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cuidamed - Añadir Recordatorio</title>
    <link rel="icon" href="../assets/icon.png" type="image/png">
    <link rel="stylesheet" href="../styles/style.css" />

    <style>
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
        }
    </style>
</head>

<body>

    <img class="logo" src="../assets/logo.png" alt="">

    <div class="container"">
        <p><a class=" btn btn_volver" href="dashboard.php">Volver al Dashboard</a></p>
        <h1>Añadir Recordatorio</h1>
        <form action="../php/add_recordatorio.php" method="post" class="form-recordatorio">


            <label for="medicamento">Medicamento:</label>
            <input type="text" list="medicamentos" name="id_medicamento" id="medicamento" required>
            <datalist id="medicamentos">
                <option value="" disabled selected>Selecciona un medicamento</option>
                <?php foreach ($medicamentos as $med): ?>
                    <option value="<?php echo htmlspecialchars($med['nombre']); ?>">
                    <?php endforeach; ?>
            </datalist>

            <label for="fecha_inicio">Fecha Inicio:</label>
            <input type="date" id="fecha_inicio" name="fecha_inicio" required />

            <label for="fecha_fin">Fecha Fin:</label>
            <input type="date" id="fecha_fin" name="fecha_fin" required />

            <label for="hora">Hora:</label>
            <input type="time" id="hora" name="hora" required />

            <fieldset class="checkbox-group">
                <legend>Días de la semana:</legend>
                <label><input type="checkbox" name="dias_semana[]" value="1" /> Lunes</label>
                <label><input type="checkbox" name="dias_semana[]" value="2" /> Martes</label>
                <label><input type="checkbox" name="dias_semana[]" value="3" /> Miércoles</label>
                <label><input type="checkbox" name="dias_semana[]" value="4" /> Jueves</label>
                <label><input type="checkbox" name="dias_semana[]" value="5" /> Viernes</label>
                <label><input type="checkbox" name="dias_semana[]" value="6" /> Sábado</label>
                <label><input type="checkbox" name="dias_semana[]" value="0" /> Domingo</label>
            </fieldset>

            <label for="dosis">Dosis:</label>
            <input type="text" name="dosis" id="dosis" placeholder="Ej: 1 tableta, 200 mg" required />

            <button type="submit" class="btn">Guardar Recordatorio</button>
        </form>
    </div>
</body>

</html>
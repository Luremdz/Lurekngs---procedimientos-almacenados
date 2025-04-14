 <!-- -- procedimiento para consultar el historial de pagos que se tiene 
-- de igual modo se declaran ciertas reglas para validar la información 
-- asi mismo tiene una traccion que permite actualizar o insertar dependiendo de la situación que requiera -->


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Pagos</title>
    <link rel="stylesheet" href="estilos.css">
    <style>
        .btn-regresar {
            position: absolute;
            top: 20px;
            left: 20px;
            padding: 10px 20px;
            background-color:rgb(188, 17, 17);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
            z-index: 1000;
        }

        .btn-regresar:hover {
            background-color:rgb(28, 212, 11);
            color: white;
        }

        @media (max-width: 768px) {
            .btn-regresar {
                top: 10px;
                left: 10px;
                padding: 8px 15px;
                font-size: 0.9em;
            }

            .header-principal h1 {
                margin-top: 40px;
            }
        }

        .filtro-form {
            margin-top: 100px;
            text-align: center;
        }

        .tabla-estudiantes {
            width: 90%;
            margin: 20px auto;
            border-collapse: collapse;
        }

        .tabla-estudiantes th, .tabla-estudiantes td {
            border: 1px solid #ccc;
            padding: 10px;
        }

        .mensaje-vacio, .mensaje-error {
            text-align: center;
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <a href="principal.php" class="btn-regresar">← Volver a Principal</a>

    <header class="header-principal">
        <h1>Procedimiento Almacenado: Historial de Pagos</h1>
    </header>

    <div class="filtro-form">
        <form method="GET">
            <label for="estado">Filtrar por estado:</label>
            <select name="estado" id="estado">
                <option value="activo" <?= (isset($_GET['estado']) && $_GET['estado'] == 'activo') ? 'selected' : '' ?>>Activo</option>
                <option value="graduado" <?= (isset($_GET['estado']) && $_GET['estado'] == 'graduado') ? 'selected' : '' ?>>Graduado</option>
                <option value="retirado" <?= (isset($_GET['estado']) && $_GET['estado'] == 'retirado') ? 'selected' : '' ?>>Retirado</option>
            </select>
            <button type="submit">Consultar</button>
        </form>
    </div>

    <div class="tabla-container">
        <?php
        include 'ConexionDB.php';

        $estado = isset($_GET['estado']) ? $_GET['estado'] : 'activo';

        // Llamar al procedimiento almacenado
        $sql = "CALL historial_pagos('$estado', 0, '', '', '', '', NULL, NULL, '', '')";

        if ($result = mysqli_query($conn, $sql)) {
            echo '<h2>Historial de Pagos de Estudiantes (' . htmlspecialchars(ucfirst($estado)) . ')</h2>';
            echo '<table class="tabla-estudiantes">';

            if (mysqli_num_rows($result) > 0) {
                $fields = mysqli_fetch_fields($result);
                echo '<tr>';
                foreach ($fields as $field) {
                    echo '<th>' . str_replace('_', ' ', $field->name) . '</th>';
                }
                echo '</tr>';

                while ($row = mysqli_fetch_assoc($result)) {
                    echo '<tr>';
                    foreach ($row as $value) {
                        echo '<td>' . nl2br(htmlspecialchars($value)) . '</td>';
                    }
                    echo '</tr>';
                }
            } else {
                echo '<tr><td colspan="6" class="mensaje-vacio">No hay historial de pagos disponible.</td></tr>';
            }

            echo '</table>';
            mysqli_free_result($result);
        } else {
            echo '<p class="mensaje-error">Error al ejecutar el procedimiento: ' . mysqli_error($conn) . '</p>';
        }

        mysqli_close($conn);
        ?>
    </div>

    <footer class="footer-principal">
        <p>Equipo de práctica de procedimientos: Moo Dzul Luis Reyes, Martín Arceo Yesenia Guadalupe, Canché Gómez Diego Emanuel y Ciau Tuz Selena Rocio</p>
    </footer>
</body>
</html>

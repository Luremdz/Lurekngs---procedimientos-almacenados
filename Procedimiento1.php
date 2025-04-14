  <!-- Obtener estudiantes que tienen un promedio superior al promedio general (Usando la tabla estudiante e Historial Academico)
  El procedimiento obtiene una lista de estudiantes cuyo promedio de calificaciones es superior al promedio general de todos 
  los estudiantes, con la opción de filtrar por estado (Activo,graduado o retirado (este procedimiento no tinene transaccion)). -->
  <!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Procedimiento 2</title>
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
    </style>
</head>
<body>
    <a href="principal.php" class="btn-regresar">← Volver a Principal</a>

    <header class="header-principal">
        <h1>Procedimiento Almacenado y Subconsulta 1</h1>
    </header>

    <div class="tabla-container">
        <?php
        include 'ConexionDB.php';

        // Procedimiento: ObtenerEstudiantesConPromedioSuperior
        $sql = "CALL ObtenerEstudiantesConPromedioSuperior(null)"; // cambiar NULL por 'Activo', 'Graduado'

        if ($result = mysqli_query($conn, $sql)) {
            echo '<h2>Estudiantes con Promedio Superior</h2>';
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
                        echo '<td>' . htmlspecialchars($value) . '</td>';
                    }
                    echo '</tr>';
                }
            } else {
                echo '<tr><td colspan="6" class="mensaje-vacio">No hay estudiantes con promedio superior.</td></tr>';
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

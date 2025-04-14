  <!-- Procedimiento almacenado, Datos de estudiantes con sus cursos y Subconsulta
  Cursos que nunca han sido asignados a un profesor (Cursos, Asignacion_Profesores, Horarios_Cursos) -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Procedimiento 2</title>
    <link rel="stylesheet" href="estilos.css">
    <style>
        /* Estilos solo para el botón */
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
    <!-- Botón de regreso -->
    <a href="principal.php" class="btn-regresar">← Volver a Principal</a>

    <!-- Header -->
    <header class="header-principal">
        <h1>Procedimiento Almacenado y Subconsulta 2</h1>
    </header>

    <div class="tabla-container">
        <?php
        include 'ConexionDB.php';
        
        $sql = "CALL Datos_estudiantes_cursos()";
        
        if ($result = mysqli_multi_query($conn, $sql)) {
            $resultSetCount = 1;
            
            do {
                $result = mysqli_store_result($conn);
                
                if ($resultSetCount <= 2) {
                    echo '<h2>' . ($resultSetCount == 1 ? 'Estudiantes con sus Cursos' : 'Cursos sin Profesor (subconsulta)') . '</h2>';
                    echo '<table class="' . ($resultSetCount == 1 ? 'tabla-estudiantes' : 'tabla-cursos') . '">';
                    
                    if ($result && mysqli_num_rows($result) > 0) {
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
                        echo '<tr><td colspan="5" class="mensaje-vacio">No hay datos disponibles</td></tr>';
                    }
                    
                    echo '</table>';
                }
                
                if ($result instanceof mysqli_result) {
                    mysqli_free_result($result);
                }
                
                $resultSetCount++;
            } while (mysqli_next_result($conn));
            
        } else {
            echo '<p class="mensaje-error">Error: ' . mysqli_error($conn) . '</p>';
        }
        
        mysqli_close($conn);
        ?>
    </div>
    <footer class="footer-principal">
        <p>Equipo de práctica de procedimientos: Moo Dzul Luis Reyes, Martín Arceo Yesenia Guadalupe, Canché Gómez Diego Emanuel y Ciau Tuz Selena Rocio</p>
    </footer>
</body>
</html>
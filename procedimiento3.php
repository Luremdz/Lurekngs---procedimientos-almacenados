<?php
// Procesamiento PHP cuando se hace la solicitud AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'ejecutar_procedimiento') {
    include 'ConexionDB.php';
    
    try {
        // Parámetros para el procedimiento almacenado
        $params = [
            'nombre' => 'Evento Ejemplo',
            'descripcion' => 'Descripción de ejemplo',
            'tipo' => 'Taller',
            'fecha' => date('Y-m-d'),
            'hora_inicio' => '09:00:00',
            'hora_fin' => '11:00:00',
            'lugar' => 'Aula 101',
            'facultad_id' => NULL,
            'departamento_id' => NULL,
            'publico_objetivo' => 'Estudiantes'
        ];
        
        // Llamada al procedimiento almacenado
        $sql = "CALL sp_RegistrarYListarEventos(
            '".mysqli_real_escape_string($conn, $params['nombre'])."',
            '".mysqli_real_escape_string($conn, $params['descripcion'])."',
            '".mysqli_real_escape_string($conn, $params['tipo'])."',
            '".mysqli_real_escape_string($conn, $params['fecha'])."',
            '".mysqli_real_escape_string($conn, $params['hora_inicio'])."',
            '".mysqli_real_escape_string($conn, $params['hora_fin'])."',
            '".mysqli_real_escape_string($conn, $params['lugar'])."',
            ".($params['facultad_id'] ?? 'NULL').",
            ".($params['departamento_id'] ?? 'NULL').",
            '".mysqli_real_escape_string($conn, $params['publico_objetivo'])."',
            @resultado
        )";
        
        // Ejecutar el procedimiento
        if (mysqli_multi_query($conn, $sql)) {
            $output = '';
            
            // Procesar solo el primer conjunto de resultados (eventos)
            if ($result = mysqli_store_result($conn)) {
                $output .= '<h2 class="section-title">Eventos Registrados</h2>';
                
                if (mysqli_num_rows($result) > 0) {
                    $output .= '<table><tr>';
                    
                    // Encabezados
                    $fields = mysqli_fetch_fields($result);
                    foreach ($fields as $field) {
                        $output .= '<th>'.htmlspecialchars(str_replace('_', ' ', $field->name)).'</th>';
                    }
                    $output .= '</tr>';
                    
                    // Datos
                    while ($row = mysqli_fetch_assoc($result)) {
                        $output .= '<tr>';
                        foreach ($row as $value) {
                            $output .= '<td>'.htmlspecialchars($value).'</td>';
                        }
                        $output .= '</tr>';
                    }
                    
                    $output .= '</table>';
                } else {
                    $output .= '<p>No hay eventos registrados</p>';
                }
                
                mysqli_free_result($result);
            }
            
            // Descarta los otros conjuntos de resultados (estudiantes)
            while (mysqli_next_result($conn));
            
            echo $output;
        } else {
            throw new Exception(mysqli_error($conn));
        }
    } catch (Exception $e) {
        echo '<div class="message error">Error: '.htmlspecialchars($e->getMessage()).'</div>';
    } finally {
        if (isset($conn)) {
            mysqli_close($conn);
        }
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Eventos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 20px;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }
        .btn:hover {
            background-color: #45a049;
        }
        .btn-back {
            background-color: #6c757d;
        }
        .btn-back:hover {
            background-color: #5a6268;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .section-title {
            margin-top: 30px;
            color: #2c3e50;
            border-bottom: 2px solid #3498db;
            padding-bottom: 5px;
        }
        .message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .loading {
            text-align: center;
            padding: 20px;
            font-style: italic;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="principal.php" class="btn btn-back">← Volver</a>
        <h1>Listado de Eventos</h1>
        
        <button id="btnCargar" class="btn">Mostrar Eventos</button>
        
        <div id="resultados">
            <!-- Aquí se cargarán los eventos -->
        </div>
    </div>

    <script>
        document.getElementById('btnCargar').addEventListener('click', function() {
            const resultadosDiv = document.getElementById('resultados');
            resultadosDiv.innerHTML = '<div class="loading">Cargando eventos...</div>';
            
            // Configurar la solicitud
            const formData = new FormData();
            formData.append('action', 'ejecutar_procedimiento');
            
            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor');
                }
                return response.text();
            })
            .then(data => {
                resultadosDiv.innerHTML = data;
            })
            .catch(error => {
                resultadosDiv.innerHTML = `<div class="error">Error al cargar los eventos: ${error.message}</div>`;
                console.error('Error:', error);
            });
        });
    </script>
</body>
</html>
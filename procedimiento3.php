<?php
// Procesamiento PHP cuando se hace la solicitud AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'ejecutar_procedimiento') {
    include 'ConexionDB.php';
    
    try {
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
        
        if (mysqli_multi_query($conn, $sql)) {
            $output = '';
            
            if ($result = mysqli_store_result($conn)) {
                $output .= '<h2 class="section-title">Eventos Registrados</h2>';
                
                if (mysqli_num_rows($result) > 0) {
                    $output .= '<table><tr>';
                    $fields = mysqli_fetch_fields($result);
                    foreach ($fields as $field) {
                        $output .= '<th>'.htmlspecialchars(str_replace('_', ' ', $field->name)).'</th>';
                    }
                    $output .= '</tr>';
                    
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
            background-color: #ffffff; /* fondo blanco */
            color: #000000;
            margin: 0;
            padding: 20px;
        }

        .container {
            background-color: #004080; /* contenido azul */
            color: #ffffff; /* letras blancas dentro */
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            border-radius: 8px;
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #cc0000;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 20px;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }

        .btn:hover {
            background-color: #a80000;
        }

        .btn-back {
            background-color: #990000;
        }

        .btn-back:hover {
            background-color: #770000;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            background-color: #003366;
        }

        th, td {
            border: 1px solid #cccccc;
            padding: 12px;
            text-align: left;
            color: white;
        }

        th {
            background-color: #0059b3;
        }

        tr:nth-child(even) {
            background-color: #002244;
        }

        .section-title {
            margin-top: 30px;
            border-bottom: 2px solid #66ccff;
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
            color: white;
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

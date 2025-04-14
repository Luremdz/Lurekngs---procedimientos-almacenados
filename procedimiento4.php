<?php
// Procesamiento PHP cuando se hace la solicitud AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'registrar_pago') {
    include 'ConexionDB.php';
    
    try {
        // Parámetros para el procedimiento almacenado
        $params = [
            'estudiante_id' => $_POST['estudiante_id'] ?? 1,
            'monto' => $_POST['monto'] ?? 1000.00,
            'concepto' => $_POST['concepto'] ?? 'Colegiatura',
            'metodo_pago' => $_POST['metodo_pago'] ?? 'Transferencia',
            'semestre_id' => $_POST['semestre_id'] ?? 1
        ];
        
        // Llamada al procedimiento almacenado
        $sql = "CALL sp_registrar_pago(
            ".intval($params['estudiante_id']).",
            ".floatval($params['monto']).",
            '".mysqli_real_escape_string($conn, $params['concepto'])."',
            '".mysqli_real_escape_string($conn, $params['metodo_pago'])."',
            ".intval($params['semestre_id']).",
            @resultado
        )";
        
        // Ejecutar el procedimiento
        if (mysqli_query($conn, $sql)) {
            // Obtener el mensaje de resultado
            $res = mysqli_query($conn, "SELECT @resultado AS resultado");
            if ($res && $row = mysqli_fetch_assoc($res)) {
                $output = '<div class="mensaje '.($row['resultado'] === 'Pago registrado con éxito' ? 'mensaje-exito' : 'mensaje-error').'">';
                $output .= htmlspecialchars($row['resultado']);
                $output .= '</div>';
                
                // Mostrar los últimos pagos registrados
                $output .= '<h2>Últimos Pagos Registrados</h2>';
                
                $query = "SELECT p.pago_id, e.nombre AS estudiante, p.monto, p.fecha_pago, 
                         p.concepto, p.metodo_pago, s.nombre AS semestre
                         FROM Pagos p
                         JOIN Estudiantes e ON p.estudiante_id = e.estudiante_id
                         JOIN Semestres s ON p.semestre_id = s.semestre_id
                         ORDER BY p.fecha_pago DESC LIMIT 10";
                
                if ($result = mysqli_query($conn, $query)) {
                    if (mysqli_num_rows($result) > 0) {
                        $output .= '<table class="tabla-pagos">';
                        $output .= '<tr>';
                        $output .= '<th>ID Pago</th><th>Estudiante</th><th>Monto</th><th>Fecha</th>';
                        $output .= '<th>Concepto</th><th>Método</th><th>Semestre</th>';
                        $output .= '</tr>';
                        
                        while ($row = mysqli_fetch_assoc($result)) {
                            $output .= '<tr>';
                            $output .= '<td>'.htmlspecialchars($row['pago_id']).'</td>';
                            $output .= '<td>'.htmlspecialchars($row['estudiante']).'</td>';
                            $output .= '<td>$'.number_format($row['monto'], 2).'</td>';
                            $output .= '<td>'.htmlspecialchars($row['fecha_pago']).'</td>';
                            $output .= '<td>'.htmlspecialchars($row['concepto']).'</td>';
                            $output .= '<td>'.htmlspecialchars($row['metodo_pago']).'</td>';
                            $output .= '<td>'.htmlspecialchars($row['semestre']).'</td>';
                            $output .= '</tr>';
                        }
                        
                        $output .= '</table>';
                    } else {
                        $output .= '<p class="mensaje-vacio">No hay pagos registrados</p>';
                    }
                    mysqli_free_result($result);
                } else {
                    $output .= '<div class="mensaje-error">Error al obtener pagos: '.htmlspecialchars(mysqli_error($conn)).'</div>';
                }
                
                echo $output;
            } else {
                throw new Exception('No se pudo obtener el resultado del procedimiento');
            }
        } else {
            throw new Exception(mysqli_error($conn));
        }
    } catch (Exception $e) {
        echo '<div class="mensaje-error">Error: '.htmlspecialchars($e->getMessage()).'</div>';
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
    <title>Procedimiento Almacenado - Registro de Pagos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
            position: relative;
        }
        
        .btn-regresar {
            position: absolute;
            top: 20px;
            left: 20px;
            padding: 10px 20px;
            background-color: rgb(188, 17, 17);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
            z-index: 1000;
        }
        
        .btn-regresar:hover {
            background-color: rgb(28, 212, 11);
            color: white;
        }
        
        .header-principal {
            text-align: center;
            padding: 20px 0;
            background-color: #fff;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .header-principal h1 {
            color: #333;
            margin: 0;
            font-size: 24px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: white;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        
        .formulario-pago {
            max-width: 600px;
            margin: 0 auto 30px;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 5px;
            box-shadow: 0 0 5px rgba(0,0,0,0.05);
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }
        
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        
        .btn-registrar {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        
        .btn-registrar:hover {
            background-color: #45a049;
        }
        
        .tabla-pagos {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .tabla-pagos th {
            background-color: #f2f2f2;
            padding: 12px;
            text-align: left;
            border-bottom: 2px solid #ddd;
        }
        
        .tabla-pagos td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        
        .tabla-pagos tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .mensaje {
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        
        .mensaje-exito {
            background-color: #dff0d8;
            color: #3c763d;
            border: 1px solid #d6e9c6;
        }
        
        .mensaje-error {
            background-color: #f2dede;
            color: #a94442;
            border: 1px solid #ebccd1;
        }
        
        .mensaje-vacio {
            color: #777;
            font-style: italic;
            text-align: center;
            padding: 20px;
        }
        
        .loading {
            text-align: center;
            color: #555;
            padding: 20px;
            font-style: italic;
        }
        
        @media (max-width: 768px) {
            .btn-regresar {
                top: 10px;
                left: 10px;
                padding: 8px 15px;
                font-size: 14px;
            }
            
            .header-principal h1 {
                font-size: 20px;
                margin-top: 40px;
            }
            
            .container {
                padding: 15px;
            }
            
            .tabla-pagos {
                font-size: 14px;
            }
            
            .tabla-pagos th,
            .tabla-pagos td {
                padding: 8px;
            }
        }
    </style>
</head>
<body>
    <a href="principal.php" class="btn-regresar">← Volver a Principal</a>
    
    <header class="header-principal">
        <h1>Procedimiento Almacenado - Registro de Pagos</h1>
    </header>
    
    <div class="container">
        <div class="formulario-pago">
            <form id="formPago">
                <div class="form-group">
                    <label for="estudiante_id">ID Estudiante:</label>
                    <input type="number" id="estudiante_id" name="estudiante_id" required>
                </div>
                
                <div class="form-group">
                    <label for="monto">Monto:</label>
                    <input type="number" id="monto" name="monto" step="0.01" required>
                </div>
                
                <div class="form-group">
                    <label for="concepto">Concepto:</label>
                    <input type="text" id="concepto" name="concepto" required>
                </div>
                
                <div class="form-group">
                    <label for="metodo_pago">Método de Pago:</label>
                    <select id="metodo_pago" name="metodo_pago" required>
                        <option value="Transferencia">Transferencia</option>
                        <option value="Efectivo">Efectivo</option>
                        <option value="Tarjeta">Tarjeta</option>
                        <option value="Cheque">Cheque</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="semestre_id">ID Semestre:</label>
                    <input type="number" id="semestre_id" name="semestre_id" required>
                </div>
                
                <button type="button" id="btnRegistrar" class="btn-registrar">Registrar Pago</button>
            </form>
        </div>
        
        <div id="resultados">
            <!-- Aquí se mostrarán los resultados -->
        </div>
    </div>

    <script>
        document.getElementById('btnRegistrar').addEventListener('click', function() {
            const resultadosDiv = document.getElementById('resultados');
            resultadosDiv.innerHTML = '<div class="loading">Procesando registro de pago...</div>';
            
            // Validación básica
            const estudianteId = document.getElementById('estudiante_id').value;
            const monto = document.getElementById('monto').value;
            const concepto = document.getElementById('concepto').value;
            const metodoPago = document.getElementById('metodo_pago').value;
            const semestreId = document.getElementById('semestre_id').value;
            
            if (!estudianteId || !monto || !concepto || !metodoPago || !semestreId) {
                resultadosDiv.innerHTML = '<div class="mensaje-error">Todos los campos son requeridos</div>';
                return;
            }
            
            // Configurar la solicitud
            const formData = new FormData(document.getElementById('formPago'));
            formData.append('action', 'registrar_pago');
            
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
                resultadosDiv.innerHTML = '<div class="mensaje-error">Error al registrar el pago: ' + error.message + '</div>';
                console.error('Error:', error);
            });
        });
    </script>
</body>
</html>
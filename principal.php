<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Procedimientos SQL - universidad_db</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="Estilos.css">
    <style>
        /* Estilos para el menú desplegable */
        .dropdown-container {
            position: absolute;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }
        
        .dropdown-menu {
            min-width: 12rem;
        }
        
        @media (max-width: 768px) {
            .dropdown-container {
                position: relative;
                top: 0;
                right: 0;
                display: flex;
                justify-content: flex-end;
                margin-bottom: 15px;
            }
        }

        .highlight-text {
            font-size: 4rem;
            font-weight: bold;
            color: #0d6efd;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
            padding: 20px;
            border: 3px solid #0d6efd;
            border-radius: 15px;
            background-color: #f8f9fa;
            text-align: center;
            margin-top: 50px;
        }
    </style>
</head>
<body>
    <header>
        <img src="image.png" alt="Tecnológico Nacional de México">
        <h1>Universidad DB</h1>
        
        <!-- Menú desplegable modificado -->
        <div class="dropdown-container">
            <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                    Procedimientos A
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                    <li><a class="dropdown-item" href="Procedimiento1.php">Procedimiento 1</a></li>
                    <li><a class="dropdown-item" href="Procedimiento2.php?pagina=1">Procedimiento 2</a></li>
                    <li><a class="dropdown-item" href="Procedimiento3.php?pagina=1">Procedimiento 3</a></li>
                    <li><a class="dropdown-item" href="Procedimiento4.php?pagina=1">Procedimiento 4</a></li>
                    <li><a class="dropdown-item" href="Procedimiento5.php?pagina=1">Procedimiento 5</a></li>
                </ul>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="highlight-text">
            PRÁCTICA PROCEDIMIENTOS ALMAACENADOS - PROGRAMACIÓN AVANZADA DE BASE DE DATOS
        </div>
    </div>

    <footer>
        <p>Equipo de práctica de procedimientos: Moo Dzul Luis Reyes, Martín Arceo Yesenia Guadalupe, Canché Gómez Diego Emanuel y Ciau Tuz Selena Rocio</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
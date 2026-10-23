<?php
declare(strict_types=1);

// Bloque 1: Configuración para mostrar errores en pantalla durante el desarrollo
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Variables iniciales para control de vistas
$mensajeError = "";
$exito = false;
$datosResguardo = [];
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscripción Comic-Con DAW 2026</title>
</head>

<body>

    <div>
        <h1>Comic-Con DAW 2026 - Inscripción</h1>

        <?php if (!empty($mensajeError)): ?>
            <div>
                <strong>Error:</strong> <?= htmlspecialchars($mensajeError) ?>
            </div>
        <?php endif; ?>

        <!-- Bloque 2: El Formulario HTML (Envía por POST al mismo archivo) -->
        <form action="" method="POST">
            <div>
                <label for="nombre">Nombre completo:</label><br>
                <input type="text" id="nombre" name="nombre" required>
            </div>

            <div>
                <label for="edad">Edad:</label><br>
                <input type="number" id="edad" name="edad" min="1" required>
            </div>

            <div>
                <label for="tipo_entrada">Tipo de Entrada:</label><br>
                <select id="tipo_entrada" name="tipo_entrada" required>
                    <option value="">-- Selecciona una opción --</option>
                    <option value="General">General (30 €)</option>
                    <option value="VIP">VIP (60 €)</option>
                    <option value="Infantil">Infantil (10 €)</option>
                </select>
            </div>

            <fieldset>
                <legend>Talleres Especiales (Opcional):</legend>
                <label>
                    <input type="checkbox" name="talleres[]" value="videojuegos"> Torneo de Videojuegos (10 €)
                </label><br>
                <label>
                    <input type="checkbox" name="talleres[]" value="cosplay"> Taller de Cosplay (15 €)
                </label><br>
                <label>
                    <input type="checkbox" name="talleres[]" value="rol"> Zona de Rol en Vivo (5 €)
                </label>
            </fieldset>

            <button type="submit">Procesar Inscripción</button>
        </form>
    </div>

</body>

</html>
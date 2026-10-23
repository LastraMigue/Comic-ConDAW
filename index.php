<?php
declare(strict_types=1);

// Configuración para mostrar errores en pantalla durante el desarrollo
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Variables iniciales para control
$mensajeError = "";
$exito = false;
$datosResguardo = [];
$resultadoLog = "Fallido";

// Función 1: Calcula el precio base
function calcularPrecioBase(string $tipo): int
{
    switch ($tipo) {
        case 'General':
            return 30;
        case 'VIP':
            return 60;
        case 'Infantil':
            return 10;
        default:
            return 0;
    }
}

// Función 2: Procesa la inscripción y recorre los talleres con foreach
function procesarInscripcion(string $nombre, int $edad, string $tipoEntrada, array $talleresSeleccionados): array
{
    // Validación estricta: edad positiva
    if ($edad <= 0) {
        throw new Exception("La edad debe ser un número entero positivo.");
    }

    // Validación estricta: entrada VIP
    if ($tipoEntrada === 'VIP' && $edad < 18) {
        throw new Exception("La entrada VIP está restringida a mayores de edad.");
    }

    $precioBase = calcularPrecioBase($tipoEntrada);
    $costeTalleres = 0;
    $listaTalleres = [];

    // Bucle foreach sencillo para sumar los talleres
    foreach ($talleresSeleccionados as $taller) {
        if ($taller === 'videojuegos') {
            $costeTalleres += 10;
            $listaTalleres[] = ['nombre' => 'Torneo de Videojuegos', 'precio' => 10];
        } elseif ($taller === 'cosplay') {
            $costeTalleres += 15;
            $listaTalleres[] = ['nombre' => 'Taller de Cosplay', 'precio' => 15];
        } elseif ($taller === 'rol') {
            $costeTalleres += 5;
            $listaTalleres[] = ['nombre' => 'Zona de Rol en Vivo', 'precio' => 5];
        }
    }

    $precioTotal = $precioBase + $costeTalleres;

    // Devolvemos los datos listos para el resguardo
    return [
        'nombre' => $nombre,
        'tipo' => $tipoEntrada,
        'precio_base' => $precioBase,
        'talleres' => $listaTalleres,
        'total' => $precioTotal
    ];
}

// Bloque de control Try / Catch / Finally
try {
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        // Recuperación segura con Operador de Fusión de Nulos (??)
        $nombreInput = trim($_POST['nombre'] ?? '');
        $edadInput = (int) ($_POST['edad'] ?? 0);
        $tipoInput = $_POST['tipo_entrada'] ?? '';
        $talleresInput = $_POST['talleres'] ?? [];

        // Comprobar campos vacíos obligatorios
        if (empty($nombreInput) || empty($tipoInput)) {
            throw new Exception("Por favor, rellena todos los campos obligatorios.");
        }

        // Llamamos a la función de procesamiento
        $datosResguardo = procesarInscripcion($nombreInput, $edadInput, $tipoInput, $talleresInput);
        $exito = true;
        $resultadoLog = "Exitoso";
    }
} catch (Exception $e) {
    $mensajeError = $e->getMessage();
    $resultadoLog = "Fallido";
} finally {
    // Registro interno en el servidor con error_log
    $fechaActual = date('Y-m-d H:i:s');
    error_log("[$fechaActual] Operación de inscripción procesada. Resultado: $resultadoLog");
}
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

        <!-- Formulario HTML -->
        <form action="" method="POST">
            <div>
                <label for="nombre">Nombre completo:</label><br>
                <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>"
                    required>
            </div>

            <div>
                <label for="edad">Edad:</label><br>
                <input type="number" id="edad" name="edad" min="1" value="<?= htmlspecialchars($_POST['edad'] ?? '') ?>"
                    required>
            </div>

            <div>
                <label for="tipo_entrada">Tipo de Entrada:</label><br>
                <?php $tipoSeleccionado = $_POST['tipo_entrada'] ?? ''; ?>
                <select id="tipo_entrada" name="tipo_entrada" required>
                    <option value="">-- Selecciona una opción --</option>
                    <option value="General" <?= $tipoSeleccionado === 'General' ? 'selected' : '' ?>>General (30 €)
                    </option>
                    <option value="VIP" <?= $tipoSeleccionado === 'VIP' ? 'selected' : '' ?>>VIP (60 €)</option>
                    <option value="Infantil" <?= $tipoSeleccionado === 'Infantil' ? 'selected' : '' ?>>Infantil (10 €)
                    </option>
                </select>
            </div>

            <fieldset>
                <legend>Talleres Especiales (Opcional):</legend>
                <?php $talleresPrevios = $_POST['talleres'] ?? []; ?>
                <label>
                    <input type="checkbox" name="talleres[]" value="videojuegos" <?= in_array('videojuegos', $talleresPrevios) ? 'checked' : '' ?>> Torneo de Videojuegos (10 €)
                </label><br>
                <label>
                    <input type="checkbox" name="talleres[]" value="cosplay" <?= in_array('cosplay', $talleresPrevios) ? 'checked' : '' ?>> Taller de Cosplay (15 €)
                </label><br>
                <label>
                    <input type="checkbox" name="talleres[]" value="rol" <?= in_array('rol', $talleresPrevios) ? 'checked' : '' ?>> Zona de Rol en Vivo (5 €)
                </label>
            </fieldset>

            <button type="submit">Procesar Inscripción</button>
        </form>

        <!-- Renderizado de Resultados (El Resguardo) -->
        <?php if ($exito): ?>
            <hr>
            <div>
                <h2>Resguardo de Inscripción</h2>
                <p><strong>Nombre del asistente:</strong> <?= htmlspecialchars($datosResguardo['nombre']) ?></p>
                <p><strong>Tipo de entrada:</strong> <?= $datosResguardo['tipo'] ?> (<?= $datosResguardo['precio_base'] ?>
                    €)</p>

                <h3>Talleres Seleccionados:</h3>
                <?php if (count($datosResguardo['talleres']) > 0): ?>
                    <ul>
                        <?php foreach ($datosResguardo['talleres'] as $t): ?>
                            <li><?= $t['nombre'] ?> - <?= $t['precio'] ?> €</li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>No se ha seleccionado ningún taller.</p>
                <?php endif; ?>

                <p><strong>Precio Total a Pagar:</strong> <?= $datosResguardo['total'] ?> €</p>
            </div>
        <?php endif; ?>

    </div>

</body>

</html>
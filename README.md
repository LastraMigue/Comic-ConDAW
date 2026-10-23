# Comic-Con DAW 2026 - Sistema de Inscripción

Aplicación web backend desarrollada en PHP para la gestión, validación y cálculo dinámico de inscripciones a la convención **Comic-Con DAW 2026**. El sistema integra en un flujo interactivo tanto el formulario de entrada como el procesamiento de negocio, el control de errores mediante excepciones y la generación de un resguardo detallado.

---

## Descripción del Proyecto

El objetivo de la aplicación es proporcionar una plataforma centralizada y segura para que los asistentes puedan registrar su participación en el evento, seleccionando el tipo de pase y talleres opcionales. La aplicación valida las restricciones de edad en tiempo real, calcula el coste total y emite un resguardo desglosado.

---

## Estructura del Proyecto

* **[index.php](file:///c:/xampp/htdocs/Act_servidor/Comic-ConDAW/index.php)**: Script principal que contiene la lógica de configuración, funciones de negocio con tipado estricto, control de excepciones, auditoría en servidor y la plantilla HTML/PHP con sintaxis alternativa.
* **[estilos.css](file:///c:/xampp/htdocs/Act_servidor/Comic-ConDAW/estilos.css)**: Hoja de estilos limpia y ligera para el centrado del formulario, estados de error y el resguardo de la inscripción.
* **[README.md](file:///c:/xampp/htdocs/Act_servidor/Comic-ConDAW/README.md)**: Documentación técnica detallada del proyecto.

---

## Características Técnicas y Cumplimiento de Requisitos

### 1. Configuración y Control Robusto de Errores
* **Modo desarrollo activo**: Se configuran las directivas `ini_set('display_errors', '1')`, `display_startup_errors` y `error_reporting(E_ALL)` para la detección inmediata de advertencias o fallos durante la ejecución.
* **Gestión con Try / Catch / Finally**: Todo el procesamiento del formulario (`POST`) se encapsula en una estructura `try-catch`. Cualquier fallo de validación interrumpe el flujo normal lanzando una `Exception` que es capturada para mostrar un mensaje amigable al usuario.
* **Auditoría en Servidor (`finally`)**: Independientemente de si la operación resulta exitosa o fallida, el bloque `finally` ejecuta `error_log()`, registrando la fecha/hora actual y el resultado (`Exitoso` o `Fallido`) en el registro interno del servidor.

### 2. Saneamiento y Seguridad de Datos
* **Protección contra ataques XSS**: Todos los datos renderizados procedentes del usuario (`$_POST`) pasan por `htmlspecialchars()` al imprimirse en los campos y en el resguardo final.
* **Operador de Fusión de Nulos (`??`)**: Se utiliza para recuperar de forma segura campos opcionales o no inicializados (como el array de talleres `$_POST['talleres'] ?? []`), evitando advertencias de índices indefinidos.
* **Persistencia de Formulario**: Los valores introducidos se mantienen en el formulario si ocurre un error de validación, evitando que el usuario tenga que rellenar todo nuevamente.

### 3. Lógica de Negocio y Funciones Tipadas
* **Tipado estricto**: Uso de `declare(strict_types=1);` al inicio del script para asegurar la integridad de tipos en parámetros y valores de retorno.
* **Función `calcularPrecioBase(string $tipo): int`**:
  * Utiliza una estructura `switch` para asignar los importes base según la entrada:
    * **General**: 30 €
    * **VIP**: 60 €
    * **Infantil**: 10 €
* **Función `procesarInscripcion(string $nombre, int $edad, string $tipoEntrada, array $talleresSeleccionados): array`**:
  * **Validación de edad**: Comprueba que la edad sea un entero estrictamente positivo (`> 0`).
  * **Restricción VIP**: Lanza excepción si un menor de 18 años intenta adquirir un pase VIP (*"La entrada VIP está restringida a mayores de edad."*).
  * **Restricción Infantil**: Lanza excepción si un asistente de 18 años o más intenta adquirir una entrada Infantil (*"La entrada Infantil está restringida a menores de edad."*).
  * **Cálculo de talleres con bucle `foreach`**: Recorre los talleres seleccionados acumulando su coste individual:
    * Torneo de Videojuegos: 10 €
    * Taller de Cosplay: 15 €
    * Zona de Rol en Vivo: 5 €
  * Retorna un array asociativo estructurado con los datos listos para el resguardo.

### 4. Interfaz y Renderizado del Resguardo
* **Formulario HTML**:
  * Envío por método `POST` al mismo script.
  * Selector desplegable para el tipo de pase.
  * Grupo de casillas de verificación (`checkboxes`) para talleres independientes.
* **Sintaxis de Plantilla Alternativa**:
  * Empleo de estructuras `<?php if (...): ?> ... <?php endif; ?>` y `<?php foreach (...): ?> ... <?php endforeach; ?>` para separar claramente la lógica de control del marcado HTML.
  * Si la inscripción es válida, se genera un resguardo con el nombre del asistente, entrada con precio base, desglose de talleres seleccionados y el precio total final.

---

## Despliegue y Ejecución

1. Clonar o ubicar la carpeta del proyecto en el directorio raíz del servidor web local:
   ```text
   C:\xampp\htdocs\Act_servidor\Comic-ConDAW\
   ```
2. Iniciar el servicio de **Apache** desde el panel de control de XAMPP.
3. Abrir un navegador web y acceder a la URL:
   ```text
   http://localhost/Act_servidor/Comic-ConDAW/index.php
   ```

---

## Casos de Prueba Verificados

| Caso de Prueba | Datos de Entrada | Resultado Esperado |
| :--- | :--- | :--- |
| **Inscripción General estándar** | Edad: 25, Entrada: General, Taller: Videojuegos | Exitoso. Base: 30 €, Taller: 10 €, Total: 40 € |
| **Intento de pase VIP por menor** | Edad: 16, Entrada: VIP | Error: *"La entrada VIP está restringida a mayores de edad."* |
| **Intento de pase Infantil por adulto** | Edad: 22, Entrada: Infantil | Error: *"La entrada Infantil está restringida a menores de edad."* |
| **Edad no válida** | Edad: 0 o negativa | Error: *"La edad debe ser un número entero positivo."* |
| **Sin selección de talleres** | Edad: 30, Entrada: General, Sin talleres | Exitoso. Base: 30 €, Total: 30 € (sin warnings PHP) |
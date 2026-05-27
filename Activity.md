¡Entendido! Vamos a transformar y adaptar el prompt refactorizado para que se centre en la entidad **Activity (Actividad)**.

En este contexto, la lógica de **Publicación Programada (Scheduled Publishing)** encaja a la perfección: una actividad puede registrarse con antelación en estado `UPCOMING` (Próxima / Programada) y el sistema la pasará automáticamente a `ACTIVE` (Activa / En curso) o `PUBLISHED` cuando llegue su fecha y hora de inicio (`started_at`).

Aquí tienes el prompt adaptado profesionalmente, manteniendo la misma rigurosidad técnica, consistencia visual y estructura de automatización que requieres para tu proyecto Laravel 11/12+ con Livewire Volt.

---

# Prompt Adaptado: Refactorización Técnica de la Entidad "Activity"

## Objetivo

Refactorizar/Implementar el módulo de **Activities** en este proyecto Laravel 11/12+ para incorporar la funcionalidad de **Publicación/Activación Programada** basada en el estado `ActivityStatus::UPCOMING`. Cuando llegue la fecha y hora planificadas, el sistema debe cambiar automáticamente el estado de la actividad de forma nativa e invisible para el usuario administrador.

---

## Contexto Técnico Específico

* **Entidad:** `Activity`
* **Campos Clave:** `id`, `title`, `description`, `image`, `status` (Enum), `started_at` (datetime, nullable), `created_at`, `updated_at`.
* **Estado Crítico:** `ActivityStatus::UPCOMING`.
* **Lógica de Fecha:** Cuando el estado es `UPCOMING`, el formulario de creación/edición debe habilitar y requerir obligatoriamente el campo `started_at`. Este campo determinará el momento exacto en que la actividad pasará a estar visible o activa en el portal público. Si el estado es `ACTIVE`, se asume inicio inmediato (o `started_at <= now()`).
* **Tecnologías:** Livewire Volt (Class-based API), Tailwind CSS, Blade Components, Laravel Scheduler, Pest (Testing).
* **Idiomas:** Todo el código, nombres técnicos, rutas, bases de datos y variables en **inglés**. Explicaciones y documentación en **español**.

---

## Requerimientos de Backend & Automatización

### 1. Base de Datos y Modelos

* Crear/Asegurar la migración de `activities` con la columna `started_at` (datetime, nullable) y `status` (string/enum mapeado a un Enum de PHP `ActivityStatus`).
* Crear el Enum `ActivityStatus` con los estados: `DRAFT`, `UPCOMING`, `ACTIVE`, y `FINISHED` (o equivalentes según el negocio).
* Crear Factory y Seeder para poblar datos de prueba en diferentes estados.

### 2. Tarea Programada (Scheduled Task)

* Crear un comando de Artisan o Job llamado `PublishUpcomingActivities` (o `ActivateScheduledActivities`).
* Configurar el Scheduler en `routes/console.php` para que se ejecute **cada minuto** (`everyMinute()`).
* **Lógica de la tarea:** Buscar registros de `Activity` donde `status === ActivityStatus::UPCOMING` y `started_at <= now()`, y actualizar automáticamente su estado a `ActivityStatus::ACTIVE`.

---

## Frontend (Livewire Volt - Class-based API)

### 1. Formulario Dinámico y Reactivo (Create/Edit Modal o Vista)

* Implementar lógica reactiva nativa de Volt: al seleccionar `ActivityStatus::UPCOMING` en el desplegable (`<x-select>`), debe aparecer mediante una transición fluida de Tailwind (`transition`, `duration`) el input de fecha y hora para `started_at`.
* **Validación en tiempo real:** Si el estado es `UPCOMING`, `started_at` es requerido (`required`) y debe ser una fecha futura (`after:now`).

### 2. UI/UX & Consistencia con el Starter Kit

* Integrar el módulo dentro del `AppLayout` existente, respetando escrupulosamente los márgenes, paddings internos y anchos máximos (`max-w-7xl`).
* Soportar nativamente **Light y Dark mode**.
* **ActivityIndex (Listado):** * Diseñar una tabla estilizada (bordes redondeados, sombras sutiles, hover states).
* Añadir badges visuales de Tailwind diferenciados por color para los estados (ej: Amarillo/Azul para `UPCOMING`, Verde para `ACTIVE`).
* Implementar un *Empty State* elegante con un icono y CTA si no hay registros.


* **Acciones y Feedback:** Modales de confirmación estilizados con Blade (no usar `confirm()` nativo de JS) para la eliminación. Feedback visual inmediato mediante *Flash Messages*.

---

## Testing con Pest

Crear un set de pruebas automatizadas que garantice:

1. Que el componente Livewire Index y Form se carguen correctamente.
2. Que las reglas de validación condicional funcionen (fallar si es `UPCOMING` sin fecha o con fecha pasada).
3. Que una actividad en estado `UPCOMING` no se muestre en los listados públicos generales hasta que cambie su estado.
4. Que el comando de consola de Artisan (`activity:publish` o similar) efectivamente procese y cambie el estado de `UPCOMING` a `ACTIVE` cuando la hora de `started_at` sea igual o menor a `now()`.

---

## Restricciones y Entrega

* Alineación estricta con las convenciones del proyecto, `AGENTS.md` y Laravel Pint.
* **Formato de Entrega:** Al finalizar, se debe presentar un resumen corto con:
* Archivos creados/modificados.
* Comandos ejecutados (migraciones, etc.).
* Resultado simulado/esperado de los tests ejecutados.
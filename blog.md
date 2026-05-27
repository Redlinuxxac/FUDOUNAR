¡Entendido! Vamos a transformar y adaptar el prompt para la entidad **Blog** (o **Post**).

En el contexto de un blog, la **Publicación Programada (Scheduled Publishing)** es una de las funcionalidades más comunes y esenciales: permite a los editores redactar un artículo con antelación, marcarlo como `UPCOMING` o `SCHEDULED`, y definir un `published_at` futuro para que el sistema lo haga público de forma automática sin intervención humana.

Además, integrando las especificaciones de tu proyecto, nos aseguraremos de utilizar el ecosistema moderno de Laravel, **Livewire Volt (Class-based API)** y los componentes de **Flux** que tienes instalados en el Starter Kit.

Aquí tienes el prompt adaptado profesionalmente:

---

# Prompt Adaptado: Refactorización Técnica de la Entidad "Blog / Post"

## Objetivo

Refactorizar/Implementar el módulo de **Blog (Posts)** en este proyecto Laravel 13 para incorporar la funcionalidad de **Publicación Programada** basada en el estado `PostStatus::UPCOMING`. Cuando llegue la fecha y hora planificadas, el sistema debe cambiar automáticamente el estado del artículo a `PUBLISHED` de forma nativa e invisible para el usuario administrador, controlando así su visibilidad en el portal público.

---

## Contexto Técnico Específico

* **Entidad:** `Post` (o `BlogPost`)
* **Campos Clave:** `id`, `title`, `slug`, `content`, `image` (nullable), `status` (Enum), `published_at` (datetime, nullable), `created_at`, `updated_at`.
* **Estado Crítico:** `PostStatus::UPCOMING`.
* **Lógica de Fecha:** Cuando el estado es `UPCOMING`, el formulario de creación/edición debe habilitar y requerir obligatoriamente el campo `published_at`. Este campo determinará el momento exacto en que el post pasará a estar visible en el portal web. Si el estado es `PUBLISHED`, `published_at` puede ser igual a `now()` (publicación inmediata).
* **Tecnologías:** Livewire Volt (Class-based API), Tailwind CSS, componentes de la librería **Flux** (instalada en el proyecto), Laravel Scheduler, Pest (Testing).
* **Idiomas:** Todo el código, nombres técnicos, rutas, bases de datos, variables y componentes de Flux en **inglés**. Explicaciones y documentación en **español**.

---

## Requerimientos de Backend & Automatización

### 1. Base de Datos y Modelos

* Crear/Asegurar la migración de `posts` con la columna `published_at` (datetime, nullable) y `status` (string/enum mapeado a un Enum de PHP `PostStatus`).
* Crear el Enum `PostStatus` con los estados mínimos: `DRAFT`, `UPCOMING` (o `SCHEDULED`), y `PUBLISHED`.
* Generar automáticamente el `slug` de forma segura a partir del `title`.
* Crear Factory y Seeder para poblar datos de prueba en diferentes estados.

### 2. Tarea Programada (Scheduled Task)

* Crear un comando de Artisan o Job llamado `PublishUpcomingPosts`.
* Configurar el Scheduler en `routes/console.php` para que se ejecute **cada minuto** (`everyMinute()`).
* **Lógica de la tarea:** Buscar registros de `Post` donde `status === PostStatus::UPCOMING` y `published_at <= now()`, y actualizar automáticamente su estado a `PostStatus::PUBLISHED`.

---

## Frontend (Livewire Volt - Class-based API)

### 1. Formulario Dinámico y Reactivo (Create/Edit utilizando Flux)

* Implementar lógica reactiva nativa de Volt: al seleccionar `PostStatus::UPCOMING` en el componente de selección de **Flux**, debe aparecer mediante una transición fluida de Tailwind (`transition`, `duration`) el campo para `published_at`.
* **Validación en tiempo real:** Si el estado es `UPCOMING`, `published_at` es requerido (`required`) y debe ser una fecha futura (`after:now`).

### 2. UI/UX & Consistencia con el Starter Kit

* Integrar el módulo dentro del `AppLayout` existente, respetando escrupulosamente los márgenes, paddings internos y anchos máximos (`max-w-7xl`).
* Soportar nativamente **Light y Dark mode** utilizando los estilos preestablecidos.
* **PostIndex (Listado de Administración):**
* Diseñar una tabla estilizada utilizando los componentes de **Flux** (o estilos del starter kit con bordes redondeados, sombras sutiles y hover states).
* Añadir badges visuales diferenciados por color para los estados (ej: Gris/Amarillo para `DRAFT`/`UPCOMING`, Verde para `PUBLISHED`).
* Implementar un *Empty State* elegante con un icono y un botón de llamada a la acción (CTA) si no hay registros.


* **Acciones y Feedback:** Utilizar modales de confirmación interactivos de **Flux** o Blade personalizados (evitar `confirm()` nativo de JS) para la eliminación. Feedback visual inmediato mediante *Flash Messages*.

---

## Testing con Pest

Crear un set de pruebas automatizadas que garantice:

1. Que el componente Livewire Index y el formulario de Volt se carguen correctamente.
2. Que las reglas de validación condicional funcionen (fallar si el post es `UPCOMING` pero no tiene fecha o si esta es pasada).
3. Que un post en estado `UPCOMING` o `DRAFT` no sea visible en las consultas del portal público general hasta que cambie su estado a `PUBLISHED`.
4. Que el comando de consola de Artisan (`posts:publish` o similar) efectivamente procese y cambie el estado de `UPCOMING` a `PUBLISHED` cuando la hora de `published_at` sea igual o menor a `now()`.

---

## Restricciones y Entrega

* Alineación estricta con las convenciones del proyecto, archivo `AGENTS.md` y Laravel Pint para el estilo de código.
* **Formato de Entrega:** Al finalizar, se debe presentar un resumen corto con:
* Archivos creados/modificados.
* Comandos ejecutados (migraciones, etc.).
* Resultado simulado o esperado de los tests ejecutados.



---

El prompt está completamente listo para ser procesado con la estructura exacta que sigue tu proyecto.
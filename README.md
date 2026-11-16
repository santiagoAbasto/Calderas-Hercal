<div align="center">
<img src="docs/readme/banner.svg" alt="Calderas Hercal — Catálogo industrial, panel CMS y bandeja de presupuestos" width="100%">
<br/>

**Sitio corporativo industrial con panel CMS y bandeja unificada de presupuestos.**

<img alt="Demo funcional" src="https://img.shields.io/badge/estado-demo_funcional-d50101?style=for-the-badge&labelColor=202020">
<img alt="Laravel 12" src="https://img.shields.io/badge/Laravel-12-ff2d20?style=flat-square&logo=laravel&logoColor=white">
<img alt="PHP 8.3+" src="https://img.shields.io/badge/PHP-8.3%2B-777bb4?style=flat-square&logo=php&logoColor=white">
<img alt="React 18" src="https://img.shields.io/badge/React-18-149eca?style=flat-square&logo=react&logoColor=white">
<img alt="Inertia.js 2" src="https://img.shields.io/badge/Inertia.js-2-9553e9?style=flat-square">
<img alt="Tailwind CSS 4" src="https://img.shields.io/badge/Tailwind_CSS-4-06b6d4?style=flat-square&logo=tailwindcss&logoColor=white">
<img alt="Vite 7" src="https://img.shields.io/badge/Vite-7-646cff?style=flat-square&logo=vite&logoColor=white">
</div>

<img src="docs/readme/divider.svg" alt="" width="100%">

## <img src="docs/readme/ic-indice.svg" width="24" height="24" alt=""> Contenido

[El problema](#-el-problema) · [La solución](#-la-solución) · [Funcionalidades](#-funcionalidades) · [Arquitectura](#-arquitectura) · [Flujos de trabajo](#-flujos-de-trabajo) · [Modelo de datos](#-modelo-de-datos) · [Criterios y decisiones](#-criterios-y-decisiones) · [Stack](#-stack) · [Estructura](#-estructura-del-repo) · [Puesta en marcha](#-puesta-en-marcha) · [Pruebas y calidad](#-pruebas-y-calidad) · [Despliegue](#-despliegue) · [Estado y pendientes](#-estado-y-pendientes) · [Licencia y autoría](#-licencia-y-autoría)

<img src="docs/readme/divider.svg" alt="" width="100%">

## <img src="docs/readme/ic-problema.svg" width="24" height="24" alt=""> El problema

Una empresa industrial que vende calderas y equipos necesitaba dos cosas que no convivían bien: un sitio que mostrara servicios, equipos y trabajos realizados a potenciales clientes, y una forma de recibir consultas comerciales sin depender de plantillas o de intervenir la base de datos para cada cambio de contenido.

Sin una herramienta propia, cada actualización de un servicio, una ficha de equipo o una foto de obra dependía de quien pudiera tocar el código, y las solicitudes de contacto y presupuesto llegaban dispersas.

## <img src="docs/readme/ic-solucion.svg" width="24" height="24" alt=""> La solución

**Calderas Hercal** es una aplicación Laravel con dos caras:

- **Sitio público** para visitantes y clientes potenciales: inicio, empresa, servicios, equipos, trabajos realizados, clientes, contacto y solicitud de presupuesto. Se renderiza en servidor con Blade.
- **Panel administrativo** para el personal autorizado: edición de páginas por secciones, catálogos, proyectos, multimedia, SEO y la bandeja de solicitudes. Se construye con React e Inertia.js.

El acceso al panel exige sesión autenticada y el permiso explícito `can_access_admin`. El personal edita contenido; el diseño de las páginas no cambia.

<img src="docs/readme/divider.svg" alt="" width="100%">

## <img src="docs/readme/ic-funcionalidades.svg" width="24" height="24" alt=""> Funcionalidades

**Sitio público**
- Inicio, empresa, servicios, equipos, trabajos realizados, clientes y búsqueda de servicios, equipos y proyectos.
- Detalle de servicios y equipos por slug, con navegación por categorías.
- Formularios de contacto y de presupuesto con selección contextual del servicio o equipo consultado.
- Enlace directo a WhatsApp configurable desde el panel.

**Panel administrativo** (rol: personal autorizado)
- CMS de páginas, secciones y recursos multimedia: imágenes, videos locales y videos de YouTube, con texto alternativo, orden y activación independiente.
- Catálogos de servicios, categorías de equipos, equipos y proyectos, con orden, estado activo y selección de contenido destacado en inicio.
- Bandeja unificada de contactos y presupuestos: búsqueda, filtros, estado leído/no leído y enlace de respuesta por correo.
- Metadatos SEO por página: título, descripción, palabras clave, URL canónica, directivas para robots e imagen Open Graph.
- Usuarios, avatar, perfil y cambio de contraseña.

<img src="docs/readme/divider.svg" alt="" width="100%">

## <img src="docs/readme/ic-arquitectura.svg" width="24" height="24" alt=""> Arquitectura

Monolito Laravel que comparte modelos, reglas, almacenamiento y autenticación entre dos formas de presentación: Blade para la web pública y Inertia para el panel. No existe una API REST pública ni versionada; algunas rutas internas del CMS responden JSON.

<img src="docs/readme/flujo.svg" alt="Cómo viaja una petición: visitante y personal entran por el sitio y el panel, Laravel valida y aplica reglas, Eloquent accede a la base y al storage de media" width="100%">

```mermaid
flowchart LR
    V["Visitante"] --> B["Web pública (Blade)"]
    U["Personal autorizado"] --> P["Panel (React + Inertia)"]
    B --> C["Controladores Web"]
    P --> A["Controladores Admin"]
    C --> M["Middleware (formularios, cabeceras, acceso)"]
    A --> M
    M --> S["Support (CmsSecurity, orden, SEO)"]
    S --> E["Eloquent ORM"]
    E --> D[("Base de datos")]
    E --> F["Storage de media (public)"]
```

<img src="docs/readme/divider.svg" alt="" width="100%">

## <img src="docs/readme/ic-flujos.svg" width="24" height="24" alt=""> Flujos de trabajo

**Solicitud de presupuesto desde el sitio público**

```mermaid
sequenceDiagram
    actor Visitante
    participant Web as Sitio público
    participant Laravel as Laravel (validación)
    participant DB as Base de datos
    participant Panel as Bandeja del panel
    Visitante->>Web: Completa el formulario de presupuesto
    Web->>Laravel: POST /presupuesto
    Laravel->>Laravel: Honeypot, límites por ruta y por correo/IP
    Laravel->>DB: Guarda la solicitud (IP como huella HMAC)
    DB-->>Panel: Aparece como no leída
    Panel->>DB: Marca como leída y responde por correo
```

**Edición de contenido en el CMS**

```mermaid
sequenceDiagram
    actor Personal
    participant Panel as Panel (React)
    participant Laravel as Controlador CMS
    participant Sec as CmsSecurity
    participant DB as Base de datos
    Personal->>Panel: Edita una sección o una media
    Panel->>Laravel: Envía el formulario
    Laravel->>Sec: Sanitiza texto, HTML permitido y URLs
    Sec-->>Laravel: Contenido limpio
    Laravel->>DB: Guarda página, sección o media
    DB-->>Panel: Contenido visible en el sitio público
```

<img src="docs/readme/divider.svg" alt="" width="100%">

## <img src="docs/readme/ic-datos.svg" width="24" height="24" alt=""> Modelo de datos

Persistencia con Eloquent. Las migraciones definen claves foráneas, borrado en cascada donde corresponde, slugs y correos únicos, e índices para orden, relaciones y estado de lectura.

```mermaid
erDiagram
    PAGE ||--o{ PAGE_SECTION : contiene
    PAGE_SECTION ||--o{ PAGE_SECTION_MEDIA : incluye
    EQUIPMENT_CATEGORY ||--o{ EQUIPMENT : agrupa
    EQUIPMENT ||--o{ EQUIPMENT_IMAGE : muestra
    PROJECT ||--o{ PROJECT_IMAGE : galeria
    SERVICE {
        bigint id PK
        string slug
    }
    CONTACT {
        bigint id PK
    }
    QUOTE {
        bigint id PK
    }
    FORM_SUBMISSION {
        bigint id PK
    }
    USER {
        bigint id PK
        boolean can_access_admin
    }
    SETTING {
        string key
        text value
    }
```

Las solicitudes de contacto y presupuesto también quedan en `form_submissions`, la tabla que alimenta la bandeja unificada. Las categorías de equipos conservan su portada con la bandera `is_catalog_root`, y las operaciones que la sincronizan usan transacciones.

<img src="docs/readme/divider.svg" alt="" width="100%">

## <img src="docs/readme/ic-criterios.svg" width="24" height="24" alt=""> Criterios y decisiones

Reglas de negocio e invariantes que no se leen a simple vista en el código:

- **Una sola portada por categoría.** `is_catalog_root` separa la representación de una categoría de sus productos, para no duplicarla en la navegación pública.
- **Sin HTML arbitrario en el CMS.** El texto enriquecido pasa por una lista de etiquetas permitidas; se filtran atributos de eventos, estilos y esquemas de URL no admitidos.
- **SVG no admitido como media de página.** Las cargas validan tipo MIME, extensión y tamaño.
- **IP nunca en texto plano.** Las solicitudes guardan una huella HMAC.
- **El usuario principal no se puede quitar.** La administración de usuarios impide retirar su acceso o eliminar la propia cuenta desde la pantalla de gestión.
- **Acceso por permiso, no por rol.** La autorización del panel se resuelve con `can_access_admin`; existe una lista de correos de compatibilidad configurada por entorno.

| Decisión | Por qué |
| --- | --- |
| Monolito Laravel con Blade y React/Inertia | Una sola base de código y un solo backend; la web pública necesita SEO por render en servidor y el panel necesita interacción sin una API independiente. |
| CMS modelado en entidades relacionadas | Páginas, secciones y media se ordenan y activan por separado en vez de guardarse como un bloque opaco. |
| Sanitización centralizada en `CmsSecurity` | Un único punto para texto, HTML, URLs, correos, teléfonos y huellas de IP. |
| Honeypot y doble límite de frecuencia | Reducen spam en formularios públicos sin pedir captcha al visitante. |

<img src="docs/readme/divider.svg" alt="" width="100%">

## <img src="docs/readme/ic-stack.svg" width="24" height="24" alt=""> Stack

| Capa | Tecnología | Versión |
| --- | --- | --- |
| Lenguaje | PHP | ^8.3 |
| Backend | Laravel framework | 12.x (lock 12.69.2) |
| Integración panel | inertiajs/inertia-laravel | 2.0.22 |
| Autenticación | Laravel Breeze | según `composer.json` |
| Frontend | React | 18.3.1 |
| Frontend | @inertiajs/react | 2.3.18 |
| Estilos | Tailwind CSS | 4.2.1 |
| Build | Vite | 7.3.6 |
| Pruebas | PHPUnit | 11 |
| Base de datos | SQLite por defecto (MySQL, MariaDB, PostgreSQL y SQL Server soportados por configuración) | — |

<img src="docs/readme/divider.svg" alt="" width="100%">

## <img src="docs/readme/ic-estructura.svg" width="24" height="24" alt=""> Estructura del repo

```text
app/Http/Controllers/Web/     sitio público (inicio, servicios, equipos, presupuesto)
app/Http/Controllers/Admin/   CMS, catálogos, usuarios y solicitudes
app/Http/Middleware/          acceso administrativo, formularios y cabeceras
app/Support/                  orden, seguridad del CMS y resolución SEO
database/migrations/          esquema relacional
database/seeders/             contenido inicial de las páginas
resources/views/              Blade público y correos
resources/js/                 panel React e Inertia
routes/web.php                rutas públicas y administrativas
tests/                        pruebas de funcionalidad y unitarias
```

<img src="docs/readme/divider.svg" alt="" width="100%">

## <img src="docs/readme/ic-arranque.svg" width="24" height="24" alt=""> Puesta en marcha

Requisitos: PHP 8.3+, Composer, Node.js y npm.

```bash
composer install
npm ci
cp .env.example .env
php artisan key:generate
touch database/database.sqlite   # configuración SQLite incluida
php artisan migrate --seed
php artisan storage:link
composer run dev                 # servidor, cola, logs y Vite en paralelo
```

Alternativa: `php artisan serve` y `npm run dev` por separado.

Variables de entorno: revisar `.env.example` (base de datos, correo, `APP_URL`, y las que configuran el acceso administrativo). Los valores reales van en `.env`, que no se versiona.

<img src="docs/readme/divider.svg" alt="" width="100%">

## <img src="docs/readme/ic-pruebas.svg" width="24" height="24" alt=""> Pruebas y calidad

Pruebas en `tests/Feature` y `tests/Unit`: autenticación, perfil, autorización del panel, protección de formularios, cabeceras HTTP, rechazo de SVG, visibilidad del CMS, reglas del catálogo y selección contextual en presupuestos.

```bash
composer test
npm run build
```

La cifra registrada en la versión anterior de este README fue de 41 pruebas y 130 aserciones aprobadas, sobre SQLite en memoria; no se volvió a ejecutar para este cambio. No hay linters frontend, análisis estático, E2E ni pruebas de carga configurados.

<img src="docs/readme/divider.svg" alt="" width="100%">

## <img src="docs/readme/ic-despliegue.svg" width="24" height="24" alt=""> Despliegue

No documentado en el repositorio. No hay Dockerfile, Docker Compose, configuración de servidor web ni pipeline CI/CD versionados. El proveedor, entorno y procedimiento de producción no se pueden verificar desde el código.

<img src="docs/readme/divider.svg" alt="" width="100%">

## <img src="docs/readme/ic-estado.svg" width="24" height="24" alt=""> Estado y pendientes

- **Estado:** demo funcional. El código incluye sitio público, panel, persistencia, migraciones, seeders y pruebas automatizadas.
- **Último commit:** 2026-06-06, `VERSION 1.0.1`, en el historial local.
- **Producción:** no verificado.
- **Pendientes técnicos:** definir despliegue y procedimiento operativo; añadir análisis estático o pruebas E2E si el cliente las requiere.

<img src="docs/readme/divider.svg" alt="" width="100%">

## <img src="docs/readme/ic-licencia.svg" width="24" height="24" alt=""> Licencia y autoría

Desarrollado por el **Ing. Santiago Abasto**.


Repositorio sin archivo de licencia. El código y el contenido son propiedad del cliente, Calderas Hercal, salvo que se acuerde otra cosa.

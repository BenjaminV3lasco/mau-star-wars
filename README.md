# Sistema de Gestión de Personajes Star Wars

Aplicación para crear, consultar, editar, eliminar e importar personajes de Star Wars desde [SWAPI](https://swapi.py4e.com/documentation). Laravel resuelve rutas, validaciones, persistencia en MySQL y la consulta a SWAPI; Vue 3 dibuja las pantallas a través de Inertia.

## Información del Candidato

- **Nombre:** Benjamín Velasco
- **Email:** velascobenjamin14@gmail.com
- **Teléfono:** +54 2657-610234
- **GitHub:** [BenjaminV3lasco](https://github.com/BenjaminV3lasco)

## Requisitos del Sistema

- PHP **8.3 o superior** (lo exige Laravel 13) con las extensiones `pdo_mysql`, `curl` y `openssl` (y `pdo_sqlite` para correr las pruebas).
- Composer 2
- Node.js **22.12 o superior** y npm (lo pide Vite 8)
- MySQL 8 o superior

## Instalación

1. Clonar el repositorio

   ```bash
   git clone https://github.com/BenjaminV3lasco/mau-star-wars.git
   cd mau-star-wars
   ```

2. Instalar dependencias

   ```bash
   composer install
   npm install
   ```

3. Configurar la base de datos

   Crear una base vacía en MySQL:

   ```sql
   CREATE DATABASE mau_star_wars CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

   Copiar el archivo de entorno (en PowerShell: `Copy-Item .env.example .env`), completar `DB_USERNAME` y `DB_PASSWORD` con las credenciales de MySQL y generar la clave:

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. Ejecutar migraciones (con `--seed` se cargan 12 personajes de ejemplo; es opcional)

   ```bash
   php artisan migrate --seed
   ```

5. Compilar assets (necesita internet: descarga las fuentes desde Bunny Fonts)

   ```bash
   npm run build
   ```

6. Iniciar servidor

   ```bash
   php artisan serve
   ```

7. Acceder a <http://localhost:8000/personajes>

Para correr las pruebas: `php artisan test`. Usan SQLite en memoria, así que no tocan la base MySQL.

## Funcionalidades Implementadas

**Obligatorias**

- CRUD completo de personajes
- Integración con SWAPI (importación por ID, con control de duplicados y errores)
- Validaciones en backend y frontend
- Paginación de 10 personajes por página
- Componentes Vue reutilizables
- Diseño responsive (probado desde 320 px)

**Bonus**

- Service Layer: `SwapiService` concentra la consulta y la traducción de SWAPI
- Pruebas unitarias y de feature: 65 casos (conversión de medidas de SWAPI, CRUD, validaciones, importación, filtros y página de error)
- Seeder con datos de prueba
- Búsqueda en tiempo real por nombre
- Filtro por género
- Ordenamiento por número de ficha, nombre, altura, masa o género
- Animaciones con Tailwind (respetan `prefers-reduced-motion`)
- Modo claro/oscuro, guardado en el navegador
- Vista de detalle de cada personaje
- Página de error 404 ambientada en lugar de la de Laravel
- Interfaz ambientada en Star Wars: estrellas que titilan, botones y campos con brillo de sable de luz, color por origen (celeste SWAPI, dorado manual), modal de importación holográfico y modo claro con el ocaso de los dos soles de Tatooine
- Efectos de sonido sintetizados con Web Audio API (sin archivos ni librerías), con botón para silenciarlos
- Cambio de tema con transición circular (View Transitions API)

## Tecnologías

- Laravel 13
- Vue.js 3.5 (Composition API con `<script setup>`)
- Inertia.js 3
- Tailwind CSS 4
- Vite 8
- MySQL 8

## Estructura del frontend

Los componentes siguen Atomic Design:

```
resources/js/
├── Components/
│   ├── Atoms/       Boton, Entrada, Selector, Aviso, InsigniaOrigen, EsquinasHud…
│   ├── Molecules/   CampoFormulario, ModalDialogo, Paginacion
│   ├── Organisms/   PersonajeForm, PersonajeCard, FiltrosPersonajes, modales, cabecera
│   └── Templates/   AppLayout, MarcoPagina, PersonajeFormLayout
├── Pages/           Las páginas que renderiza Inertia (incluida Error.vue)
├── composables/     useFiltrosPersonajes (búsqueda, filtros y orden) y useSonido (efectos de sonido)
└── utils/           Géneros y helpers compartidos
```

## Notas

- **Mapeo de SWAPI:** los géneros distintos de `male` y `female` (`hermaphrodite`, `none`, `n/a`) se guardan como `n/a`. Los valores `unknown` quedan vacíos. Si una medida supera los límites del formulario (por ejemplo, Jabba pesa 1.358 kg), se guarda vacía para no inventar un dato. `planeta_origen` queda vacío porque SWAPI entrega una URL en `homeworld` y la consigna no pide una segunda consulta.
- **`SQLSTATE[HY000] [2002]` al abrir la app:** MySQL no está corriendo. Hay que iniciar el servicio (por ejemplo, `net start MySQL84` en Windows) y recargar. Laravel guarda las sesiones en la base, por eso falla desde la primera página.
- **`cURL error 60` al importar:** PHP no encuentra los certificados raíz. Hay que configurar `curl.cainfo` y `openssl.cafile` en el `php.ini` (lo muestra `php --ini`) apuntando al [bundle oficial de curl](https://curl.se/ca/cacert.pem) y reiniciar el servidor. La verificación HTTPS tiene que seguir habilitada.

## Decisiones técnicas

### 1. Organización del código backend

`PersonajeController` coordina las acciones y se mantiene corto. `GuardarPersonajeRequest` e `ImportarPersonajeRequest` validan la entrada y `SwapiService` concentra la llamada HTTP, la validación de la respuesta y la traducción de campos. Elegí un Service y no un Repository porque Eloquent ya cubre el acceso a datos; la parte con lógica propia es SWAPI, y aislarla me permite probarla con `Http::fake()` sin depender de la red.

### 2. Componentes Vue

Organicé los componentes con Atomic Design. Los átomos (`Boton`, `Entrada`, `Selector`, `InsigniaOrigen`) no saben nada de personajes y se usan en todas las pantallas. `CampoFormulario` arma etiqueta, control, ayuda y error, y lo usan tanto el formulario como el modal de importación. `ModalDialogo` resuelve apertura, cierre, foco y botones, así que los modales de importar y eliminar solo definen su contenido. `PersonajeForm` sirve para crear y editar. La lógica de búsqueda, filtros y orden vive en el composable `useFiltrosPersonajes`, separada de la vista.

### 3. Manejo de errores de SWAPI

El ID se valida antes de consultar (entero entre 1 y 83, que no esté importado). La petición tiene 5 segundos para conectar y 15 para responder. Un timeout, un 404, cualquier otro estado HTTP o una respuesta con datos incompletos muestran un mensaje claro en el modal y no guardan nada. El índice único de `swapi_id` evita duplicados incluso si llegan dos importaciones a la vez. En el frontend también contemplé la sesión vencida (419) y la falta de conexión con el servidor.

### 4. Validaciones

Las reglas de negocio están en los Form Requests, porque el backend tiene que protegerse de cualquier petición, venga o no de Vue. En el frontend uso validación HTML (`required`, `minlength`, `min`, `max`) para avisar rápido, y muestro los errores del backend con `useForm()`. La base de datos refuerza la unicidad de `swapi_id`. `origen_swapi` y `swapi_id` solo se asignan en la importación, nunca desde datos que manda el navegador.

### 5. Mayor dificultad

La primera consulta real a SWAPI desde PHP falló con `cURL error 60`: mi instalación de PHP (Herd Lite en Windows) no tenía configurado un bundle de certificados. Descargué el bundle oficial de curl, verifiqué su checksum SHA-256, lo configuré en el `php.ini` y después de reiniciar SWAPI respondió bien.

También me encontré con que las pruebas pasaban en Windows pero fallaban en Linux: Inertia 3 busca las páginas en `resources/js/pages` (minúscula) y mi carpeta es `Pages`. Windows no distingue mayúsculas, así que el error no aparecía en mi máquina. Lo resolví declarando la ruta en `config/inertia.php` y verifiqué la instalación desde un clon limpio en Linux con MySQL.

### 6. Uso de Inteligencia Artificial

Sí. Usé Codex para organizar la consigna, preparar el entorno, implementar el CRUD y la importación, escribir pruebas y proponer el diseño. Después usé Claude para revisar la entrega contra la consigna, reorganizar el frontend con Atomic Design, migrar los estilos a Tailwind, sumar la ambientación (luces, sonidos y modo claro), documentar el código, ordenar el README y verificar una instalación limpia con PHP 8.3 y MySQL. Validé el código con las pruebas automatizadas, con el build de Vite y recorriendo cada flujo en el navegador contra MySQL y SWAPI reales. Revisé cada archivo para poder explicarlo y modificarlo.

### 7. Mejoras futuras

- Traer el planeta de origen consultando `homeworld` (con caché, para no repetir consultas).
- Un comando Artisan para importar varios personajes a la vez.
- Pruebas de interfaz con Vitest y Playwright.
- Paginación numerada y Docker Compose para levantar el proyecto con un solo comando.

### 8. Autoevaluación

8/10. Lo mejor es el manejo de errores de la importación y que todo lo obligatorio está cubierto por pruebas.

### 9. Tiempo invertido

Aproximadamente 8 horas.

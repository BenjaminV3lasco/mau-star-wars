# Sistema de Gestión de Personajes Star Wars

Aplicación local para crear, consultar, editar, eliminar e importar personajes de Star Wars. Laravel gestiona rutas, validaciones, MySQL y la consulta a SWAPI; Vue 3 muestra las pantallas mediante Inertia.

## Información del candidato

- **Nombre:** Benjamín Velasco
- **Email:** pendiente de completar por el candidato antes de entregar.
- **Teléfono:** +54 2657-610234
- **GitHub:** [BenjaminV3lasco](https://github.com/BenjaminV3lasco)
- **Repositorio público:** [github.com/BenjaminV3lasco/mau-star-wars](https://github.com/BenjaminV3lasco/mau-star-wars).

## Requisitos del sistema

- PHP **8.3 o superior** con `pdo_mysql`, `curl` y `openssl`. Para ejecutar las pruebas, también `pdo_sqlite`.
- Composer 2.
- Node.js **22.12 o superior** y npm. Vite 8 requiere una versión reciente de Node; el proyecto se verificó con Node 22.13 y npm 11.
- MySQL 8 o superior.

Entorno utilizado: PHP 8.4.0, Composer 2.8.12, Laravel Framework 13.42.0, Vue 3.5, Inertia 3, Tailwind CSS 4, Vite 8 y MySQL 8.4. La versión exacta de cada dependencia se fija en `composer.lock` y `package-lock.json`.

## Instalación

1. Clonar el repositorio y entrar al proyecto:

   ```bash
   git clone https://github.com/BenjaminV3lasco/mau-star-wars.git
   cd mau-star-wars
   ```

2. Instalar las dependencias fijadas en los lockfiles:

   ```bash
   composer install
   npm ci
   ```

3. Crear una base vacía en MySQL 8 con un usuario que tenga permisos para crear tablas. Por ejemplo, desde el cliente MySQL:

   ```sql
   CREATE DATABASE mau_star_wars CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

4. Copiar `.env.example` a `.env` y completar `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME` y `DB_PASSWORD` con los datos de **tu** MySQL. El ejemplo trae `mau_app` como nombre de usuario de referencia; se puede reemplazar por uno ya existente. Nunca subir `.env` al repositorio.

   En PowerShell:

   ```powershell
   Copy-Item .env.example .env
   ```

   En macOS o Linux:

   ```bash
   cp .env.example .env
   ```

5. Generar la clave, crear las tablas y compilar los recursos:

   ```bash
   php artisan key:generate
   php artisan migrate
   npm run build
   ```

6. Iniciar la aplicación:

   ```bash
   php artisan serve
   ```

   Abrir [http://127.0.0.1:8000/personajes](http://127.0.0.1:8000/personajes). El servidor permanece activo mientras la terminal esté abierta. Para desarrollo visual, se puede ejecutar `npm run dev` en otra terminal; para usar el build anterior no hace falta.

Si PHP devuelve `cURL error 60` al importar, hay que configurar un bundle de autoridades certificadoras en el `php.ini` que usa `php --ini`, mediante `curl.cainfo` y `openssl.cafile`, y reiniciar el servidor. En Windows con Herd Lite se verificó con el [bundle oficial de curl](https://curl.se/ca/cacert.pem) y su checksum publicado. **La verificación HTTPS debe permanecer habilitada.** El procedimiento y la incidencia real están en [context/importacion-swapi.md](context/importacion-swapi.md).

## Funcionalidades implementadas

- Listado de personajes en tarjetas, paginado de diez en diez, con altura, masa, género y origen Manual/SWAPI.
- Alta y edición con validación en el navegador y el servidor. Los datos opcionales pueden quedar vacíos.
- Eliminación con confirmación y mensajes de resultado.
- Importación por ID del 1 al 83 desde `https://swapi.py4e.com/api/people/{id}/`, con estados de carga, error y éxito; control de duplicados, errores HTTP y timeout.
- Componentes Vue reutilizables para formulario y paginación; diseño Star Wars adaptable a móviles.
- Búsqueda en vivo por nombre (con una breve espera para evitar consultas por cada tecla) y filtro por género.
- Ordenamiento ascendente o descendente por número de ficha, nombre, altura, masa o género, aplicado antes de paginar; los datos de altura y masa vacíos aparecen al final.
- Selector de modo oscuro/claro, con preferencia guardada en el navegador.

- Pruebas HTTP automatizadas y un servicio específico para consultar y transformar SWAPI.

Las medidas importadas fuera de los límites del formulario quedan vacías. Los valores adicionales de género se representan como `n/a`; `planeta_origen` queda vacío porque SWAPI entrega una URL para `homeworld` y la consigna no exige otra consulta. El nombre de un droide importado, como R2-D2, puede conservar sus números al editarse. La regla adicional pedida para nombres manuales sigue prohibiendo números.

## Comprobación rápida

```bash
php artisan route:list --path=personajes
php artisan test
npm run build
```

`php artisan test` utiliza SQLite **en memoria** según `phpunit.xml` y comprueba que la conexión sea esa antes de renovar tablas. No borra ni modifica los personajes de la base MySQL local. Última ejecución: **55 pruebas y 232 assertions correctas**. También se comprobó en Edge la importación real de R2-D2, su edición, un intento duplicado y el modal a 320 px.

## Decisiones técnicas

### 1. Organización del backend

`PersonajeController` coordina las acciones CRUD y la importación. `GuardarPersonajeRequest` e `ImportarPersonajeRequest` validan las entradas; el modelo `Personaje` gestiona la persistencia mediante Eloquent. `SwapiService` concentra la llamada externa y el mapeo. Así el controlador conserva una responsabilidad clara y la consulta a SWAPI se puede probar sin depender de la red.

### 2. Componentes Vue

`PersonajeForm.vue` se usa en Crear y Editar: presenta los mismos campos y errores sin duplicar ambos formularios. `Paginacion.vue` recibe el paginador y dibuja los enlaces Anterior/Siguiente en el listado. `AppLayout.vue` comparte la navegación y el marco visual de todas las páginas. El modal de importación concentra la interacción por ID.

### 3. Errores de SWAPI

El servidor valida el ID antes de consultar. El cliente HTTP tiene 5 segundos para conectar y 15 segundos para completar la petición. Una respuesta 404, otro estado HTTP, un timeout o datos incompletos producen mensajes claros y no guardan una ficha. El índice único impide importar el mismo ID dos veces, incluso si dos solicitudes llegan juntas. El modal conserva el ID para reintentar.

### 4. Validaciones

Laravel valida los datos en los `FormRequest`, porque también debe proteger peticiones que no pasan por Vue. Los formularios HTML comprueban lo básico y muestran los errores recibidos mediante `useForm()`. La base refuerza la unicidad de `swapi_id`. El origen SWAPI y su ID se asignan sólo en el flujo de importación, sin confiar en campos adicionales enviados por el navegador.

### 5. Mayor dificultad

La consulta real a SWAPI desde PHP falló inicialmente con `cURL error 60`: faltaba un bundle de certificados configurado en Herd Lite. Se instaló un bundle oficial verificado por SHA-256 y se reinició el servidor; después SWAPI devolvió HTTP 200 y se guardó R2-D2. También hubo que adaptar la regla adicional de nombres manuales para permitir R2-D2 al editar un importado.

### 6. Uso de inteligencia artificial

Se usó Codex para ayudar a organizar la consigna, preparar el entorno, implementar el CRUD y la importación, proponer el diseño, escribir pruebas y documentación. El resultado se comprobó con pruebas automatizadas, compilación de Vite y recorridos en Edge contra MySQL y SWAPI reales. El candidato debe revisar personalmente el código y poder explicar o modificar cada flujo durante la entrevista.

### 7. Mejoras futuras

Como ampliación, agregaría búsqueda y filtros en el listado, además de pruebas de interfaz automatizadas. Antes de sumar funciones, completaría una nueva instalación y revisaría accesibilidad con lector de pantalla.

### 8. Autoevaluación

**Propuesta para revisar con el candidato: 8/10.** Las funciones obligatorias están implementadas y el manejo de errores de importación tiene pruebas concretas. El README, la instalación limpia y la visibilidad pública deben quedar confirmados antes de considerar cerrada la entrega.

### 9. Tiempo invertido

**Tiempo estimado por el candidato: 8 horas de trabajo.**

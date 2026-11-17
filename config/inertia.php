<?php

// Inertia busca las páginas en resources/js/pages (minúscula). Este proyecto usa
// Pages, y en Linux la diferencia importa: sin esta ruta las pruebas fallan.
return [
    'pages' => [
        'ensure_pages_exist' => false,
        'paths' => [resource_path('js/Pages')],
        'extensions' => ['vue'],
    ],
];

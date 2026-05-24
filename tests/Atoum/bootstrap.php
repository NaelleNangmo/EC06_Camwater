<?php

// Autoloader Composer (Laravel + atoum classmap)
require_once __DIR__ . '/../../vendor/autoload.php';

// Autoloader natif d'atoum (nécessaire pour résoudre ses dépendances internes)
require_once __DIR__ . '/../../vendor/atoum/atoum/classes/autoloader.php';

// Bootstrap Laravel (sans HTTP kernel)
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

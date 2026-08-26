<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
try {
    echo view('vrmanagement', ['rooms' => []])->render();
} catch (Throwable $e) {
    echo "ERROR:\n" . $e->getMessage() . "\n" . $e->getTraceAsString();
}

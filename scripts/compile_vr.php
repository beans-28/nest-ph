<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$compiler = $app->make(Illuminate\View\Compilers\BladeCompiler::class);
$src = file_get_contents(__DIR__ . '/../resources/views/vrmanagement.blade.php');
$compiled = $compiler->compileString($src);
echo $compiled;

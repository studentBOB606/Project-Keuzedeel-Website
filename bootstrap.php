<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Engines\PhpEngine;
use Illuminate\View\Factory;
use Illuminate\View\FileViewFinder;

// Initialize Eloquent ORM
$capsule = new Capsule;

$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => 'localhost',
    'database'  => 'studenten',
    'username'  => 'root',
    'password'  => '',
    'charset'   => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix'    => '',
]);

$capsule->setEventDispatcher(new Dispatcher(new Container));
$capsule->setAsGlobal();
$capsule->bootEloquent();

// Initialize Blade templating
$filesystem = new Filesystem;
$viewPaths = [__DIR__ . '/views'];
$cachePath = __DIR__ . '/storage/views';

// Create cache directory if it doesn't exist
if (!file_exists($cachePath)) {
    mkdir($cachePath, 0755, true);
}

$compiler = new BladeCompiler($filesystem, $cachePath);

$resolver = new EngineResolver;
$resolver->register('blade', function () use ($compiler) {
    return new CompilerEngine($compiler);
});

$resolver->register('php', function () {
    return new PhpEngine;
});

$finder = new FileViewFinder($filesystem, $viewPaths);
$factory = new Factory($resolver, $finder, new Dispatcher(new Container));

// Make view factory available globally
function view($view, $data = []) {
    global $factory;
    return $factory->make($view, $data)->render();
}

// Keep the existing Database class and Auth for backward compatibility
require_once __DIR__ . '/PHP/classes.php';

<?php

use DI\Application;
use DI\Container;
use DI\FileSystem;
use DI\Logger;

require 'vendor/autoload.php';

$container = new Container();

$container->register('filesystem', function () {
    return new FileSystem();
});

$container->register('logger', function ($container) {
    return new Logger($container->get('filesystem'));
});

$container->register('application', function ($container) {
    return new Application($container->get('logger'));
});

$app = $container->get('application');
echo $app->initialize();

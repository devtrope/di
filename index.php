<?php

use DI\Container;

require 'vendor/autoload.php';

try {
    $container = new Container();
    $container->load('config/services.php');
    $app = $container->get(\DI\Application::class);
    echo $app->initialize();
} catch (\Exception $e) {
    die($e->getMessage());
}

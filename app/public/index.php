<?php

use DI\Container;
use DI\ContainerBuilder;
use Slim\Factory\AppFactory;
use DI\Bridge\Slim\Bridge as SlimAppBridge;

require __DIR__ . '/../vendor/autoload.php';

$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions(__DIR__ . "/../src/definitions.php");
$container = $containerBuilder->build();


AppFactory::setContainer($container);
$app = AppFactory::create();

// $app = SlimAppBridge::create($container);

// Routes
require_once __DIR__ . "/../routes/api.php";
require_once __DIR__ . "/../routes/web.php";

$app->run();
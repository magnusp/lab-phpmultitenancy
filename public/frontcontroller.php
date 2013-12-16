<?php
require '../vendor/autoload.php';
set_exception_handler(function($exception) {
    http_response_code(500);
});

ini_set('session.save_handler', 'redis');
ini_set('session.save_path', 'tcp://127.0.0.1:6379');

$log = new \Monolog\Logger('ErrorLog');
$log->pushHandler(new \Monolog\Handler\ErrorLogHandler());
\Monolog\ErrorHandler::register($log);

$klein = new \Klein\Klein();

$klein->respond(function($request, $response, $service, $app) {
    $rootPath = implode(DIRECTORY_SEPARATOR, [__DIR__, '..']);

    $loader = new Twig_Loader_Filesystem(implode(DIRECTORY_SEPARATOR, [$rootPath, 'templates']));

    $app->twig = new Twig_Environment($loader, [
        'cache' => implode(DIRECTORY_SEPARATOR, [$rootPath, 'var', 'cache', 'twig']),
        'debug' => true
    ]);
});

$klein->respond('GET', '/hello', function($request, $response, $service, $app) {
  return $app->twig->render('base.twig');
});


$klein->dispatch();
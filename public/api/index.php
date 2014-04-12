<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/settings.php';
require_once ROOT . '/vendor/autoload.php';

use RedBean_Facade as R;

R::setup("mysql:host={$_settings['db']['host']};dbname={$_settings['db']['db']}", $_settings['db']['user'], $_settings['db']['pw']);

$app = new \Slim\Slim();
$app->contentType('application/json');
$app->add(new \mtc2ms\SlimMiddleware\Partials());
$app->add(new \mtc2ms\SlimMiddleware\TranslateRequest());
$app->add(new \mtc2ms\SlimMiddleware\AuthenticationToken());
$app->add(new \Slim\Middleware\SessionCookie(['name' => 'Dough', 'secret' => SECRET, 'expires' => '60 minutes']));

$app->get('/', function () use ($app) {
    echo json_encode(['requested' => 'nothing']);
});

$app->get('/test', function () use ($app) {
    echo json_encode(['check' => 'ok']);
});

$app->get('/login', function () use ($app) {
    echo json_encode(['translation' => 'ok']);
});

$app->get('/logout', function () use ($app) {
    echo json_encode(['logout' => 'ok']);
});

$app->get('/partials/:partial', function ($partial) use ($app) {
    $_partial = R::findOne('route', ' route = :route ', [':route' => $partial]);
    echo json_encode($_partial ? R::exportAll($_partial->ownPartial) : ['route' => 'unknown']);
});

$app->get('/(:segments+)', function ($segments = []) {
    echo json_encode(['route' => 'unknown']);
});

$app->post('/login', function () use ($app) {
    echo json_encode(['ice-cream' => $app->environment()->Icecream]);
});

$app->run();

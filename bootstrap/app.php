<?php

use Cart\App;
use Slim\Views\Twig;
use Slim\Csrf\Guard;
use Illuminate\Database\Capsule\Manager as Capsule;
use Respect\Validation\Validator as v;
//use Slim\Router;

session_start();

require __DIR__ . '/../vendor/autoload.php';

$app = new App;

$container = $app->getContainer();

$capsule = new Capsule;
$capsule->addConnection([
	'driver' => 'mysql',
	'host' => '127.0.0.1',
	'database' => 'cart',
	'username' => 'root',
	'password' => '4444',
	'charset' => 'utf8',
	'collation' => 'utf8_unicode_ci',
	'prefix' => ''
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

//Braintree_Configuration::environment('sandbox');
// Braintree_Configuration::merchantId('7tr4jqxzxtz63ydx');
// Braintree_Configuration::publicKey('sc8fyp5vgdxkcsvd');
// Braintree_Configuration::privateKey('22725fd16e1076d7514272fb9d7ae923');

require __DIR__ . '/../app/routes.php';

$app->add(new \Cart\Middleware\ValidationErrorsMiddleware($container->get(Twig::class)));
$app->add(new \Cart\Middleware\OldInputMiddleware($container->get(Twig::class)));
$app->add(new \Cart\Middleware\CsrfViewMiddleware($container->get(Twig::class), $container->get(Guard::class) ));

//$app->add( new  \Cart\Middleware\TestMiddleware($container->get('router'), $container->get(Twig::class)));

$app->add($container->get(Guard::class));
v::with('Cart\\Validation\\Rules\\');
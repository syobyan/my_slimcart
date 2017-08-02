<?php

use Cart\Basket\Basket;
use function DI\get;
use Cart\Models\Address;
use Cart\Models\Customer;
use Cart\Models\Order;
use Cart\Models\Payment;
use Cart\Models\Product;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;
use Slim\Flash\Messages;
use Interop\Container\ContainerInterface;
use Cart\Auth\Auth;
use Cart\Auth\AdminAuth;
use Cart\Support\Storage\SessionStorage;
use Cart\Support\Storage\Contracts\StorageInterface;
use Cart\Validation\Contracts\ValidatorInterface;
use Cart\Validation\Validator;
//use Cart\Middleware\GuestMiddleware;
use Cart\Middleware\CsrfViewMiddleware;
use Slim\Csrf\Guard;
return [
	'router' => get(Slim\Router::class),
	
	ValidatorInterface::class => function(ContainerInterface $c){
		return new Validator;
	},
	StorageInterface::class => function(ContainerInterface $c){
		return new SessionStorage('cart');
	},
	Twig::class => function(ContainerInterface $c){
		$twig = new Twig(__DIR__ . '/../resources/views', [
			'cache' => false
		]);

		$twig->addExtension(new TwigExtension(
			$c->get('router'),
			$c->get('request')->getUri()
		));

		$twig->getEnvironment()->addGlobal('auth', $c->get(auth::class));
		$twig->getEnvironment()->addGlobal('adminauth', $c->get(AdminAuth::class));
		$twig->getEnvironment()->addGlobal('basket', $c->get(Basket::class));
	  	$twig->getEnvironment()->addGlobal('flash', $c->get(Messages::class));
		return $twig;

	},
	Messages::class => function(ContainerInterface $c) {
	 	return new Messages;
	},
	Guard::class => function(ContainerInterface $c) {
	 	return new Guard;
	},
	Auth::class => function(ContainerInterface $c) {
	 	return new Auth;
	},
	AdminAuth::class => function(ContainerInterface $c) {
	 	return new AdminAuth;
	},
	Address::class => function(ContainerInterface $c){
		return new Address;
	},
	Customer::class => function(ContainerInterface $c){
		return new Customer;
	},
	Order::class => function(ContainerInterface $c){
		return new Order;
	},
	Payment::class => function(ContainerInterface $c){
		return new Payment;
	},
	Product::class => function(ContainerInterface $c){
		return new Product;
	},
	Basket::class => function(ContainerInterface $c){
		return new Basket(
			$c->get(SessionStorage::class),
			$c->get(Product::class)
		);
	},
	// GuestMiddleware::class => function(ContainerInterface $c){
	// 	return[
	// 		$c->get(Auth::class),
	// 		$c->get(Twig::class),
	// 		$c->get('router'),
	// 		$c->get(Messages::class)
	// 	];
	// },

];

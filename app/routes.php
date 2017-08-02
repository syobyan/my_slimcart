<?php

// use Cart\Middleware\AuthMiddleware;
// use Cart\Middleware\GuestMiddleware;
use Slim\Views\Twig;
use Cart\Auth\Auth;
use Cart\Auth\AdminAuth;
use Slim\Router;
use Slim\Flash\Messages;

$app->get('/', ['Cart\Controllers\HomeController', 'index'])->setName('home');

$app->get('/products/{slug}', ['Cart\Controllers\ProductController', 'get'])->setName('product.get');

$app->get('/cart', ['Cart\Controllers\CartController', 'index'])->setName('cart.index');

$app->get('/cart/add/{slug}/{quantity}', ['Cart\Controllers\CartController', 'add'])->setName('cart.add');
$app->post('/cart/update/{slug}', ['Cart\Controllers\CartController', 'update'])->setName('cart.update');



$app->group('', function(){
	//user
	$this ->get('/auth/signin', ['Cart\Controllers\AuthController', 'getSignIn'])->setName('auth.signin');
	$this ->post('/auth/signin', ['Cart\Controllers\AuthController', 'postSignIn']);
	$this ->get('/auth/signup', ['Cart\Controllers\AuthController', 'getSignUp'])->setName('auth.signup');
	$this ->post('/auth/signup', ['Cart\Controllers\AuthController', 'postSignUp']);

	//admin
	$this ->get('/admin/signin', ['Cart\Controllers\AdminController', 'getSignIn'])->setName('admin.signin');
	$this ->post('/admin/signin', ['Cart\Controllers\AdminController', 'postSignIn']);
})->add(new  \Cart\Middleware\GuestMiddleware(
	$container->get(Auth::class),
	$container->get(AdminAuth::class),
	$container->get(Twig::class),
	$container->get('router')));



$app->group('', function(){
	$this->get('/order/record', ['Cart\Controllers\OrderRecordController', 'getOrderProcessing'])->setName('order.record');
	$this->get('/order', ['Cart\Controllers\OrderController', 'index'])->setName('order.index');
	$this->get('/order/{hash}', ['Cart\Controllers\OrderController', 'show'])->setName('order.show');
	$this->post('/order', ['Cart\Controllers\OrderController', 'create'])->setName('order.create');
	$this ->get('/auth/signout', ['Cart\Controllers\AuthController', 'getSignOut'])->setName('auth.signout');
	$this ->get('/auth/password/chagne', ['Cart\Controllers\PasswordController', 'getChangePassword'])->setName('auth.password.change');
	$this ->post('/auth/password/chagne', ['Cart\Controllers\PasswordController', 'postChangePassword']);
})->add(new  \Cart\Middleware\AuthMiddleware(
	$container->get(Auth::class),
	$container->get(Twig::class),
	$container->get('router'),
	$container->get(Messages::class)));


$app->group('', function(){
	$this->get('/admin/home', ['Cart\Controllers\AdminController', 'index'])->setName('admin.home');
	$this->get('/admin/products', ['Cart\Controllers\AdminProductController', 'getProducts'])->setName('admin.products');
	$this->get('/admin/product/add', ['Cart\Controllers\AdminProductController', 'getCreate'])->setName('admin.product.add');
	$this->post('/admin/product/add', ['Cart\Controllers\AdminProductController', 'create']);
	$this->get('/admin/product/update/{id}', ['Cart\Controllers\AdminProductController', 'getUpdate'])->setName('admin.product.update.get');
	$this->post('/admin/product/update/{id}', ['Cart\Controllers\AdminProductController', 'update'])->setName('admin.product.update');
	$this->post('/admin/product/del', ['Cart\Controllers\AdminProductController', 'del'])->setName('admin.product.del');

	$this->get('/admin/orders', ['Cart\Controllers\AdminOrderController', 'getOrders'])->setName('admin.orders');
	$this->get('/admin/order/history/{id}', ['Cart\Controllers\AdminOrderController', 'getOrderHistorySingle'])->setName('admin.order.history.get');
	$this->get('/admin/order/history', ['Cart\Controllers\AdminOrderController', 'getOrderHistory'])->setName('admin.order.history');
	$this ->post('/admin/order/status', ['Cart\Controllers\AdminOrderController', 'status'])->setName('admin.order.status');

	$this ->get('/admin/password/chagne', ['Cart\Controllers\AdminController', 'getChangePassword'])->setName('admin.password.change');
	$this ->post('/admin/password/chagne', ['Cart\Controllers\AdminController', 'postChangePassword']);


	$this ->get('/admin/inventory', ['Cart\Controllers\AdminInventoryController', 'index'])->setName('admin.inventory');

	$this ->get('/admin/signout', ['Cart\Controllers\AdminController', 'getSignOut'])->setName('admin.signout');
})->add(new  \Cart\Middleware\AdminMiddleware(
	$container->get(AdminAuth::class),
	$container->get(Twig::class),
	$container->get('router'),
	$container->get(Messages::class)));

$app->get('/braintree/token', ['Cart\Controllers\BraintreeController', 'token'])->setName('braintree.token');



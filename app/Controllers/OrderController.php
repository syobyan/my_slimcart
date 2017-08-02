<?php

namespace Cart\Controllers;

use Slim\Router;
use Slim\Views\Twig;
use Cart\Basket\Basket;
use Cart\Models\Address;
use Cart\Models\Customer;
use Cart\Models\Order;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Cart\Validation\Contracts\ValidatorInterface;
use Cart\Validation\Rules\emailAvailable;
//use Cart\Validation\Forms\OrderForm;
use Respect\Validation\Validator as v;
use Cart\Auth\Auth;
//use Braintree_Transaction;

class OrderController{

	protected $basket;

	protected $router;

	protected $auth;

	protected $validator;

	public function __construct(Basket $basket, Router $router, ValidatorInterface $validator, Auth $auth){
		$this->basket = $basket;
		$this->router = $router;
		$this->validator = $validator;
		$this->auth = $auth;
	}

	public function index(Request $request, Response $response, Twig $view, Customer $customer){
		$this->basket->refresh();
		$customer = $customer::where('id',$this->auth->user()->id)->first();
		//$customer = $customer->where('id', $this->auth->user()->id);
		//print_r($customer);
		// var_dump($customer);
		// die();
		if(!$this->basket->subTotal()){
			return $response->withRedirect($this->router->pathFor('cart.index'));
		}
		
		//return $view->render($response, 'order/index.twig');
		 return $view->render($response, 'order/index.twig',[
			'customer'=> $customer,
			]);
	}

	public function show($hash, Request $request, Response $response, Twig $view, Order $order){
		

		$order = $order->with(['address', 'products'])->where('hash', $hash)->first();

		if (!$order) {
			return $response->withRedirect($router->pathFor('home'));
		}

		return $view->render($response, 'order/show.twig', [
			'order' => $order,
		]);
	}

	public function create(Request $request, Response $response){
		$this->basket->refresh();

		if(!$this->basket->subTotal()){
			return $response->withRedirect($this->router->pathFor('cart.index'));
		}

		// if(!$request->getParam('payment_method_nonce')){
		// 	return $response->withRedirect($this->router->pathFor('order.index'));
		// }

		//$validation = $this->validator->validate($request, OrderForm::rules());
		$validation = $this->validator->validate($request, [
		//	'email' =>v::noWhitespace()->notEmpty()->emailAvailable(),
			'name' => v::alpha(' '),
			'address1' => v::alnum(' -'),
			//'address2' => v::optional(v::alnum(' -')),
			'city' => v::alnum(' '),
			'postal_code' => v::alnum(' '),
			//'password' => v::noWhitespace()->notEmpty(),
			//'confirm_password'  =>  v::confirmPassword($request->getParam('password')),
		]);

		if($validation->fails()){
			return $response->withRedirect($this->router->pathFor('order.index'));
		}

		$hash = bin2hex(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM));
		$number = 'N'.date("ymdHis",strtotime('+6 hours')).rand('1','9999');
		// $customer = Customer::firstOrCreate([
		// 	'email' => $request->getParam('email'),
		// 	'name' => $request->getParam('name'),
		// 	//'password' => password_hash($request->getParam('password'), PASSWORD_DEFAULT),
		// ]);
	
		$address = Address::firstOrCreate([
			'address_one' => $request->getParam('address1'),
			//'address_two' => $request->getParam('address2'),
			'city' => $request->getParam('city'),
			'postal_code' => $request->getParam('postal_code'),
		]);

	//	$order = $customer->orders()->create([
		$order = Order::firstOrCreate([
			'hash' => $hash,
			'number' => $number,
			'paid' => false,
			'total' => ($this->basket->subTotal() + 5),
			'address_id' => $address->id,
			'customer_id' =>$this->auth->user()->id,
		]);

		$orderProducts = $this->basket->all();

		$order->products()->saveMany(
			$orderProducts,
			$this->getQuantities($orderProducts)
		);

		// $result = Braintree_Transaction::sale([
		// 	'amount' => $this->basket->subTotal() + 5,
		// 	'paymentMethodNonce' => $request->getParam('payment_method_nonce'),
		// 	'options' => [
		// 		'submitForSettlement' => True
		// 	]
		// ]);

		// $event =  new \Cart\Events\OrderWasCreated($order, $this->basket);

		// if(!$result->success){
		// 	$event->attach(new \Cart\Handlers\RecordFailedPayment);
		// 	$event->dispatch();

		// 	return $response->withRedirect($this->router->pathFor('order.index'));
		// }

		// $event->attach([
		// 	new \Cart\Handlers\MarkOrderPaid,
		// 	new \Cart\Handlers\RecordSuccessfulPayment($result->transaction->id),
		// 	new \Cart\Handlers\UpdateStock,
		// 	new \Cart\Handlers\EmptyBasket,
		// ]);

		// $event->dispatch();
		$this->basket->clear();
		return $response->withRedirect($this->router->pathFor('order.show', [
			'hash' => $hash,
		]));

		
	}

	protected function getQuantities($items){
		$quantities = [];

		foreach($items as $item){
			$quantities[] = ['quantity' => $item->quantity];
		}

		return $quantities;
	}

}

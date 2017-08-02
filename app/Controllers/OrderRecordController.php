<?php

namespace Cart\Controllers;

use Slim\Views\Twig;
use Cart\Models\Product;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Cart\Auth\Auth;
use Cart\Models\Customer;
use Cart\Models\Order;

use Illuminate\Pagination\Paginator;

class OrderRecordController{


	protected $view;

	public function __construct(Twig $view){
		$this->view = $view;
	}

	// public function index(Request $request, Response $response, Twig $view, Product $product){
	// 	$products = $product->all();

	// 	return $view->render($response, 'home.twig', [
	// 		'products' => $products
	// 	]);
	// }

	public function getOrderProcessing(Request $request, Response $response, Order $order, Customer $customer, Auth $auth)
	{
		//$order = $order->where('customer_id','=',$auth->user()->id)->get();
		// $order = $order->with(['address', 'products','customers'])
		// 				->where('customer_id','=',$auth->user()->id)
		// 				->get();
		$order = $order::where('customer_id', $auth->user()->id)->paginate(5, ['*'], 'page', $request->getParam('page'));
		
		return $this->view->render($response, 'order/record.twig', [
				'orders' => $order
		]);
	}

	public function getOrderHistory()
	{

	}

	public function testpages(Request $request, Response $response, Product $produtc, Order $order, Auth $auth)
	{
			$allpolls = $order::where('customer_id', $auth->user()->id)->paginate(2, ['*'], 'page', $request->getParam('page'));
			//$allpolls = $produtc->paginate(2, ['*'], 'page', $request->getParam('page'));
			return $this->view->render($response, 'test.twig', ['ccc' => $allpolls]);

	}

}

<?php

namespace Cart\Controllers;

use Slim\Router;
use Slim\Views\Twig;
use Cart\Models\Product;
use Cart\Models\Customer;
use Cart\Models\Order;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Cart\Validation\Contracts\ValidatorInterface;
use Respect\Validation\Validator as v;
use Slim\Flash\Messages;

class AdminOrderController{

	protected $view;

	protected $validator;

	protected $router;

	protected $flash;

	public function __construct(Twig $view, ValidatorInterface $validator, Router $router,  Messages $flash){
		$this->view = $view;
		$this->validator = $validator;
		$this->router = $router;
		$this->flash = $flash;
	}



	public function getOrders(Request $request, Response $response, Twig $view, Order $order)
	{
		$order = $order::where('status', '=', '0')->paginate(10, ['*'], 'page', $request->getParam('page'));

		return $this->view->render($response, 'admin/orders/order.twig', [
			'orders' => $order
		]);
	}

	public function status(Request $request, Response $response, Order $order)
	{

		$order = $order::where('id', '=', $request->getParam('id'))->update([
			'status' => '1',
		]);
		echo 'Success';
		// $this->flash->addMessage('info' ,'you have been ok!');

		// return $response->withRedirect($this->router->pathFor('admin.products'));
	}

	public function getOrderHistory(Request $request, Response $response, Twig $view, Order $order)
	{
		$order = $order::where('status', '=', '1')->paginate(10, ['*'], 'page', $request->getParam('page'));
		return $this->view->render($response, 'admin/history/orders/history.twig', [
			'orders' => $order
		]);
	}

	public function getOrderHistorySingle($id, Request $request, Response $response, Twig $view, Order $order)
	{
		$order = $order::where('id', '=', $id)->first();
		return $this->view->render($response, 'admin/history/orders/partials/viewsingle.twig', [
			'orders' => $order
		]);
	}



}

<?php

namespace Cart\Controllers;

use Slim\Router;
use Slim\Views\Twig;
use Slim\Flash\Messages;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Cart\Auth\AdminAuth;
use Cart\Models\Customer;
use Cart\Validation\Contracts\ValidatorInterface;
use Cart\Validation\Rules\emailAvailable;
use Respect\Validation\Validator as v;


class AdminInventoryController{

	protected $view;

	protected $flash;

	protected $router;

	protected $validator;

	public function __construct(Twig $view, Messages $flash, Router $router,  ValidatorInterface $validator){
		$this->view = $view;
		$this->flash = $flash;
		$this->router = $router;
		$this->validator = $validator;

	}

	public function index(Request $request, Response $response){

		return $this->view->render($response, 'admin/Inventory/inventory.twig');
	}




}

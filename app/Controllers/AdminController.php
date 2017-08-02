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


class AdminController{

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

		return $this->view->render($response, 'admin/home.twig');
	}


	public function getSignOut(Request $request, Response $response,  AdminAuth $auth)
	{
			$auth->logout();

			return $response->withRedirect($this->router->pathFor('admin.signin'));

	}

	public function getSignIn(Request $request, Response $response)
	{
		return $this->view->render($response, 'admin/auth/signin.twig');
	}

	public function postSignIn(Request $request, Response $response, AdminAuth $auth )
	{
	
		$auth = $auth->attempt(
			$request->getParam('username'),
			$request->getParam('password')
		);

		if(!$auth){
			$this->flash->addMessage('error', 'could not sign you in those details,');
		 	return $response->withRedirect($this->router->pathFor('admin.signin'));
		}
		return $response->withRedirect($this->router->pathFor('admin.orders'));
	}

	public function getChangePassword(Request $request, Response $response)
	{
		return $this->view->render($response, 'admin/auth/password/change.twig');
	}

	public function postChangePassword(Request $request, Response $response, AdminAuth $auth)
	{
		$validation = $this->validator->validate($request, [
			'password_old' => v::noWhitespace()->notEmpty()->matchesPassword($auth->user()->password),
			'password' => v::noWhitespace()->notEmpty(),
			'confirm_password'  =>  v::notEmpty()->confirmPassword( $request->getParam( 'password' ) )
		]);

		if($validation->fails()){
			return $response->withRedirect($this->router->pathFor('admin.password.change'));
		}

		//die('change password');
		$auth->user()->setPassword($request->getParam('password'));
		$this->flash->addMessage('info', 'You password was changes.');
		unset($_SESSION['old']);
		return $response->withRedirect($this->router->pathFor('admin.password.change'));
	}


	



}

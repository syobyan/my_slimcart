<?php

namespace Cart\Controllers;

use Slim\Router;
use Slim\Views\Twig;
use Slim\Flash\Messages;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Cart\Auth\Auth;
use Cart\Models\Customer;
use Cart\Validation\Contracts\ValidatorInterface;
use Cart\Validation\Rules\emailAvailable;
use Respect\Validation\Validator as v;


class AuthController{

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


	public function getSignOut(Request $request, Response $response,  Auth $auth)
	{
			$auth->logout();

			return $response->withRedirect($this->router->pathFor('home'));

	}

	public function getSignIn(Request $request, Response $response)
	{
		return $this->view->render($response, 'auth/signin.twig');
	}

	public function postSignIn(Request $request, Response $response, Auth $auth )
	{
		$auth = $auth->attempt(
			$request->getParam('email'),
			$request->getParam('password')
		);

		if(!$auth){
			$this->flash->addMessage('error', 'could not sign you in those details,');
		 	return $response->withRedirect($this->router->pathFor('auth.signin'));
		}
		return $response->withRedirect($this->router->pathFor('home'));
	}

	public function getSignUp(Request $request, Response $response)
	{
		return $this->view->render($response, 'auth/signup.twig');
	}

	public function postSignUp(Request $request, Response $response, Auth $auth )
	{
		$validation = $this->validator->validate($request, [
			'email' => v::noWhitespace()->notEmpty()->emailAvailable(),
			'name' => v::noWhitespace()->notEmpty()->alpha(),
			'phone' => v::noWhitespace()->notEmpty(),
			'password' => v::noWhitespace()->notEmpty(),
			'confirm_password'  =>v::notEmpty()->confirmPassword($request->getParam( 'password' ) ),
		]);

		if($validation->fails()){
			return $response->withRedirect($this->router->pathFor('auth.signup'));
		}


		Customer::create([
			'email' => $request->getParam('email'),
			'name' => $request->getParam('name'),
			'phone' => $request->getParam('phone'),
			'password' => password_hash($request->getParam('password'), PASSWORD_DEFAULT),
		]);

		$this->flash->addMessage('info' ,'you have been signed up!');

		$auth->attempt($request->getParam('email'), $request->getParam('password'));

		return $response->withRedirect($this->router->pathFor('home'));

	}




}

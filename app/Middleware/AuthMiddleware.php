<?php

namespace Cart\Middleware;

use Cart\Auth\Auth;
use Slim\Router;
use Slim\Views\Twig;
use Slim\Flash\Messages;


class AuthMiddleware
{
	protected $auth;

	protected $view;
	
	protected $router;

	protected $flash;


	//public function __construct(Auth $auth, Twig $view, Messages $flash){
	public function __construct(Auth $auth, Twig $view, Router $router,  Messages $flash){
		$this->auth = $auth;
		$this->router = $router;
		$this->view = $view;
		$this->flash = $flash;
	}

	public function __invoke($request, $response, $next)
	{
		if(!$this->auth->check()){

			$this->flash->addMessage('error', 'Please sing in before doing that.');

			return $response->withRedirect($this->router->pathFor('auth.signin'));
		}

		$response = $next($request, $response);
		return $response;
	}
}


?>
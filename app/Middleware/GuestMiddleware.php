<?php
namespace Cart\Middleware;

use Slim\Views\Twig;
use Cart\Auth\Auth;
use Cart\Auth\AdminAuth;
use Slim\Router;

class GuestMiddleware
{
 	protected $auth;

	protected $adminauth;

	protected $view;

	protected $router;

	public function __construct(Auth $auth, AdminAuth $adminauth, Twig $view, Router $router)
	{
		$this->auth = $auth;
		$this->adminauth = $adminauth;
		$this->view = $view;
		$this->router = $router;
	}

	public function __invoke($request, $response, $next)
	{
		if($this->auth->check()){
			return $response->withRedirect($this->router->pathFor('home'));
		}

		if( $this->adminauth->check()){
				return $response->withRedirect($this->router->pathFor('admin.home'));
				//return $this->view->render($response, 'admin/error/error.twig');
		}

		$response = $next($request, $response);
		return $response;
	}
}


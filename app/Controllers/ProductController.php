<?php

namespace Cart\Controllers;

use Slim\Router;
use Slim\Views\Twig;
use Cart\Models\Product;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Cart\Validation\Contracts\ValidatorInterface;
use Respect\Validation\Validator as v;
class ProductController{

	protected $view;

	protected $validator;

	protected $router;

	public function __construct(Twig $view, ValidatorInterface $validator, Router $router){
		$this->view = $view;
		$this->validator = $validator;
		$this->router = $router;
	}

	public function get($slug, Request $request, Response $response, Twig $view, Router $router, Product $product){
		$product = $product->where('slug', $slug)->first();

		if (!$product) {
			return $response->withRedirect($router->pathFor('home'));
		}

		return $this->view->render($response, 'products/product.twig', [
			'product' => $product
		]);
	}



}

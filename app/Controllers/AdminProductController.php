<?php

namespace Cart\Controllers;

use Slim\Router;
use Slim\Views\Twig;
use Cart\Models\Product;
use Cart\Models\Customer;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Cart\Validation\Contracts\ValidatorInterface;
use Respect\Validation\Validator as v;
use Slim\Flash\Messages;

use Cart\Validation\Uploadfile\ValidationUploadFile;

class AdminProductController{

	protected $view;

	protected $validator;

	protected $router;

	protected $flash;

	protected $filefoledr = '../public/images/';

	public function __construct(Twig $view, ValidatorInterface $validator, Router $router,  Messages $flash){
		$this->view = $view;
		$this->validator = $validator;
		$this->router = $router;
		$this->flash = $flash;
	}

	public function getProducts(Request $request, Response $response, Twig $view, Product $product)
	{
		
		//$product = $product->all();
		$product = $product->paginate(10, ['*'], 'page', $request->getParam('page'));

		if (!$product) {
			return $response->withRedirect($this->router->pathFor('home'));
		}

		return $this->view->render($response, 'admin/products/product.twig', [
			'products' => $product
		]);
	}

	public function getCreate(Request $request, Response $response)
	{
		return $this->view->render($response, 'admin/products/partials/add.twig');
	}

	public function create(Request $request, Response $response, Product $product)
	{

		$validation = $this->validator->validate($request, [
			'title' => v::alpha(' '),
			'price' => v::alnum(' -'),
			'stock' => v::alnum(' '),
			'description' => v::alnum(' '),
		]);

		if($validation->fails()){
			return $response->withRedirect($this->router->pathFor('admin.product.add'));
		}


		//upload file
		$files = $request->getUploadedFiles();
   		if (empty($files['file'])) {
        	throw new Exception('Expected a newfile');
    	}

    	$newfile = $files['file'];
 		$valid_extensions = array('jpeg', 'jpg', 'png', 'gif'); // valid extensions
 		$imgSize = $newfile->getSize();
	    $uploadFileName = $newfile->getClientFilename();
	    if($uploadFileName){
	   		if ($newfile->getError() === UPLOAD_ERR_OK) {
				$imgExt = strtolower(pathinfo($uploadFileName,PATHINFO_EXTENSION)); // get image extension
		   		$userpic = time().rand(1000,1000000).".".$imgExt;
		   		if(in_array($imgExt, $valid_extensions)){
		   			 if($imgSize < 5000000) {
						$newfile->moveTo($this->filefoledr.$userpic);
		   			 }
		   			 else{
		   			 	    $errMSG = "Sorry, your file is too large.";
		   			 	    return false;
		   			 }
		   		}
		   		else{
					$errMSG = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
					return false;
				}
			}
		}
		else{
			$this->flash->addMessage('error' ,'error while inserting....');
			return $response->withRedirect($this->router->pathFor('admin.product.add'));
		}


		$product = product::firstOrCreate([
			'title' => $request->getParam('title'),
			'price' => $request->getParam('price'),
			'stock' => $request->getParam('stock'),
			'slug' 	=> $request->getParam('title'),
			'description' => $request->getParam('description'),
			'image' => $userpic,
		]);

		$this->flash->addMessage('info' ,'Successfully Create ...');

		return $response->withRedirect($this->router->pathFor('admin.products'));


	}

	public function getUpdate($id, Request $request, Response $response, Product $product)
	{
		$product = $product::where('id', $id)->first();

		if (!$product) {
			return $response->withRedirect($this->router->pathFor('admin.product.update'));
		}

		return $this->view->render($response, 'admin/products/partials/update.twig', [
			'product' => $product
			]);
	}

	public function update($id, Request $request, Response $response, Product $product)
	{
		$product =$product::where('id', '=', $id)->first(['image']);
		
		$validation = $this->validator->validate($request, [
			'title' => v::alpha(' '),
			'price' => v::alnum(' -'),
			'stock' => v::alnum(' '),
			'description' => v::alnum(' '),
		]);

		if($validation->fails()){
			return $response->withRedirect($this->router->pathFor('admin.product.update', ['id'=>$id]));
		}

		//upload file
		$files = $request->getUploadedFiles();
   		if (empty($files['file'])) {
        	throw new Exception('Expected a newfile');
    	}

    	$newfile = $files['file'];
 		$valid_extensions = array('jpeg', 'jpg', 'png', 'gif'); // valid extensions
 		$imgSize = $newfile->getSize();
	    $uploadFileName = $newfile->getClientFilename();
	    if($uploadFileName){
	   		if ($newfile->getError() === UPLOAD_ERR_OK) {
				$imgExt = strtolower(pathinfo($uploadFileName,PATHINFO_EXTENSION)); // get image extension
		   		$userpic = time().rand(1000,1000000).".".$imgExt;
		   		if(in_array($imgExt, $valid_extensions)){
		   			 if($imgSize < 5000000) {
		   			 	unlink($this->filefoledr .$product->image);
						$newfile->moveTo($this->filefoledr.$userpic);
		   			 }
		   			 else{
		   			 	    $errMSG = "Sorry, your file is too large.";
		   			 	    return false;
		   			 }
		   		}
		   		else{
					$errMSG = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
					return false;
				}
			}
		}
		else{
			  $userpic = $product->image; // old image from database
		}

		$product = $product::where('id', '=', $id)->update([
			'title' => $request->getParam('title'),
			'price' => $request->getParam('price'),
			'stock' => $request->getParam('stock'),
			'slug' 	=> $request->getParam('title'),
			'description' => $request->getParam('description'),
			'image' => $userpic,

		]);
		$this->flash->addMessage('info' ,'Successfully Updated ...');

		return $response->withRedirect($this->router->pathFor('admin.products'));
	}

	public function del(Request $request, Response $response, Product $product)
	{
		$product =$product::where('id', '=', $request->getParam('id'))->first(['image']);
		unlink($this->filefoledr .$product->image);

		$product =$product::where('id', '=', $request->getParam('id'))->delete();
		if(!$product){
			return false;
		}

		echo 'Success';
	}

}

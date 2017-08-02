<?php 
namespace Cart\Middleware;

use Slim\Router;
use Slim\Views\Twig;

class PaginationMiddleware{

    protected $view;

   // protected $auth;

  //  protected $router;
//
    public function __construct( Twig $view){
        //$this->auth = $auth;
        $this->view = $view;
        //$this->router = $router;
    }

    public function __invoke($request, $response, $next) {

        $uri          = $request->getUri();
        $current_path = $uri->getPath();
        $route        = $request->getAttribute('route');

        
        if ($route) {

            $route_name = $route->getName();

            $uri_page_parameter = $request->getParam('page');
        
            // We want to retrieve the whole url including all the get parameters to get the current url itself
            // Excluding page (pagination) parameter.
            if ($uri_page_parameter != '') {
                $uri = str_replace(['?page=' . $uri_page_parameter, '&page=' . $uri_page_parameter], '', $uri);
            }

            // We'll also check if the request has been sent using get method
            $uri_request_sent = explode('?', $uri);

            // Route Information
            $this->view->getEnvironment()->addGlobal('uri', [
                'link' => $uri,
                'request_sent' => (isset($uri_request_sent[1])) ? true : false
            ]);
            $this->view->getEnvironment()->addGlobal('current_route', $route_name);
            $this->view->getEnvironment()->addGlobal('current_path', $current_path);
        }

        $response = $next($request, $response);
        return $response;
    }
}

 ?>
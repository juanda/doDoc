<?php

namespace Jazzyweb\doDocBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class BookAPIRestController extends Controller {

    protected function sendResponse($data)
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($data['json']));
        $response->setStatusCode($data['status_code']);

        return $response;
    }
    
    public function getAllBooksAction() {
        
        echo $this->getRequest()->get('rows');exit;
        $json_url = "http://www.trirand.com/blog/jqgrid/server.php?q=2&_search=false&nd=1347463000773&rows=10&page=1&sidx=id&sord=desc";
        $json = file_get_contents($json_url);
        $results['json'] = json_decode($json, TRUE);

        $results['status_code'] = 200;

        return $this->sendResponse($results);
    }

}

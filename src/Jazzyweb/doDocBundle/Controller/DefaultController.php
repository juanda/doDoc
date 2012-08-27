<?php

namespace Jazzyweb\doDocBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;


class DefaultController extends Controller
{
    
    public function getAllDocumentsAction()
    {      
        $request = $this->getRequest();
        
        $bookCode = $request->get('book');
        
        if ($bookCode == '') {
            throw $this->createNotFoundException('The book name must be provided in the request');
        }
        
        $docService = $this->get('jazzyweb.dodocmanager');
        
        $documents = $docService->getAllDocuments($bookCode);
        
        $response = new Response(json_encode($documents));
        $response->headers->set('Content-Type', 'application/json');
        
        return $response;
    }
}

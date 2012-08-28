<?php

namespace Jazzyweb\doDocBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller {
    
    protected function sendResponse($data)
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($data['json']));
        $response->setStatusCode($data['status_code']);  
        
        return $response;
    }

    public function getAllDocumentsAction() {
        
        $request = $this->getRequest();

        $bookCode = $request->get('book');
        
        $docService = $this->get('jazzyweb.dodocmanager');

        $result = $docService->getAllDocuments($bookCode);
                            
        return $this->sendResponse($result);
    }
    
    public function getDocumentAction()
    {
        $request = $this->getRequest();
        
        $bookCode = $request->get('book');
        $docName=$request->get('docname');
        
        $docService = $this->get('jazzyweb.dodocmanager');

        $result = $docService->getDocument($bookCode, $docName);
        
        return $this->sendResponse($result);
    }
    
    public function newDocumentAction()
    {
        $request = $this->getRequest();
        
        $bookCode = $request->get('book');
        $docName  = $request->get('docname');        
        
        $docService = $this->get('jazzyweb.dodocmanager');

        $result = $docService->createDocument($bookCode, $docName);
        
        return $this->sendResponse($result);
    }
    
    public function updateDocumentAction()
    {
        $request = $this->getRequest();
        
        $bookCode    = $request->get('book');
        $docName     = $request->get('docname');     
        $jsonContent =  $request->getContent();
        
        $content = json_decode($jsonContent);
                       
        $docService = $this->get('jazzyweb.dodocmanager');

        $result = $docService->saveDocument($bookCode, $docName, $content['content']);
      
        return $this->sendResponse($result);
    }
}

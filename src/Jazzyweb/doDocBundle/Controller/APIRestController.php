<?php

namespace Jazzyweb\doDocBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class APIRestController extends Controller {

    protected function sendResponse($data) {
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

    public function getDocumentAction() {
        $request = $this->getRequest();

        $bookCode = $request->get('book');
        $docName = $request->get('docname');

        $docService = $this->get('jazzyweb.dodocmanager');

        $result = $docService->getDocument($bookCode, $docName);

        return $this->sendResponse($result);
    }

    public function newDocumentAction() {
        $request = $this->getRequest();

        $bookCode = $request->get('book');
        $docName = $request->get('docname');
        $jsonContent = $request->getContent();

        $oContent = json_decode($jsonContent);

        $docService = $this->get('jazzyweb.dodocmanager');

        $content = (isset($oContent->content)) ? $oContent->content : null;

        $result = $docService->createDocument($bookCode, $docName, $content);

        return $this->sendResponse($result);
    }

    public function updateDocumentAction() {
        $request = $this->getRequest();

        $bookCode = $request->get('book');
        $docName = $request->get('docname');
        $jsonContent = $request->getContent();

        $oContent = json_decode($jsonContent);

        $docService = $this->get('jazzyweb.dodocmanager');

        $content = (isset($oContent->content)) ? $oContent->content : null;

        $result = $docService->saveDocument($bookCode, $docName, $content);

        return $this->sendResponse($result);
    }

    public function createOrSaveDocumentAction() {
        $request = $this->getRequest();

        $bookCode = $request->get('book');
        $docName = $request->get('docname');
        $jsonContent = $request->getContent();

        $oContent = json_decode($jsonContent);

        $docService = $this->get('jazzyweb.dodocmanager');

        $content = (isset($oContent->content)) ? $oContent->content : null;

        if ($request->getMethod() == "POST") {
            $result = $docService->createDocument($bookCode, $docName, $content);
        } else {
            $result = $docService->saveDocument($bookCode, $docName, $content);
        }

        return $this->sendResponse($result);
    }

    public function removeDocumentAction() {
        $request = $this->getRequest();

        $bookCode = $request->get('book');
        $docName = $request->get('docname');
        
        $docService = $this->get('jazzyweb.dodocmanager');
        
        $result = $docService->removeDocument($bookCode, $docName);
        
        return $this->sendResponse($result);
        
    }

}

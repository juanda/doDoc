<?php

namespace Jazzyweb\doDocBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class EasybookRestController extends Controller {

    protected function sendResponse($data) {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($data['json']));
        $response->setStatusCode($data['status_code']);

        return $response;
    }

    public function newBookAction() {

        try {
            $easyBook = $this->get('jazzyweb.dodoceasybookbridge');
            $result = $easyBook->newBook();
        } catch (\Exception $e) {

            $result['json']['message'] = $e->getMessage();
            $result['status_code'] = 500;
        }

        return $this->sendResponse($result);
    }

    public function publishBookAction() {

        try {
            $easyBook = $this->get('jazzyweb.dodoceasybookbridge');
            $result = $easyBook->publishBook();
        } catch (\Exception $e) {
            $result['json']['message'] = $e->getMessage();
            $result['status_code'] = 500;
        }

        return $this->sendResponse($result);
    }

    public function getEditionAction() {
        
        try {
            $easyBook = $this->get('jazzyweb.dodoceasybookbridge');
            list($result,$mime, $ext) = $easyBook->getEdition();
            $response = new Response();
            $response->headers->set('Content-Type', $mime);
            $response->headers->set('Content-Disposition', 'filename=book'. '.' . $ext);
            $response->setContent($result);
            return $response;
        } catch (\Exception $e) {
            $result['json']['message'] = $e->getMessage();
            $result['status_code'] = 500;
            
            return $this->sendResponse($result);
        }                        
    }

}

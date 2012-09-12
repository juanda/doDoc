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

        $bookName = $this->getRequest()->get('book');
        try {
            $easyBook = $this->get('jazzyweb.dodoceasybookbridge');
            $result = $easyBook->newBook($bookName);
        } catch (\Exception $e) {

            $result['json']['message'] = $e->getMessage();
            $result['status_code'] = 500;
        }

        return $this->sendResponse($result);
    }

    public function publishBookAction() {

        $slug = $this->getRequest()->get('slug');
        $edition = $this->getRequest()->get('edition');

        try {
            $easyBook = $this->get('jazzyweb.dodoceasybookbridge');
            $result = $easyBook->publishBook($slug, $edition);
        } catch (\Exception $e) {
            $result['json']['message'] = $e->getMessage();
            $result['status_code'] = 500;
        }

        return $this->sendResponse($result);
    }

    public function getEditionAction() {
        $slug = $this->getRequest()->get('slug');
        $edition = $this->getRequest()->get('edition');
        try {
            $easyBook = $this->get('jazzyweb.dodoceasybookbridge');
            list($result, $mime, $ext) = $easyBook->getEdition($slug, $edition);
            $response = new Response();
            $response->headers->set('Content-Type', $mime);
            $response->headers->set('Content-Disposition', 'filename=book' . '.' . $ext);
            $response->setContent($result);
            return $response;
        } catch (\Exception $e) {
            $result['json']['message'] = $e->getMessage();
            $result['status_code'] = 500;

            return $this->sendResponse($result);
        }
    }

}

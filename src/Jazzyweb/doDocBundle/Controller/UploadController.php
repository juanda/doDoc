<?php

namespace Jazzyweb\doDocBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class UploadController extends Controller {

    public function preAction() {
        header('Pragma: no-cache');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Content-Disposition: inline; filename="files.json"');
        header('X-Content-Type-Options: nosniff');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: OPTIONS, HEAD, GET, POST, PUT, DELETE');
        header('Access-Control-Allow-Headers: X-File-Name, X-File-Type, X-File-Size');
    }

    public function getAction() {
        $request = $this->getRequest();

        $bookcode = $request->get('book');
        $file = $request->get('file');

        $uploadHandler = $this->get('jazzyweb.dodocuploadhandler');

        $uploadHandler->setBook($bookcode);

//        echo '<pre>';print_r($uploadHandler->getOptions());exit;

        $info = $uploadHandler->get_file_object($file);
        $this->preAction();
        header('Content-type: application/json');
        echo json_encode($info);
        exit;
    }

    public function getAllAction() {
        $request = $this->getRequest();

        $bookcode = $request->get('book');

        $uploadHandler = $this->get('jazzyweb.dodocuploadhandler');

        $uploadHandler->setBook($bookcode);

        $info = $uploadHandler->get_file_objects();

        header('Content-type: application/json');
        $this->preAction();
        echo json_encode($info);
        exit;
    }

    public function deleteAction() {
        $request = $this->getRequest();

        $bookcode = $request->get('book');
        $file = $request->get('file');

        $uploadHandler = $this->get('jazzyweb.dodocuploadhandler');

        $uploadHandler->setBook($bookcode);

        $info = $uploadHandler->delete($file);

        header('Content-type: application/json');
        $this->preAction();
        echo json_encode($info);
        exit;
    }

    public function postAction() {
        $request = $this->getRequest();

        $bookcode = $request->get('book');

        $uploadHandler = $this->get('jazzyweb.dodocuploadhandler');

        $uploadHandler->setBook($bookcode);

        $uploadHandler->post();
        
        $this->preAction();

        exit;
    }

}

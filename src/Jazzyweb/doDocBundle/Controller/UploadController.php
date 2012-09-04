<?php

namespace Jazzyweb\doDocBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class UploadController extends Controller {

    public function afterAction() {

        header('Pragma: no-cache');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Content-Disposition: inline; filename="files.json"');
        header('X-Content-Type-Options: nosniff');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: OPTIONS, HEAD, GET, POST, PUT, DELETE');
        header('Access-Control-Allow-Headers: X-File-Name, X-File-Type, X-File-Size');
    }

    public function beforeAction() {
        $this->request = $this->getRequest();

        $bookcode = $this->request->get('book');

        $this->uploadHandler = $this->get('jazzyweb.dodocuploadhandler');

        $this->uploadHandler->setBook($bookcode);
    }

    public function getAction() {

        $this->beforeAction();

        $file = $this->request->get('file');

        $info = $this->uploadHandler->get_file_object($file);
        
        $this->afterAction();
        
        header('Content-type: application/json');
        echo json_encode($info);
        exit;
    }

    public function getAllAction() {

        $this->beforeAction();

        $info = $uploadHandler->get_file_objects();

        header('Content-type: application/json');
        
        $this->afterAction();
        echo json_encode($info);
        exit;
    }

    public function deleteAction() {
        $this->beforeAction();
        
        $file = $this->request->get('file');
        
        $info = $this->uploadHandler->delete($file);

        header('Content-type: application/json');
        
        $this->afterAction();
        echo json_encode($info);
        exit;
    }

    public function postAction() {
        
        $this->beforeAction();
        

        $info = $this->uploadHandler->post();

        $this->afterAction();
        
        header('Vary: Accept');
        $json = json_encode($info);
        $redirect = isset($_REQUEST['redirect']) ?
            stripslashes($_REQUEST['redirect']) : null;
        if ($redirect) {
            header('Location: '.sprintf($redirect, rawurlencode($json)));
            return;
        }
        if (isset($_SERVER['HTTP_ACCEPT']) &&
            (strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)) {
            header('Content-type: application/json');
        } else {
            header('Content-type: text/plain');
        }
        echo $json;

        exit;
    }

}

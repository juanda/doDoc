<?php

namespace Jazzyweb\doDocBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class UploadController extends Controller {

    public function afterAction($info) {

        $this->response->headers->set('Content-type', 'application/json');
        $this->response->headers->set('Pragma', 'no-cache');
        $this->response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate');
        $this->response->headers->set('Content-Disposition', 'inline; filename="files.json');
        $this->response->headers->set('X-Content-Type-Option', 'nosniff');
        $this->response->headers->set('Access-Control-Allow-Origin', '*');
        $this->response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELET');
        $this->response->headers->set('Access-Control-Allow-Headers', 'X-File-Name, X-File-Type, X-File-Size');

        $this->response->setContent(json_encode($info));
    }

    public function beforeAction() {
        $this->request = $this->getRequest();

        $this->response = new Response();

        $route = $this->request->get('_route');

        $images = (stripos($route, 'images'))? true : false;

        $dir = $this->request->get('dir');

        $this->uploadHandler = $this->get('jazzyweb.dodocuploadhandler');

        $this->uploadHandler->setBook($dir, $images);
    }

    public function getAction() {

        $this->beforeAction();

        $file = $this->request->get('file');

        $info = $this->uploadHandler->get_file_object($file);

        $this->afterAction($info);

        return $this->response;
    }

    public function getAllAction() {

        $this->beforeAction();

        $info = $this->uploadHandler->get_file_objects();

        $this->afterAction($info);

        return $this->response;
    }

    public function deleteAction() {
        $this->beforeAction();

        $file = $this->request->get('file');

        $info = $this->uploadHandler->delete($file);

        $this->afterAction($info);

        return $this->response;
    }

    public function postAction() {

        $this->beforeAction();

        $info = $this->uploadHandler->post();

        $this->response->headers->set('Vary', 'Accept');

        $json = json_encode($info);
        $redirect = isset($_REQUEST['redirect']) ?
                stripslashes($_REQUEST['redirect']) : null;
        if ($redirect) {
            $this->response->headers->set('Location', sprintf($redirect, rawurlencode($json)));

            return $this->response;
        }
        if (isset($_SERVER['HTTP_ACCEPT']) &&
                (strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)) {
            $this->response->headers->set('Content-type', 'application/json');
        } else {
            $this->response->headers->set('Content-type', 'text/plain');
        }

        $this->afterAction($info);

        return $this->response;
    }

    public function getImagesAction() {
        $this->images = true;

        $this->getAction();
    }

    public function getAllImagesAction() {
        $this->images = true;

        $this->getAllAction();
    }

    public function deleteImagesAction() {
        $this->images = true;

        $this->deleteAction();
    }

    public function postImagesAction() {
        $this->images = true;

        $this->postAction();
    }

}

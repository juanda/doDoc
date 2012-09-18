<?php

namespace Jazzyweb\doDocBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController as Controller;

class BookAdminController extends Controller {

    public function editionsAction(){
        
        return $this->render('JazzywebdoDocBundle:BookAdmin:editions.html.twig');
    }
}

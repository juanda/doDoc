<?php

namespace Jazzyweb\doDocBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController as Controller;

class BookAdminController extends Controller {

    public function editionsAction(){
        
        $request = $this->getRequest();
        $em = $this->getDoctrine();
        
        $id = $request->get('id');
        
        $book = $em->getRepository('JazzywebdoDocBundle:Book')->findOneById($id);
        return $this->render('JazzywebdoDocBundle:BookAdmin:editions.html.twig', array('object' => $book));
    }
}

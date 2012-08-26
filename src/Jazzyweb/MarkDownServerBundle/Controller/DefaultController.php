<?php

namespace Jazzyweb\MarkDownServerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{

    public function toHtmlAction()
    {
        $request = $this->getRequest();

        $data = $request->get('data');
        
        if ($data == '') {
            throw $this->createNotFoundException('No se ha enviado el parámetro 
                ``data`` con el documento a convertir');
        }
          
        $md = $this->container->get('jmd_server.markdown.service');
        
        return new Response($md->buildHtml($data));
    }
    
    public function pruebaPOSTAction()
    {
        echo '<pre>';
        print_r($this->getRequest());
        echo '</pre>';
        exit;
    }

}

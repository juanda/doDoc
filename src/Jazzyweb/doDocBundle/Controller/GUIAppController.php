<?php

namespace Jazzyweb\doDocBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class GUIAppController extends Controller {

    public function editorAction() {
        $request = $this->getRequest();
        $easybook = $this->get('jazzyweb.dodoceasybookbridge');

        $bookCode = $request->get('book');
        $bookName = $bookCode;

        $editionNames = $easybook->getEditionNames($bookCode);       
        
        $build_formats = $editionNames; //array('web', 'print', 'ebook');

        $download_formats = array('HTML', 'PDF');

        $params = array(
            'book_code' => $bookCode,
            'book_name' => $bookName,
            'build_formats' => $build_formats,
            'download_formats' => $download_formats,
        );

        return $this->render('JazzywebdoDocBundle:GUIApp:editor.html.twig', $params);
    }
    
    public function uploadToolAction(){
        
        $request = $this->getRequest();

        $bookCode = $request->get('book');
        $images   = $request->get('images');
                                
        return $this->render('JazzywebdoDocBundle:GUIApp:uploadtool.html.php',
                array('dir' => $bookCode, 'images' => $images));
    }
    
    public function bookManagerAction(){
        
        return $this->render('JazzywebdoDocBundle:GUIApp:bookManager.html.twig',
                array());
    }
    
}

?>

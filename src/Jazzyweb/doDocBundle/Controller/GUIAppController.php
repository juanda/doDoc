<?php

namespace Jazzyweb\doDocBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class GUIAppController extends Controller {

    public function editorAction() {
        $request = $this->getRequest();

        $bookCode = $request->get('book');
        $bookName = $bookCode;

        $build_formats = array('web', 'print', 'ebook');

        $download_formats = array('HTML', 'PDF');

        $params = array(
            'book_code' => $bookCode,
            'book_name' => $bookName,
            'build_formats' => $build_formats,
            'download_formats' => $download_formats,
        );

        return $this->render('JazzywebdoDocBundle:GUIApp:editor.html.twig', $params);
    }

}

?>

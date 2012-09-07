<?php

namespace Jazzyweb\doDocBundle\Controller;

use Easybook\DependencyInjection\Application;
use Easybook\Util\Validator;
use Easybook\Events\EasybookEvents as Events;
use Easybook\Events\BaseEvent;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class EasybookController extends Controller {

    public function newBookAction() {

        $request = $this->getRequest();

        $this->docService = $this->get('jazzyweb.dodocmanager');

        $this->app = new Application();

        $title = Validator::validateNonEmptyString(
                        'title', $request->get('book')
        );

        $dir = Validator::validateDirExistsAndWritable($this->docService->getDocDir());

        $slug = $this->app->get('slugger')->slugify($title);
        $bookDir = $dir . '/' . $slug;

        // check if `$bookDir` directory is available
        // if not, create a unique directory name appending a numeric suffix
        $i = 1;
        while (file_exists($bookDir)) {
            $bookDir = $dir . '/' . $slug . '-' . $i++;
        }        

        // create the skeleton of the new book
        // don't use mirror() method because git deletes empty directories
        $skeletonDir = $this->app['app.dir.skeletons'] . '/Book';
        $this->app->get('filesystem')->mkdir($bookDir . '/Contents');
        $this->app->get('filesystem')->copy(
                $skeletonDir . '/Contents/chapter1.md', $bookDir . '/Contents/chapter1.md'
        );
        $this->app->get('filesystem')->copy(
                $skeletonDir . '/Contents/chapter2.md', $bookDir . '/Contents/chapter2.md'
        );
        $this->app->get('filesystem')->mkdir($bookDir . '/Contents/images');
        $this->app->get('filesystem')->mkdir($bookDir . '/Output');
        $this->app->renderFile($skeletonDir, 'config.yml.twig', $bookDir . '/config.yml', array(
            'generator' => array(
                'name' => $this->app['app.name'],
                'version' => $this->app['app.version']
            ),
            'title' => $title,
        ));
        
        return new Response('book created in:'. $bookDir);
    }

}

<?php

namespace Jazzyweb\doDocBundle\Services;

use Easybook\DependencyInjection\Application;
use Easybook\Util\Toolkit;

class EasyBookBridge {

    protected $docHandler;
    protected $appEasyBook;

    public function __construct($docHandler) {
        $this->docHandler = $docHandler;

        $this->appEasyBook = new Application();

        $this->appEasyBook->set('configurator', new EasyBookConfigurator($this->appEasyBook));
        $this->appEasyBook->set('validator', new EasyBookValidator($this->appEasyBook));
    }

    public function newBook($bookTitle) {

        $title = EasyBookValidator::validateNonEmptyString(
                        'title', $bookTitle
        );

        $dir = EasyBookValidator::validateDirExistsAndWritable($this->docHandler->getDocDir());

        $slug = $this->appEasyBook->get('slugger')->slugify($title);
        $bookDir = $dir . '/' . $slug;

        // check if `$bookDir` directory is available
        // if not, create a unique directory name appending a numeric suffix
        $i = 1;
        while (file_exists($bookDir)) {
            $bookDir = $dir . '/' . $slug . '-' . $i++;
        }

        // create the skeleton of the new book
        // don't use mirror() method because git deletes empty directories
        $skeletonDir = $this->appEasyBook['app.dir.skeletons'] . '/Book';
        $this->appEasyBook->get('filesystem')->mkdir($bookDir . '/Contents');
        $this->appEasyBook->get('filesystem')->copy(
                $skeletonDir . '/Contents/chapter1.md', $bookDir . '/Contents/chapter1.md'
        );
        $this->appEasyBook->get('filesystem')->copy(
                $skeletonDir . '/Contents/chapter2.md', $bookDir . '/Contents/chapter2.md'
        );
        $this->appEasyBook->get('filesystem')->mkdir($bookDir . '/Contents/images');
        $this->appEasyBook->get('filesystem')->mkdir($bookDir . '/Output');
        $this->appEasyBook->renderFile($skeletonDir, 'config.yml.twig', $bookDir . '/config.yml', array(
            'generator' => array(
                'name' => $this->appEasyBook['app.name'],
                'version' => $this->appEasyBook['app.version']
            ),
            'title' => $title,
        ));


        $out['json']['message'] = "The book '" . $title . "' has been created. You can start writing it";
        $out['status_code'] = 200;

        return $out;
    }

    public function publishBook($slug, $edition) {

        $this->setParamsForPublish($slug, $edition);

        // 1-line magic publication!
        $this->appEasyBook->get('publisher')->publishBook();

        $out['json']['message'] = "The edition " . $this->appEasyBook->get('publishing.edition') . " of the book '" . $this->appEasyBook->get('publishing.book.slug') . "' has been created.";
        $out['status_code'] = 200;

        return $out;
    }

    public function getEdition($slug, $edition) {

        $this->setParamsForPublish($slug, $edition);

        $outputFormat = $this->appEasyBook->edition('format');

        $ext = '';
        $mime = '';
        switch (strtolower($outputFormat)) {
            case 'pdf':
                $ext = "pdf";
                $mime = "application/pdf";
                break;

            case 'html':

                $dir = $this->appEasyBook->get('publishing.dir.output')
                        . '/'
                        . $this->appEasyBook->get('publishing.edition');

                $bookZip = $dir . '/book.zip';

                if (file_exists($bookZip)) {
                    unlink($bookZip);
                }

                Toolkit::zip($dir, $bookZip);

                $ext = "zip";
                $mime = "application/zip";
                break;

//            case 'html_chunked':
//                return new HtmlChunkedPublisher($app);

            case 'epub':
            case 'epub2':
                $ext = "epub";
                $mime = "application/epub+zip";
                break;

            //case 'epub3':
            //    return new Epub3Publisher($app);

            default:
                throw new \Exception(sprintf(
                                'Unknown "%s" format for "%s" edition (allowed: "pdf", "html", "html_chunked", "epub", "epub2")', $outputFormat, $this->appEasyBook->get('publishing.edition')
                ));
        }

        $file = $this->appEasyBook->get('publishing.dir.output')
                . '/'
                . $this->appEasyBook->get('publishing.edition')
                . '/book'
                . '.'
                . $ext;

        if (file_exists($file)) {
            return array(file_get_contents($file), $mime, $ext);
        } else {
            throw new \Exception(sprintf("The file " . $file . "doesn't exist"));
        }
    }

    public function getEditionNames($slug) {
        
        $dir = $this->docHandler->getDocDir();

        $this->configurator = $this->appEasyBook->get('configurator');
        $this->validator = $this->appEasyBook->get('validator');
        
        // validate book dir and add some useful values to the app configuration
        $bookDir = $this->validator->validateBookDir($slug, $dir);
        
        $this->appEasyBook->set('publishing.dir.book', $bookDir);        
        $this->appEasyBook->set('publishing.book.slug', $slug);
                
        $this->configurator->loadBookConfiguration();
        
        $editions = $this->appEasyBook->book('editions');
        
        $editionNames = array();
        foreach ($editions as $k=>$e)
        {
            $editionNames[] = $k;
        }
        
        return $editionNames;
        
    }

    protected function setParamsForPublish($slug, $edition) {

        $dir = $this->docHandler->getDocDir();

        $this->configurator = $this->appEasyBook->get('configurator');
        $this->validator = $this->appEasyBook->get('validator');

        // validate book dir and add some useful values to the app configuration
        $bookDir = $this->validator->validateBookDir($slug, $dir);

        $this->appEasyBook->set('publishing.dir.book', $bookDir);
        $this->appEasyBook->set('publishing.dir.contents', $bookDir . '/Contents');
        $this->appEasyBook->set('publishing.dir.output', $bookDir . '/Output');
        $this->appEasyBook->set('publishing.dir.resources', $bookDir . '/Resources');
        $this->appEasyBook->set('publishing.dir.plugins', $bookDir . '/Resources/Plugins');
        $this->appEasyBook->set('publishing.dir.templates', $bookDir . '/Resources/Templates');
        $this->appEasyBook->set('publishing.book.slug', $slug);
        $this->appEasyBook->set('publishing.edition', $edition);

        // load book configuration
        $this->configurator->loadBookConfiguration();

        // validate edition slug and add some useful values to the app configuration
        $this->edition = $this->validator->validatePublishingEdition($this->appEasyBook->get('publishing.edition'));

        // load edition configuration (it also resolves possible edition inheritante)
        $this->configurator->loadEditionConfiguration();

        // resolve book+edition configuration
        $this->configurator->resolveConfiguration();
    }

}

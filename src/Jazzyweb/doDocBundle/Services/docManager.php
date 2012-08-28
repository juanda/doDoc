<?php

namespace Jazzyweb\doDocBundle\Services;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;

class docManager {

    protected $doctrine;
    protected $docDir;
    protected $contentsDirname;
    protected $outputDirname;
    protected $fileSystem;

    public function __construct($doctrine, $docDir, $contentsDirname, $outputDirname, $configFilename) {

        $this->doctrine = $doctrine;
        $this->docDir = $docDir;
        $this->contentsDirname = $contentsDirname;
        $this->outputDirname = $outputDirname;
        $this->configFilename = $configFilename;
        $this->fileSystem = new Filesystem();
        $this->finder = new Finder();
    }

    public function getAllDocuments($bookCode, $user = null) {

        // check if the ``$user`` is asigned to the ``$book``
        // if not return null

        $out = array();

        try {
            $iterator = $this->finder
                    ->files()
                    ->name('*')
                    ->in($this->docDir . '/' . $bookCode . '/' . $this->contentsDirname)
                    ->depth(0)
                    ->sortByName()
                    ->getIterator();
        } catch (\InvalidArgumentException $e) {

            $out['json']['message'] = "The book " . $bookCode . " does not exists";
            $out['error'] = true;
            $out['status_code'] = 404;
            return $out;
        }


        foreach ($iterator as $document) {
            $out['json']['documents'][] = $document->getFilename();
        }
        $out['status_code'] = 200;

        return $out;
    }

    public function getDocument($bookCode, $docName, $user = null) {

        // check if the ``$user`` is asigned to the ``$book``
        // if not return null

        $out = array();

        $bookPath = $this->docDir
                . '/' .
                $bookCode
                . '/' .
                $this->contentsDirname;

        $docPath = $bookPath . '/' . $docName;

        if (!file_exists($bookPath)) {
            $out['json']['message'] = "The book " . $bookCode . " does not exists";
            $out['error'] = true;
            $out['status_code'] = 404;
        } elseif (!file_exists($docPath)) { // No file found
            $out['json']['message'] = "The document " . $docName . " does not exists in book " . $bookCode;
            $out['error'] = true;
            $out['status_code'] = 404;
        } else {
            //$document = $iterator->current();            
            $content = file_get_contents($docPath);
            $out['json']['docname'] = $docName;
            $out['json']['content'] = $content;
            $out['status_code'] = 200;
        }

        return $out;
    }

    public function createDocument($bookCode, $docName, $user = null) {

        $out = array();

        $bookPath = $this->docDir
                . '/' .
                $bookCode
                . '/' .
                $this->contentsDirname;

        $docPath = $bookPath . '/' . $docName;

        if (!file_exists($bookPath)) {
            $out['json']['message'] = "The book " . $bookCode . " does not exists";
            $out['error'] = true;
            $out['status_code'] = 404;
        } elseif (file_exists($docPath)) { // the document exists
            $out['json']['message'] = "There is a document named " . $docName . "  in book " . $bookCode;
            $out['error'] = true;
            $out['status_code'] = 403;
        } else {

            try {
                $this->fileSystem->touch($docPath);
            } catch (\ErrorException $e) {
                $out['json']['message'] = $e->getMessage();
                $out['error'] = true;
                $out['status_code'] = 500;
                return $out;
            }
            $out['json']['docname'] = $docPath;
            $out['status_code'] = 200;
        }

        return $out;
    }

    public function saveDocument($bookCode, $docName, $content) {
        
        $out = array();

        $bookPath = $this->docDir
                . '/' .
                $bookCode
                . '/' .
                $this->contentsDirname;

        $docPath = $bookPath . '/' . $docName;

        if (!file_exists($bookPath)) {
            $out['json']['message'] = "The book " . $bookCode . " does not exists";
            $out['error'] = true;
            $out['status_code'] = 404;
        } elseif (file_exists($docPath)) { // the document exists
            $out['json']['message'] = "There is a document named " . $docName . "  in book " . $bookCode;
            $out['error'] = true;
            $out['status_code'] = 403;
        }
    }

}


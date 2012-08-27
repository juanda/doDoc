<?php

namespace Jazzyweb\doDocBundle\Services;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;

class docManager
{

    protected $doctrine;
    protected $docDir;
    protected $contentsDirname;
    protected $outputDirname;
    protected $fileSystem;

    public function __construct($doctrine, $docDir, $contentsDirname, $outputDirname, $configFilename)
    {

        $this->doctrine = $doctrine;
        $this->docDir = $docDir;
        $this->contentsDirname = $contentsDirname;
        $this->outputDirname = $outputDirname;
        $this->configFilename = $configFilename;
        $this->fileSystem = new Filesystem();
        $this->finder = new Finder();
    }

    public

    function getAllDocuments($book, $user = null)
    {
        // check if the ``$user`` is asigned to the ``$book``
        // if not return null

        $iterator = $this->finder
                        ->files()
                        ->name('*')
                        ->in($this->docDir . '/' . $book . '/' . $this->contentsDirname)
                        ->depth(0)
                        ->sortByName()->getIterator();

        $documents = array();
        
        foreach ($iterator as $document)
        {
           $documents[] = $document->getFilename();
        }

        return $documents;
    }

}

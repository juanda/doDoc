<?php

namespace Jazzyweb\doDocBundle\Services;

class docManager
{
    protected $doctrine;

    public function __construct($doctrine) {
        
        $this->doctrine = $doctrine;
    }
    
    public function getAllDocuments($book){
        
        $em = $this->doctrine->getEntityManager();
        
        $documents = $em->findByCode($book);
        
        return $documents;
    }
}

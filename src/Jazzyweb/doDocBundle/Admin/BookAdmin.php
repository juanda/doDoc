<?php

namespace Jazzyweb\doDocBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class BookAdmin extends Admin {

    protected $easyBookService;

    public function __construct($code, $class, $baseControllerName, $easyBookService) {
        parent::__construct($code, $class, $baseControllerName);
        $this->easyBookService = $easyBookService;
    }

    protected function configureFormFields(FormMapper $formMapper) {
        $formMapper
                ->add('name')
                ->add('description')

        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper) {
        $datagridMapper
                ->add('name')
        ;
    }

    protected function configureListFields(ListMapper $listMapper) {

        $listMapper
                ->addIdentifier('name')
                ->add('slug')
                ->add('description')               
                ->add('_action', null, array(
                    'actions' => array(
                        'delete' => array(),
                        'edit' => array(),
                        'writebook' => array('template' => 'JazzywebdoDocBundle:BookAdmin:list__action_writebook.html.twig')
                        )))
        ;
    }

    public function validate(ErrorElement $errorElement, $object) {
        $errorElement
                ->with('name')
                ->assertMaxLength(array('limit' => 255))
                ->end()
        ;
    }

    public function prePersist($object) {
        $slug = $this->easyBookService->createBook($object->getName());
        $object->setSlug($slug);
    }

    public function preUpdate($object) {
        $slugOld = $object->getSlug();

        $slugNew = $this->easyBookService->slugify($object->getName());


        $this->easyBookService->changeBookName($slugOld, $object->getName());
        $this->easyBookService->changeBookSlug($slugOld, $slugNew);


        $object->setSlug($slugNew);
    }
    
    public function preRemove($object) {
           $this->easyBookService->deleteBook($object->getSlug());    
    }

    protected function configureRoutes(RouteCollection $collection) {

        $collection->add('editions', $this->getRouterIdParameter() . '/editions');
    }

}
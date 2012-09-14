<?php

namespace Jazzyweb\doDocBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('slug')
            ->add('name')
            ->add('description')
            ->add('users')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Jazzyweb\doDocBundle\Entity\Book'
        ));
    }

    public function getName()
    {
        return 'jazzyweb_dodocbundle_booktype';
    }
}

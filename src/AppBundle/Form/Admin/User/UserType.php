<?php

namespace AppBundle\Form\Admin\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{

    public function buildForm( FormBuilderInterface $builder, array $options )
    {
        $builder
                ->add( 'username' )
                ->add( 'email' )
                ->add( 'enabled', 'checkbox' )
                ->add( 'locked', 'checkbox' )
                ->add( 'expired', 'checkbox' )
                ->add( 'credentialsExpired', 'checkbox' )
        ;
    }

    public function configureOptions( OptionsResolver $resolver )
    {
        $resolver->setDefaults( array(
            'data_class' => 'AppBundle\Entity\User'
        ) );
    }

    public function getName()
    {
        return 'user';
    }

}

<?php

namespace AppBundle\Form\Admin\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class UserType extends AbstractType
{

    public function buildForm( FormBuilderInterface $builder, array $data )
    {
        $builder
        ->add( 'id', HiddenType::class )
        ->add( 'username', TextType::class )
        ->add( 'email', TextType::class )
        ->add( 'enabled', CheckboxType::class )
        ->add( 'locked', CheckboxType::class );
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

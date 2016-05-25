<?php

namespace AppBundle\Form\Common;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType As SymfonyEmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class AppEmailType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm( FormBuilderInterface $builder, array $options )
    {
        $builder
                ->add( 'type', EntityType::class, [
                    'class' => 'AppBundle:EmailType',
                    'choice_label' => 'type',
                    'multiple' => false,
                    'expanded' => false,
                    'required' => true,
                    'label' => false,
                    'choice_translation_domain' => false
                ] )
                ->add( 'email', SymfonyEmailType::class, [ 
                ] )
                ->add( 'comment', TextType::class )
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions( OptionsResolver $resolver )
    {
        $resolver->setDefaults( array(
            'data_class' => 'AppBundle\Entity\Email'
        ) );
    }

    public function getName()
    {
        return 'email';
    }

}
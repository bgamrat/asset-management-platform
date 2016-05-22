<?php

namespace AppBundle\Form\Common;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ContactType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm( FormBuilderInterface $builder, array $options )
    {
        $builder
                ->add( 'type', EntityType::class, [
                    'class' => 'AppBundle:ContactType',
                    'choice_label' => 'type',
                    'multiple' => false,
                    'expanded' => false,
                    'required' => true,
                    'label' => 'common.type',
                    'choice_translation_domain' => false
                ] )
                ->add( 'person', PersonType::class, array(
                    'data_class' => 'AppBundle\Entity\Person',
                    'by_reference' => true,
                    'required' => false,
                    'label' => false,
                    'empty_data' => null
                ) )
                ->add( 'email', TextType::class, ['label' => 'common.email'] )
                ->add( 'comment', TextType::class )
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions( OptionsResolver $resolver )
    {
        $resolver->setDefaults( array(
            'data_class' => 'AppBundle\Entity\Contact'
        ) );
    }

}

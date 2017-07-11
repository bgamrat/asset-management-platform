<?php

namespace AppBundle\Form\Common;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CustomAttributeType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm( FormBuilderInterface $builder, array $options )
    {
        $builder
                ->add( 'key', TextType::class, [ 'label' => 'common.key'] )
                ->add( 'value', TextType::class, ['label' => 'common.value'] )
        ;
    }

    public function configureOptions( OptionsResolver $resolver )
    {
        $resolver->setDefaults( array(
            'data_class' => 'AppBundle\Entity\CustomAttribute',
            'by_reference' => false
        ) );
    }
     
    public function getName()
    {
        return 'custom_attributes';
    }

}

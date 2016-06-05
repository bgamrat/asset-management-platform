<?php

namespace AppBundle\Form\Admin\Asset;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use AppBundle\Form\Common\PersonType;
use AppBundle\Form\Admin\Asset\BrandType;

class ManufacturerType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm( FormBuilderInterface $builder, array $options )
    {
        $builder
                ->add( 'name', TextType::class, ['label' => 'common.name'] )
                ->add( 'active', CheckboxType::class, ['label' => 'common.active'] )
                ->add( 'comment', TextareaType::class, [
                    'required' => false
                ]  )
                ->add( 'person', CollectionType::class, [
                    'entry_type' => PersonType::class,
                    'by_reference' => true,
                    'required' => false,
                    'label' => false,
                    'empty_data' => null,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'delete_empty' => true,
                    'mapped' => false,
                    'prototype_name' => '__person__'
                ] )
                ->add( 'brands', CollectionType::class, [
                    'entry_type' => BrandType::class,
                    'by_reference' => true,
                    'required' => false,
                    'label' => false,
                    'empty_data' => null,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'delete_empty' => true,
                    'mapped' => false,
                    'prototype_name' => '__brand__'
                ] )
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions( OptionsResolver $resolver )
    {
        $resolver->setDefaults( array(
            'data_class' => 'AppBundle\Entity\Manufacturer'
        ) );
    }

}

<?php

namespace AppBundle\Form\Admin\Venue;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use AppBundle\Form\Common\Type\PersonType;
use AppBundle\Form\Common\AddressType;

class VenueType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm( FormBuilderInterface $builder, array $options )
    {
        $builder
                ->add( 'id', HiddenType::class )
                ->add( 'name', TextType::class, ['label' => 'common.name'] )
                ->add( 'address', AddressType::class, [
                    'by_reference' => false,
                    'required' => true,
                    'label' => false
                ] )
                ->add( 'directions', TextareaType::class, [
                    'label' => false,
                    'required' => false
                ] )
                ->add( 'parking', TextareaType::class, [
                    'label' => false,
                    'required' => false
                ] )
                ->add( 'active', CheckboxType::class, ['label' => 'common.active'] )
                ->add( 'comment', TextareaType::class, [
                    'label' => false,
                    'required' => false
                ] )
                ->add( 'contacts', CollectionType::class, [
                    'entry_type' => PersonType::class,
                    'by_reference' => false,
                    'required' => false,
                    'label' => false,
                    'empty_data' => null,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'delete_empty' => true,
                    'prototype_name' => '__person__'
                ] )
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions( OptionsResolver $resolver )
    {
        $resolver->setDefaults( array(
            'data_class' => 'AppBundle\Entity\Venue\Venue'
        ) );
    }

    public function getName()
    {
        return 'venue';
    }

}

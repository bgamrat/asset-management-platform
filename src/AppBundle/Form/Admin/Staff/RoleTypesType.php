<?php

namespace AppBundle\Form\Admin\Staff;

use AppBundle\Form\Admin\Staff\RoleTypeType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RoleTypesType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm( FormBuilderInterface $builder, array $options )
    {
        $builder
                ->add( 'types', CollectionType::class, [
                    'entry_type' => RoleTypeType::class,
                    'entry_options' => [
                        'required' => false,
                        'empty_data' => null
                    ],
                    'by_reference' => false,
                    'required' => false,
                    'label' => false,
                    'empty_data' => null,
                    'allow_add' => true,
                    'allow_delete' => false,
                    'delete_empty' => true,
                    'prototype_name' => '__type__'
                ] )
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions( OptionsResolver $resolver )
    {
        return;
        $resolver->setDefaults( array(
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id' => 'statuses',
        ) );
    }

    public function getName()
    {
        return 'role_types';
    }

}

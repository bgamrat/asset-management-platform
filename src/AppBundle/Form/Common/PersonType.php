<?php

namespace AppBundle\Form\Common;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use AppBundle\Form\Common\PhoneNumberType;
use AppBundle\Form\Common\AppEmailType; // Named to avoid conflicts with Symfony EmailType
use AppBundle\Form\Common\AddressType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class PersonType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm( FormBuilderInterface $builder, array $options )
    {
        $builder
                ->add( 'id', HiddenType::class, ['label' => false] )
                ->add( 'type', EntityType::class, [
                    'class' => 'AppBundle\Entity\Common\PersonType',
                    'choice_label' => 'type',
                    'multiple' => false,
                    'expanded' => false,
                    'required' => true,
                    'label' => 'common.type',
                    'choice_translation_domain' => false
                ] )
                ->add( 'firstname', TextType::class, ['label' => 'person.firstname'] )
                ->add( 'middlename', TextType::class, ['label' => 'person.middlename'] )
                ->add( 'lastname', TextType::class, ['label' => 'person.lastname'] )
                ->add( 'phones', CollectionType::class, [
                    'entry_type' => PhoneNumberType::class,
                    'by_reference' => false,
                    'required' => false,
                    'label' => false,
                    'empty_data' => null,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'delete_empty' => true,
                    'prototype_name' => '__phone__'
                ] )
                ->add( 'emails', CollectionType::class, [
                    'label' => 'common.email',
                    'entry_type' => AppEmailType::class,
                    'by_reference' => false,
                    'required' => false,
                    'label' => false,
                    'empty_data' => null,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'delete_empty' => true,
                    'prototype_name' => '__email__'
                ] )
                ->add( 'addresses', CollectionType::class, [
                    'entry_type' => AddressType::class,
                    'by_reference' => false,
                    'required' => false,
                    'label' => false,
                    'empty_data' => null,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'delete_empty' => true,
                    'prototype_name' => '__address__'
                ] )
                ->add( 'comment', TextareaType::class, [
                    'required' => false,
                    'label' => false
                ] );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions( OptionsResolver $resolver )
    {
        $resolver->setDefaults( array(
            'data_class' => 'AppBundle\Entity\Common\Person',
            'allow_extra_fields' => true
        ) );
    }

    public function getName()
    {
        return 'person';
    }

}

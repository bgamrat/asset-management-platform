<?php

namespace AppBundle\Form\Common;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
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
                ->add( 'type', EntityType::class, [
                    'class' => 'AppBundle:PersonType',
                    'choice_label' => 'type',
                    'multiple' => false,
                    'expanded' => false,
                    'required' => true,
                    'label' => 'common.type',
                    'choice_translation_domain' => false
                ] )
                ->add( 'firstname', TextType::class, ['label' => 'person.firstname'] )
                ->add( 'middleinitial', TextType::class, ['label' => 'person.middleinitial'] )
                ->add( 'lastname', TextType::class, ['label' => 'person.lastname'] )
                ->add( 'phone_numbers', CollectionType::class, [
                    'entry_type' => PhoneNumberType::class,
                    'by_reference' => true,
                    'required' => false,
                    'label' => false,
                    'empty_data' => null,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'delete_empty' => true,
                    'mapped' => false,
                    'prototype_name' => '__phone__'
                ] )
                ->add( 'emails', CollectionType::class, [
                    'label' => 'common.email',
                    'entry_type' => AppEmailType::class,
                    'by_reference' => true,
                    'required' => false,
                    'label' => false,
                    'empty_data' => null,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'delete_empty' => true,
                    'mapped' => false,
                    'prototype_name' => '__email__'
                    ] )
                ->add( 'addresses', CollectionType::class, [
                    'entry_type' => AddressType::class,
                    'by_reference' => true,
                    'required' => false,
                    'label' => false,
                    'empty_data' => null,
                    'allow_add' => true,
                    'allow_delete' => false,
                    'delete_empty' => true,
                    'mapped' => false,
                    'prototype_name' => '__address__'
                ] )
                ->add( 'comment', TextareaType::class, [
                    'required' => false
                ] );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions( OptionsResolver $resolver )
    {
        $resolver->setDefaults( array(
            'data_class' => 'AppBundle\Entity\Person'
        ) );
    }

    public function getName()
    {
        return 'person';
    }

}

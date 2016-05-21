<?php

namespace AppBundle\Form\Common;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use AppBundle\Form\Common\PhoneNumbersType;
use AppBundle\Form\Common\AddressType;

class PersonType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm( FormBuilderInterface $builder, array $options )
    {
        $builder
                ->add( 'firstname', TextType::class, ['label' => 'person.firstname'] )
                ->add( 'lastname', TextType::class, ['label' => 'person.lastname'] )
                ->add( 'middleinitial', TextType::class, ['label' => 'person.middleinitial'] )
                ->add( 'address', CollectionType::class, [
                    'entry_type' => AddressType::class,
                    'by_reference' => true,
                    'required' => false,
                    'label' => false,
                    'empty_data' => null,
                    'allow_add' => true,
                    'allow_delete' => false,
                    'delete_empty' => true,
                    'mapped' => false
                ] )
                ->add( 'phone_numbers', CollectionType::class, [
                    'entry_type' => PhoneNumbersType::class,
                    'by_reference' => true,
                    'required' => false,
                    'label' => false,
                    'empty_data' => null,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'delete_empty' => true,
                    'mapped' => false
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

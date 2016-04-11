<?php

namespace AppBundle\Form\Common;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

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
                ->add( 'middleinitial', TextType::class, ['label' => 'person.middleinitial'] );
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

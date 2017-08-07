<?php

namespace AppBundle\Form\Admin\Asset;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LocationTypeType extends AbstractType
{

    private $entities;

    public function __construct( $entities )
    {
        $this->entities = [];
        foreach ($entities as $e => $ent) {
            $this->entities[ucfirst($e)] = $e;
        }
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm( FormBuilderInterface $builder, array $options )
    {
        $builder
                ->add( 'id', HiddenType::class )
                ->add( 'name', TextType::class )
                ->add( 'entity', ChoiceType::class, ['choices' => $this->entities]
                )
                ->add( 'url', TextType::class )
                ->add( 'active', CheckboxType::class )
                ->add( 'default', CheckBoxType::class );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions( OptionsResolver $resolver )
    {
        $resolver->setDefaults( ['label' => false,
            'data_class' => 'AppBundle\Entity\Asset\LocationType'
        ] );
    }

    public function getName()
    {
        return 'location_type';
    }

}

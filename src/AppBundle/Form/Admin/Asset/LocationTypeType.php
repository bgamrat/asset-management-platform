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

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm( FormBuilderInterface $builder, array $options )
    {
        $builder
                ->add( 'id', HiddenType::class )
                ->add( 'name', TextType::class )
                ->add( 'entity', ChoiceType::class, ['choices' =>
                    ["Asset" => 'asset', "Client" => "client", "Contact" => "contact", "Manufacturer" => 'manufacturer',
                        "Other" => 'other', "Shop" => 'shop', "Trailer" => 'trailer', "Vendor" => 'vendor',
                        "Venue" => 'venue']
                ] )
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

<?php

namespace AppBundle\Form\Admin\Asset;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TransferStatusType extends AbstractType
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
                ->add( 'comment', TextType::class )
                ->add( 'none', CheckboxType::class, [ 'mapped' => false ] )
                ->add( 'in_transit', CheckboxType::class )
                ->add( 'location_destination', CheckboxType::class )
                ->add( 'location_unknown', CheckboxType::class )
                ->add( 'active', CheckboxType::class )
                ->add( 'default', CheckBoxType::class )
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions( OptionsResolver $resolver )
    {
        $resolver->setDefaults( ['label' => false,
            'data_class' => 'AppBundle\Entity\Asset\TransferStatus'
        ] );
    }

    public function getName()
    {
        return 'transfer_status';
    }

}

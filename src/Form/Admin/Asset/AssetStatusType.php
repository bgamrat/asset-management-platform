<?php

Namespace App\Form\Admin\Asset;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AssetStatusType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm( FormBuilderInterface $builder, array $options )
    {
        $builder->add( 'default', CheckBoxType::class )
                ->add( 'id', HiddenType::class )
                ->add( 'name', TextType::class )
                ->add( 'comment', TextType::class )
                ->add( 'available', CheckboxType::class )
                ->add( 'in_use', CheckboxType::class )

        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions( OptionsResolver $resolver )
    {
        $resolver->setDefaults( ['label' => false,
            'data_class' => 'App\Entity\Asset\AssetStatus'
        ] );
    }

    public function getName()
    {
        return 'asset_status';
    }

}

<?php

Namespace App\Form\Admin\Asset;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class BrandSelectType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm( FormBuilderInterface $builder, array $options )
    {
        $builder
                ->add( 'brand', EntityType::class, [
                    'class' => 'App\Entity\Asset\Brand',
                    'choice_label' => 'name',
                    'multiple' => false,
                    'expanded' => false,
                    'required' => false,
                    'empty_data' => null,
                    'label' => 'asset.brand',
                    'choice_translation_domain' => false
                ] );
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions( OptionsResolver $resolver )
    {
        $resolver->setDefaults( array(
            'data_class' => 'App\Entity\Asset\Brand'
        ) );
    }

    public function getName()
    {
        return 'brand';
    }

}

<?php

namespace AppBundle\Form\Admin\Asset;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityManager;
use AppBundle\Form\Admin\Asset\DataTransformer\ModelToIdTransformer;

class AssetType extends AbstractType
{

    private $em;

    public function __construct( EntityManager $em )
    {
        $this->em = $em;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm( FormBuilderInterface $builder, array $options )
    {
        $builder
                ->add( 'id', HiddenType::class, ['label' => false] )
                ->add( 'serial_number', TextType::class, ['label' => false] )
                ->add( 'model', TextType::class, [
                    'label' => 'common.model'
                ] )
                ->add( 'status', EntityType::class, [
                    'class' => 'AppBundle:AssetStatus',
                    'choice_label' => 'name',
                    'multiple' => false,
                    'expanded' => false,
                    'required' => true,
                    'label' => 'asset.status',
                    'preferred_choices' => function($status, $key, $index)
                    {
                        return $status->isActive();
                    },
                    'choice_translation_domain' => false
                ] )
                ->add( 'location', EntityType::class, [
                    'class' => 'AppBundle:Location',
                    'choice_label' => 'name',
                    'multiple' => false,
                    'expanded' => false,
                    'required' => true,
                    'label' => 'asset.location',
                    'choice_translation_domain' => false
                ] )
                ->add( 'barcodes', CollectionType::class, [
                    'label' => 'asset.barcode',
                    'entry_type' => BarcodeType::class,
                    'by_reference' => true,
                    'required' => false,
                    'label' => false,
                    'empty_data' => null,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'delete_empty' => true,
                    'prototype_name' => '__barcode__'
                ] )
                ->add( 'comment', TextType::class, [
                    'label' => false
                ] )
                ->add( 'active', CheckboxType::class, ['label' => 'common.active'] )
        ;
        $builder->get( 'model' )
                ->addModelTransformer( new ModelToIdTransformer( $this->em ) );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions( OptionsResolver $resolver )
    {
        $resolver->setDefaults( array(
            'data_class' => 'AppBundle\Entity\Asset'
        ) );
    }

    public function getName()
    {
        return 'asset';
    }

}
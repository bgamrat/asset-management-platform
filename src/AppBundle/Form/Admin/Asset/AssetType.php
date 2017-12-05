<?php

namespace AppBundle\Form\Admin\Asset;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityManager;
use AppBundle\Form\Admin\Asset\DataTransformer\ModelToIdTransformer;
use AppBundle\Form\Admin\Asset\DataTransformer\VendorToIdTransformer;
use AppBundle\Form\Admin\Asset\AssetLocationType;
use AppBundle\Form\Common\CustomAttributeType;
use Symfony\Component\Validator\Constraints\Valid;

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
        $defaultStatus = $this->em->getRepository( 'AppBundle\Entity\Asset\AssetStatus' )->findOneBy( ['default' => true] );
        $builder
                ->add( 'id', HiddenType::class, ['label' => false] )
                ->add( 'serial_number', TextType::class, ['label' => false] )
                ->add( 'model', TextType::class, [
                    'label' => 'common.model'
                ] )
                ->add( 'status', EntityType::class, [
                    'class' => 'AppBundle\Entity\Asset\AssetStatus',
                    'choice_label' => 'name',
                    'multiple' => false,
                    'expanded' => false,
                    'required' => true,
                    'label' => 'asset.status',
                    'preferred_choices' => function($status, $key, $index)
                    {
                        return $status->isActive();
                    },
                    'data' => $this->em->getReference( 'AppBundle\Entity\Asset\AssetStatus', $defaultStatus->getId() ),
                    'choice_translation_domain' => false
                ] )
                ->add( 'purchased', DateType::class, [
                    'label' => 'common.purchased',
                    'widget' => 'single_text',
                    'format' => 'yyyy-MM-dd',
                    'required' => false
                ] )
                ->add( 'cost', MoneyType::class, ['label' => 'common.cost', 'currency' => 'USD'] )
                ->add( 'value', MoneyType::class, ['label' => 'common.value', 'currency' => 'USD'] )
                ->add( 'owner', TextType::class, [
                    'label' => 'asset.owner'
                ] )
                ->add( 'location', AssetLocationType::class )
                ->add( 'location_text', HiddenType::class )
                ->add( 'barcodes', CollectionType::class, [
                    'entry_type' => BarcodeType::class,
                    'by_reference' => false,
                    'required' => false,
                    'label' => false,
                    'empty_data' => null,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'delete_empty' => true,
                    'prototype_name' => '__barcode__'
                ] )
                ->add( 'custom_attributes', CollectionType::class, [
                    'entry_type' => CustomAttributeType::class,
                    'required' => false,
                    'label' => false,
                    'empty_data' => null,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'delete_empty' => true,
                    'by_reference' => false,
                    'property_path' => 'customAttributes',
                    'constraints' => [new Valid()]
                ] )
                ->add( 'comment', TextType::class, [
                    'label' => false
                ] )
                ->add( 'active', CheckboxType::class, ['label' => 'common.active'] )
        ;
        $builder->get( 'model' )
                ->addModelTransformer( new ModelToIdTransformer( $this->em ) );
        $builder->get( 'owner' )
                ->addModelTransformer( new VendorToIdTransformer( $this->em ) );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions( OptionsResolver $resolver )
    {
        $resolver->setDefaults( array(
            'data_class' => 'AppBundle\Entity\Asset\Asset'
        ) );
    }

    public function getName()
    {
        return 'asset';
    }

}

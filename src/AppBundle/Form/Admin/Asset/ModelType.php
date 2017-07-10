<?php

namespace AppBundle\Form\Admin\Asset;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use AppBundle\Form\Admin\Asset\Type\ModelRelationshipType;
use AppBundle\Form\Admin\Asset\Type\CategoryType;
use AppBundle\Form\Admin\Asset\CustomAttributeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityManager;
use AppBundle\Form\Admin\Asset\DataTransformer\CategoryToIdTransformer;

class ModelType extends AbstractType
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
                ->add( 'id', HiddenType::class, ['required' => false, 'mapped' => false] )
                ->add( 'category', TextType::class, [
                    'label' => 'asset.category'
                ] )
                ->add( 'name', TextType::class, [
                    'label' => false, 'required' => true] )
                ->add( 'container', CheckboxType::class, [
                    'label' => 'asset.container'] )
                ->add( 'weight', NumberType::class, [
                    'label' => false, 'required' => false] )
                ->add( 'custom_attributes', CollectionType::class, [
                    'entry_type' => CustomAttributeType::class,
                    'required' => false,
                    'label' => false,
                    'empty_data' => null,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'delete_empty' => true,
                    'by_reference' => false,
                    'property_path' => 'customAttributes'
                ] )
                ->add( 'default_contract_value', MoneyType::class, ['label' => 'asset.default_contract_value', 'currency' => 'USD'] )
                ->add( 'default_event_value', MoneyType::class, ['label' => 'asset.default_event_value', 'currency' => 'USD'] )
                ->add( 'carnet_value', MoneyType::class, ['label' => 'asset.carnet_value', 'currency' => 'USD'] )
                ->add( 'comment', TextType::class, [
                    'label' => false, 'required' => false
                ] )
                ->add( 'active', CheckboxType::class, ['label' => 'common.active'] )
                ->add( 'requires', CollectionType::class, [
                    'entry_type' => ModelRelationshipType::class,
                    'required' => false,
                    'label' => false,
                    'empty_data' => null,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'delete_empty' => true
                ] )
                ->add( 'required_by', CollectionType::class, [
                    'entry_type' => ModelRelationshipType::class,
                    'required' => false,
                    'label' => false,
                    'empty_data' => null,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'delete_empty' => true,
                    'property_path' => 'requiredBy'
                ] )
                ->add( 'extends', CollectionType::class, [
                    'entry_type' => ModelRelationshipType::class,
                    'required' => false,
                    'label' => false,
                    'empty_data' => null,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'delete_empty' => true
                ] )
                ->add( 'extended_by', CollectionType::class, [
                    'entry_type' => ModelRelationshipType::class,
                    'required' => false,
                    'label' => false,
                    'empty_data' => null,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'delete_empty' => true,
                    'property_path' => 'extendedBy'
                ] )
                ->add( 'satisfies', CollectionType::class, [
                    'label' => 'common.satisfies',
                    'entry_type' => CategoryType::class,
                    'required' => false,
                    'empty_data' => null,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'delete_empty' => true,
                    'prototype_name' => '__satisfies__'
                ] );
        $builder->get( 'category' )
                ->addModelTransformer( new CategoryToIdTransformer( $this->em ) );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions( OptionsResolver $resolver )
    {
        $resolver->setDefaults( array(
            'data_class' => 'AppBundle\Entity\Asset\Model'
        ) );
    }

    public function getName()
    {
        return 'model';
    }

}

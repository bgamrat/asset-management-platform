<?php

namespace AppBundle\Form\Admin\Client;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use AppBundle\Form\Admin\Client\CategoryQuantityType;
use AppBundle\Form\Admin\Client\TrailerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContractType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm( FormBuilderInterface $builder, array $options )
    {
        $builder
                ->add( 'id', HiddenType::class )
                ->add( 'name', TextType::class, [
                    'label' => 'common.name'] )
                ->add( 'comment', TextType::class, [
                    'label' => false
                ] )
                ->add( 'active', CheckboxType::class, ['label' => 'common.active'] )
                ->add( 'container', CheckboxType::class, [
                    'label' => 'asset.container'] )
                ->add( 'start', DateType::class, [
                    'label' => 'common.start',
                    'widget' => 'single_text',
                    'format' => 'yyyy-MM-dd',
                    'required' => false
                ] )
                ->add( 'end', DateType::class, [
                    'label' => 'common.end',
                    'widget' => 'single_text',
                    'format' => 'yyyy-MM-dd',
                    'required' => false
                ] )
                ->add( 'value', MoneyType::class, ['label' => 'common.value', 'currency' => 'USD'] )
                ->add( 'requires_trailers', CollectionType::class, [
                    'entry_type' => TrailerType::class,
                    'property_path' => 'requiresTrailers',
                    'required' => false,
                    'label' => false,
                    'empty_data' => null,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'delete_empty' => true
                ] )
                ->add( 'available_trailers', CollectionType::class, [
                    'entry_type' => TrailerType::class,
                    'property_path' => 'availableTrailers',
                    'required' => false,
                    'label' => false,
                    'empty_data' => null,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'delete_empty' => true
                ] )
                ->add( 'requires_category_quantities', CollectionType::class, [
                    'entry_type' => CategoryQuantityType::class,
                    'property_path' => 'requiresCategoryQuantities',
                    'required' => false,
                    'label' => false,
                    'empty_data' => null,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'delete_empty' => true
                ] )
                ->add( 'available_category_quantities', CollectionType::class, [
                    'entry_type' => CategoryQuantityType::class,
                    'property_path' => 'availableCategoryQuantities',
                    'required' => false,
                    'label' => false,
                    'empty_data' => null,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'delete_empty' => true
                ] )
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions( OptionsResolver $resolver )
    {
        $resolver->setDefaults( array(
            'allow_extra_fields' => true,
            'data_class' => 'AppBundle\Entity\Client\Contract'
        ) );
    }

    public function getName()
    {
        return 'contract';
    }

}

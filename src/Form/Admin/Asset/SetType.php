<?php

Namespace App\Form\Admin\Asset;

use App\Form\Admin\Asset\Type\CategoryType;
use App\Form\Admin\Asset\Type\ModelType;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SetType extends AbstractType
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
                ->add( 'value', MoneyType::class, ['currency' => 'USD'] )
                ->add( 'comment', TextType::class )
                ->add( 'in_use', CheckboxType::class )
                ->add( 'satisfies', CollectionType::class, [
                    'entry_type' => CategoryType::class,
                    'required' => false,
                    'label' => false,
                    'empty_data' => null,
                    'allow_add' => true,
                    'allow_delete' => false,
                    'delete_empty' => true,
                    'prototype_name' => '__category__'
                ] )
                ->add( 'models', CollectionType::class, [
                    'entry_type' => ModelType::class,
                    'required' => false,
                    'label' => false,
                    'empty_data' => null,
                    'allow_add' => true,
                    'allow_delete' => false,
                    'delete_empty' => true,
                    'prototype_name' => '__model__'
                ] )
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions( OptionsResolver $resolver )
    {
        return;
        $resolver->setDefaults( array(
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id' => 'categories',
        ) );
    }

    public function getName()
    {
        return 'categories';
    }

}

<?php

namespace AppBundle\Form\Admin\Asset;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use AppBundle\Form\Common\Type\PersonType;
use Doctrine\ORM\EntityManager;
use AppBundle\Form\Admin\Asset\DataTransformer\BrandsToIdsTransformer;

class VendorType extends AbstractType
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
                ->add( 'id', HiddenType::class )
                ->add( 'name', TextType::class, ['label' => 'common.name'] )
                ->add( 'active', CheckboxType::class, ['label' => 'common.active'] )
                ->add( 'comment', TextareaType::class, [
                    'label' => false,
                    'required' => false
                ] )
                ->add( 'contacts', CollectionType::class, [
                    'entry_type' => PersonType::class,
                    'by_reference' => false,
                    'required' => false,
                    'label' => false,
                    'empty_data' => null,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'delete_empty' => true,
                    'prototype_name' => '__person__'
                ] )
                ->add( 'brands', CollectionType::class, [
                    'entry_type' => TextType::class,
                    'required' => false,
                    'label' => false,
                    'empty_data' => null,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'delete_empty' => true,
                    'prototype_name' => '__brand__'
                ] )
                ->add( 'rma_required', CheckboxType::class, ['label' => 'asset.rma_required'] )
                ->add( 'service_instructions', TextareaType::class, [
                    'label' => false,
                    'required' => false,
                    'property_path' => 'serviceInstructions'
                ] )
        ;
        $builder->get( 'brands' )
                ->addModelTransformer( new BrandsToIdsTransformer( $this->em ) );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions( OptionsResolver $resolver )
    {
        $resolver->setDefaults( array(
            'data_class' => 'AppBundle\Entity\Asset\Vendor'
        ) );
    }

    public function getName()
    {
        return 'vendor';
    }

}

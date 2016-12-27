<?php

namespace AppBundle\Form\Admin\Asset;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityManager;
use AppBundle\Form\Admin\Asset\DataTransformer\ModelToIdTransformer;
use AppBundle\Form\Admin\Asset\DataTransformer\TrailerRelationshipsToIdsTransformer;
use AppBundle\Form\Admin\Asset\AssetLocationType;

class TrailerType extends AbstractType
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
                ->add( 'name', TextType::class, ['label' => false] )
                ->add( 'serial_number', TextType::class, ['label' => false] )
                ->add( 'model', TextType::class, [
                    'label' => 'asset.model'
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
                    'choice_translation_domain' => false
                ] )
                ->add( 'purchased', DateType::class, [
                    'label' => 'common.purchased', 
                    'widget' => 'single_text',
                    'format' => 'yyyy-MM-dd',
                    'input' => 'string',
                    'required' => false
                ] )
                ->add( 'cost', MoneyType::class, ['label' => 'common.cost', 'currency' => 'USD', 'required' => false] )
                ->add( 'location', AssetLocationType::class )
                ->add( 'location_text', HiddenType::class )
                ->add( 'comment', TextType::class, [
                    'label' => false
                ] )
                ->add( 'active', CheckboxType::class, ['label' => 'common.active'] )
                ->add( 'requires', CollectionType::class, [
                    'entry_type' => EntityType::class,
                    'entry_options' => [ 'class' => 'AppBundle\Entity\Asset\Trailer',
                        'choice_label' => false],
                    'by_reference' => false,
                    'required' => false,
                    'label' => false,
                    'empty_data' => null,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'delete_empty' => true
                ] )
                ->add( 'required_by', CollectionType::class, [
                    'entry_type' => EntityType::class,
                    'entry_options' => [ 'class' => 'AppBundle\Entity\Asset\Trailer',
                        'choice_label' => false],
                    'by_reference' => false,
                    'required' => false,
                    'label' => false,
                    'empty_data' => null,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'delete_empty' => true,
                    'property_path' => 'requiredBy'
                ] )
                ->add( 'extends', CollectionType::class, [
                    'entry_type' => EntityType::class,
                    'entry_options' => [ 'class' => 'AppBundle\Entity\Asset\Trailer',
                        'choice_label' => false],
                    'by_reference' => false,
                    'required' => false,
                    'label' => false,
                    'empty_data' => null,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'delete_empty' => true
                ] )
                ->add( 'extended_by', CollectionType::class, [
                    'entry_type' => EntityType::class,
                    'entry_options' => [ 'class' => 'AppBundle\Entity\Asset\Trailer',
                        'choice_label' => false],
                    'by_reference' => false,
                    'required' => false,
                    'label' => false,
                    'empty_data' => null,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'delete_empty' => true,
                    'property_path' => 'extendedBy'
                ] );
        $builder->get( 'requires' )
                ->addModelTransformer( new TrailerRelationshipsToIdsTransformer( $this->em ) );
        $builder->get( 'required_by' )
                ->addModelTransformer( new TrailerRelationshipsToIdsTransformer( $this->em ) );
        $builder->get( 'extends' )
                ->addModelTransformer( new TrailerRelationshipsToIdsTransformer( $this->em ) );
        $builder->get( 'extended_by' )
                ->addModelTransformer( new TrailerRelationshipsToIdsTransformer( $this->em ) );

        $builder->get( 'model' )
                ->addModelTransformer( new ModelToIdTransformer( $this->em ) );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions( OptionsResolver $resolver )
    {
        $resolver->setDefaults( array(
            'data_class' => 'AppBundle\Entity\Asset\Trailer'
        ) );
    }

    public function getName()
    {
        return 'trailer';
    }

}

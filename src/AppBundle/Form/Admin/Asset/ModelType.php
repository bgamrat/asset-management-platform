<?php

namespace AppBundle\Form\Admin\Asset;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityManager;
use AppBundle\Form\Admin\Asset\DataTransformer\ModelRelationshipsToIdsTransformer;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

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
                ->add( 'category', EntityType::class, [
                    'class' => 'AppBundle:Category',
                    'choice_label' => 'name',
                    'multiple' => false,
                    'expanded' => false,
                    'required' => true,
                    'label' => 'asset.category',
                    'preferred_choices' => function($category, $key, $index)
                    {
                        return $category->isActive();
                    },
                    'choice_translation_domain' => false
                ] )
                ->add( 'name', TextType::class, [
                    'label' => false, 'required' => true] )
                ->add( 'comment', TextType::class, [
                    'label' => false, 'required' => false
                ] )
                ->add( 'active', CheckboxType::class, ['label' => 'common.active'] )
                ->add( 'requires', CollectionType::class, [
                    'entry_type' => EntityType::class,
                    'entry_options' => [ 'class' => 'AppBundle:Model',
                    'choice_label' =>false],
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
                    'entry_options' => [ 'class' => 'AppBundle:Model',
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
                    'entry_options' => [ 'class' => 'AppBundle:Model',
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
                    'entry_options' => [ 'class' => 'AppBundle:Model',
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
                ->addModelTransformer( new ModelRelationshipsToIdsTransformer( $this->em ) );
        $builder->get( 'required_by' )
                ->addModelTransformer( new ModelRelationshipsToIdsTransformer( $this->em ) );
        $builder->get( 'extends' )
                ->addModelTransformer( new ModelRelationshipsToIdsTransformer( $this->em ) );
        $builder->get( 'extended_by' )
                ->addModelTransformer( new ModelRelationshipsToIdsTransformer( $this->em ) );

        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions( OptionsResolver $resolver )
    {
        $resolver->setDefaults( array(
            'data_class' => 'AppBundle\Entity\Model'
        ) );
    }

    public function getName()
    {
        return 'model';
    }

}

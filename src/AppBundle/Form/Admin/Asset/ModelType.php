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
use AppBundle\Form\Admin\Asset\DataTransformer\ModelsToIdsTransformer;

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
                ->add( 'id', HiddenType::class, ['required' => false] )
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
                    'entry_type' => TextType::class,
                    'by_reference' => false,
                    'required' => false,
                    'label' => false,
                    'empty_data' => null,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'delete_empty' => true
                ] )
                ->add( 'supports', CollectionType::class, [
                    'entry_type' => TextType::class,
                    'by_reference' => false,
                    'required' => false,
                    'label' => false,
                    'empty_data' => null,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'delete_empty' => true
                ] )
                ->add( 'extends', CollectionType::class, [
                    'entry_type' => TextType::class,
                    'by_reference' => false,
                    'required' => false,
                    'label' => false,
                    'empty_data' => null,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'delete_empty' => true
                ] );
        $builder->get( 'requires' )
                ->addModelTransformer( new ModelsToIdsTransformer( $this->em ) );
        $builder->get( 'supports' )
                ->addModelTransformer( new ModelsToIdsTransformer( $this->em ) );
        $builder->get( 'extends' )
                ->addModelTransformer( new ModelsToIdsTransformer( $this->em ) );
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

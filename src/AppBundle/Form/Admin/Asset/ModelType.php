<?php

namespace AppBundle\Form\Admin\Asset;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ModelType extends AbstractType
{

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

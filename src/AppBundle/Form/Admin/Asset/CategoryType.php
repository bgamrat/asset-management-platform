<?php

namespace AppBundle\Form\Admin\Asset;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class CategoryType extends AbstractType
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
                ->add( 'position', IntegerType::class )
                ->add( 'parent', EntityType::class, [
                    'class' => 'AppBundle\Entity\Asset\Category',
                    'choice_label' => 'name',
                    'multiple' => false,
                    'expanded' => false,
                    'required' => false,
                    'empty_data' => null,
                    'label' => 'common.parent',
                    'choice_translation_domain' => false
                ] )
                ->add( 'value', MoneyType::class, ['currency' => 'USD'] )
                ->add( 'comment', TextType::class )
                ->add( 'in_use', CheckboxType::class )
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions( OptionsResolver $resolver )
    {
        $resolver->setDefaults( ['label' => false,
            'data_class' => 'AppBundle\Entity\Asset\Category'
        ] );
    }

    public function getName()
    {
        return 'category';
    }

}

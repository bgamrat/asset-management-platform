<?php

Namespace App\Form\Admin\Asset\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Form\Admin\Asset\DataTransformer\CategoryToIdTransformer;

class CategoryType extends AbstractType
{

    private $categoryToIdTransformer;

    public function __construct( CategoryToIdTransformer $categoryToIdTransformer )
    {
        $this->categoryToIdTransformer = $categoryToIdTransformer;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm( FormBuilderInterface $builder, array $options )
    {

        $builder
                ->addModelTransformer( $this->categoryToIdTransformer );
    }

    public function configureOptions( OptionsResolver $resolver )
    {
        
    }

    public function getParent()
    {
        return TextType::class;
    }

}

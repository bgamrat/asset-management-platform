<?php

Namespace App\Form\Admin\Asset\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Form\Admin\Asset\DataTransformer\ModelToIdTransformer;

class ModelRelationshipType extends AbstractType
{

    private $modelToIdTransformer;

    public function __construct( ModelToIdTransformer $modelToIdTransformer )
    {
        $this->modelToIdTransformer = $modelToIdTransformer;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm( FormBuilderInterface $builder, array $options )
    {

        $builder
                ->addModelTransformer( $this->modelToIdTransformer );
    }

    public function configureOptions( OptionsResolver $resolver )
    {
        
    }

    public function getParent()
    {
        return TextType::class;
    }

}

<?php

Namespace App\Form\Admin\Schedule\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Form\Admin\Client\DataTransformer\TrailerToIdTransformer;

class TrailerType extends AbstractType
{

    private $trailerToIdTransformer;

    public function __construct( TrailerToIdTransformer $trailerToIdTransformer )
    {
        $this->trailerToIdTransformer = $trailerToIdTransformer;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm( FormBuilderInterface $builder, array $options )
    {

        $builder
                ->addModelTransformer( $this->trailerToIdTransformer );
    }

    public function configureOptions( OptionsResolver $resolver )
    {
        
    }

    public function getParent()
    {
        return TextType::class;
    }

}

<?php

Namespace App\Form\Common\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Form\Common\DataTransformer\PersonToIdTransformer;

class PersonType extends AbstractType
{

    private $personToIdTransformer;

    public function __construct( PersonToIdTransformer $personToIdTransformer )
    {
        $this->personToIdTransformer = $personToIdTransformer;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm( FormBuilderInterface $builder, array $options )
    {

        $builder
                ->addModelTransformer( $this->personToIdTransformer );
    }

    public function configureOptions( OptionsResolver $resolver )
    {
        $resolver->setDefaults( [
            'label' => false
        ] );
    }

    public function getParent()
    {
        return TextType::class;
    }

}

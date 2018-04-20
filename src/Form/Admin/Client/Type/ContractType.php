<?php

Namespace App\Form\Admin\Client\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Form\Admin\Client\DataTransformer\ContractToIdTransformer;

class ContractType extends AbstractType
{

    private $contractToIdTransformer;

    public function __construct( ContractToIdTransformer $contractToIdTransformer )
    {
        $this->contractToIdTransformer = $contractToIdTransformer;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm( FormBuilderInterface $builder, array $options )
    {

        $builder
                ->addModelTransformer( $this->contractToIdTransformer );
    }

    public function configureOptions( OptionsResolver $resolver )
    {
        
    }

    public function getParent()
    {
        return TextType::class;
    }

}

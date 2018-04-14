<?php

Namespace App\Form\Admin\Common;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TestType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm( FormBuilderInterface $builder, array $options )
    {
        $builder
                ->add( 'id', HiddenType::class )
                ->add( 'text', TextType::class, ['label' => 'common.event'] )
                ->add( 'money', MoneyType::class, [
                    'label' => 'common.amount',
                    'currency' => 'USD',
                    'invalid_message' => 'Invalid value',
                    'attr' => ['data-invalid-message' => 'Invalid']
                    ] )
                ->add( 'integer', IntegerType::class, [
                    'label' => false
                ] )
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions( OptionsResolver $resolver )
    {
        $resolver->setDefaults( array(
            'data_class' => null
        ) );
    }

    public function getName()
    {
        return 'test';
    }

}

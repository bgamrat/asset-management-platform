<?php

Namespace App\Form\Admin\Client;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityManager;
use App\Form\Admin\Client\DataTransformer\CategoryToIdTransformer;

class CategoryQuantityType extends AbstractType
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
               // ->add( 'id', HiddenType::class, ['label' => false] )
                ->add( 'category', TextType::class, [
                    'label' => 'asset.category'
                ] )
                ->add( 'quantity', IntegerType::class, ['label' => 'common.quantity'] )
                ->add( 'value', MoneyType::class, ['label' => 'common.value', 'currency' => 'USD'] )
                ->add( 'comment', TextType::class, [
                    'label' => false
                ] )
        ;
        $builder->get( 'category' )
                ->addModelTransformer( new CategoryToIdTransformer( $this->em ) );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions( OptionsResolver $resolver )
    {
        $resolver->setDefaults( array(
            'data_class' => 'App\Entity\Common\CategoryQuantity'
        ) );
    }

    public function getName()
    {
        return 'categoryquantity';
    }

}

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
use App\Form\Admin\Client\DataTransformer\TrailerToIdTransformer;

class TrailerType extends AbstractType
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
                ->add( 'id', HiddenType::class, ['label' => false] )
                ->add( 'trailer', TextType::class, [ 'label' => 'asset.trailer'] )
                ->add( 'value', MoneyType::class, ['label' => 'common.value', 'currency' => 'USD'] )
                ->add( 'comment', TextType::class, [
                    'label' => false
                ] )
        ;
        $builder->get( 'trailer' )
              ->addModelTransformer( new TrailerToIdTransformer( $this->em ) );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions( OptionsResolver $resolver )
    {
        $resolver->setDefaults( array(
            'data_class' => 'App\Entity\Client\Trailer'
        ) );
    }

    public function getName()
    {
        return 'trailer';
    }

}

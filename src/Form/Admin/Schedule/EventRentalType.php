<?php

Namespace App\Form\Admin\Schedule;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityManager;
use App\Form\Admin\Asset\DataTransformer\CategoryToIdTransformer;
use App\Form\Admin\Asset\DataTransformer\VendorToIdTransformer;


class EventRentalType extends AbstractType
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
                ->add( 'category', TextType::class, ['label' => false ] )
                ->add( 'vendor', TextType::class, ['label' => false ] )
                ->add( 'quantity', IntegerType::class, ['label' => false] )
                ->add( 'cost', MoneyType::class, ['label' => 'common.cost', 'currency' => 'USD'] )
                ->add( 'comment', TextType::class, [
                    'label' => false
                ] )
        ;
        $builder->get( 'category' )
                ->addModelTransformer( new CategoryToIdTransformer( $this->em ) );
        $builder->get( 'vendor' )
                ->addModelTransformer( new VendorToIdTransformer( $this->em ) );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions( OptionsResolver $resolver )
    {
        $resolver->setDefaults( array(
            'data_class' => 'App\Entity\Schedule\EventRental'
        ) );
    }

    public function getName()
    {
        return 'event_rental';
    }

}

<?php

namespace AppBundle\Form\Admin\Client;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityManager;
use AppBundle\Form\Admin\Client\DataTransformer\ClientToIdTransformer;
use AppBundle\Form\Admin\Schedule\DataTransformer\EventToIdTransformer;

class BillToType extends AbstractType
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
                ->add( 'client', TextType::class, ['label' => false] )
                ->add( 'event', TextType::class, ['label' => 'common.event'] )
                ->add( 'amount', MoneyType::class, ['label' => 'common.amount', 'currency' => 'USD'] )
                ->add( 'comment', TextType::class, [
                    'label' => false
                ] )
        ;
        $builder->get( 'client' )
                ->addModelTransformer( new ClientToIdTransformer( $this->em ) );
        $builder->get( 'event' )
                ->addModelTransformer( new EventToIdTransformer( $this->em ) );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions( OptionsResolver $resolver )
    {
        $resolver->setDefaults( array(
            'data_class' => 'AppBundle\Entity\Client\BillTo'
        ) );
    }

    public function getName()
    {
        return 'transfer_item';
    }

}

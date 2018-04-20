<?php

Namespace App\Form\Admin\Common;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityManager;
use App\Form\Admin\Common\ContactType;
use App\Form\Admin\Schedule\DataTransformer\EventToIdTransformer;

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
                //->add( 'id', HiddenType::class )
                ->add( 'contact', ContactType::class )
                ->add( 'event', TextType::class, ['label' => 'common.event'] )
                ->add( 'amount', MoneyType::class, ['label' => 'common.amount', 'currency' => 'USD'] )
                ->add( 'comment', TextType::class, [
                    'label' => false
                ] )
        ;
        $builder->get( 'event' )
                ->addModelTransformer( new EventToIdTransformer( $this->em ) );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions( OptionsResolver $resolver )
    {
        $resolver->setDefaults( array(
            'data_class' => 'App\Entity\Common\BillTo'
        ) );
    }

    public function getName()
    {
        return 'bill_to';
    }

}

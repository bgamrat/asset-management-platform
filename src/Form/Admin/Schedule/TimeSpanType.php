<?php

Namespace App\Form\Admin\Schedule;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityManager;
use App\Form\Admin\Schedule\DataTransformer\TimeSpanTypeToIdTransformer;

class TimeSpanType extends AbstractType
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
                ->add( 'type', TextType::class, [
                    'label' => 'common.type'] )
                ->add( 'start', DateTimeType::class, [
                    'label' => 'common.start',
                    'widget' => 'single_text',
                    'required' => false
                ] )
                ->add( 'end', DateTimeType::class, [
                    'label' => 'common.end',
                    'widget' => 'single_text',
                    'required' => false
                ] )
                ->add( 'comment', TextType::class, [
                    'label' => false
                ] )

        ;
        $builder->get( 'type' )
                ->addModelTransformer( new TimeSpanTypeToIdTransformer( $this->em ) );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions( OptionsResolver $resolver )
    {
        $resolver->setDefaults( array(
            'data_class' => 'App\Entity\Schedule\TimeSpan'
        ) );
    }

    public function getName()
    {
        return 'time_span';
    }

}

<?php

namespace AppBundle\Form\Admin\Asset;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityManager;
use AppBundle\Form\Admin\Asset\DataTransformer\ModelToIdTransformer;
use AppBundle\Form\Admin\Asset\AssetLocationType;

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
                ->add( 'name', TextType::class, ['label' => false] )
                ->add( 'serial_number', TextType::class, ['label' => false] )
                ->add( 'model', TextType::class, [
                    'label' => 'asset.model'
                ] )
                ->add( 'status', EntityType::class, [
                    'class' => 'AppBundle\Entity\Asset\AssetStatus',
                    'choice_label' => 'name',
                    'multiple' => false,
                    'expanded' => false,
                    'required' => true,
                    'label' => 'asset.status',
                    'preferred_choices' => function($status, $key, $index)
                    {
                        return $status->isActive();
                    },
                    'choice_translation_domain' => false
                ] )
                ->add( 'purchased', DateType::class, ['label' => 'common.purchased'] )
                ->add( 'cost', MoneyType::class, ['label' => 'common.cost', 'currency' => 'USD'] )
                ->add( 'value', MoneyType::class, ['label' => 'common.value', 'currency' => 'USD'] )
                ->add( 'location', AssetLocationType::class )
                ->add( 'location_text', HiddenType::class )
                ->add( 'comment', TextType::class, [
                    'label' => false
                ] )
                ->add( 'active', CheckboxType::class, ['label' => 'common.active'] )
        ;
        $builder->get( 'model' )
                ->addModelTransformer( new ModelToIdTransformer( $this->em ) );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions( OptionsResolver $resolver )
    {
        $resolver->setDefaults( array(
            'data_class' => 'AppBundle\Entity\Asset\Trailer'
        ) );
    }

    public function getName()
    {
        return 'trailer';
    }

}
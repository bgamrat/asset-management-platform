<?php

namespace AppBundle\Form\Admin\Asset;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityManager;
use AppBundle\Form\Admin\Asset\DataTransformer\BarcodeIdToAssetTransformer;

class IssueItemType extends AbstractType
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
                ->add( 'item', TextType::class, ['label' => false, 'property_path' => 'asset'] )
                ->add( 'status', EntityType::class, [
                    'class' => 'AppBundle\Entity\Asset\AssetStatus',
                    'choice_label' => 'name',
                    'multiple' => false,
                    'expanded' => false,
                    'required' => true,
                    'label' => false,
                    'preferred_choices' => function($status, $key, $index)
                    {
                        return $status->isActive();
                    },
                    'choice_translation_domain' => false,
                    'mapped' => false
                ] )
                ->add( 'comment', TextType::class, [
                    'label' => false
                ] )
        ;
        $builder->get( 'item' )
                ->addModelTransformer( new BarcodeIdToAssetTransformer( $this->em ) );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions( OptionsResolver $resolver )
    {
        $resolver->setDefaults( array(
            'data_class' => 'AppBundle\Entity\Asset\IssueItem'
        ) );
    }

    public function getName()
    {
        return 'issue_item';
    }

}

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
use AppBundle\Form\Common\DataTransformer\PersonToIdTransformer;

class IssueType extends AbstractType
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
        $defaultStatus = $this->em->getRepository( 'AppBundle\Entity\Asset\IssueStatus' )->findOneBy( ['default' => true] );
        $builder
                ->add( 'id', HiddenType::class, ['label' => false] )
                ->add( 'priority', IntegerType::class, ['label' => 'issue.priority'] )
                ->add( 'title', TextType::class, ['label' => false] )
                ->add( 'description', TextType::class, [
                    'label' => false
                ] )
                ->add( 'barcodes', CollectionType::class, [
                    'entry_type' => BarcodeType::class,
                    'by_reference' => false,
                    'required' => false,
                    'label' => false,
                    'empty_data' => null,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'delete_empty' => true,
                    'prototype_name' => '__barcode__'
                ] )
                ->add( 'issue_status', EntityType::class, [
                    'class' => 'AppBundle\Entity\Asset\IssueStatus',
                    'choice_label' => 'status',
                    'multiple' => false,
                    'expanded' => false,
                    'required' => true,
                    'label' => 'asset.status',
                    'preferred_choices' => function($status, $key, $index)
                    {
                        return $status->isActive();
                    },
                    'data' => $this->em->getReference( 'AppBundle\Entity\Asset\IssueStatus', $defaultStatus->getId() ),
                    'choice_translation_domain' => false
                ] )
                ->add( 'cost', MoneyType::class, ['label' => 'common.cost', 'currency' => 'USD'] )
                ->add( 'assigned_to', TextType::class )
                ->add( 'client_billable', CheckboxType::class, ['label' => 'common.client_billable'] )
        ;
        $builder->get( 'assigned_to' )
                ->addModelTransformer( new PersonToIdTransformer( $this->em ) );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions( OptionsResolver $resolver )
    {
        $resolver->setDefaults( array(
            'data_class' => 'AppBundle\Entity\Asset\Issue'
        ) );
    }

    public function getName()
    {
        return 'issue';
    }

}

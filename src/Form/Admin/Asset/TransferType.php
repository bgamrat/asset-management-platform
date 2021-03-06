<?php

Namespace App\Form\Admin\Asset;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityManager;
use App\Form\Common\DataTransformer\PersonToIdTransformer;
use App\Form\Admin\Common\BillToType;
use App\Form\Admin\Asset\DataTransformer\CarrierServiceToIdTransformer;

class TransferType extends AbstractType
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
        $defaultStatus = $this->em->getRepository( 'App\Entity\Asset\TransferStatus' )->findOneBy( ['default' => true] );
        $builder
                ->add( 'id', HiddenType::class, ['label' => false] )
                ->add( 'created_at', DateTimeType::class, [
                    'label' => 'common.created',
                    'widget' => 'single_text',
                    'format' => 'yyyy-MM-dd HH:mm:ss',
                    'required' => false,
                    'disabled' => true
                ] )
                ->add( 'updated_at', DateTimeType::class, [
                    'label' => 'common.updated',
                    'widget' => 'single_text',
                    'format' => 'yyyy-MM-dd HH:mm:ss',
                    'required' => false,
                    'disabled' => true
                ] )
                ->add( 'status', EntityType::class, [
                    'class' => 'App\Entity\Asset\TransferStatus',
                    'choice_label' => 'name',
                    'multiple' => false,
                    'expanded' => false,
                    'required' => true,
                    'label' => 'issue.status',
                    'preferred_choices' => function($status, $key, $index)
                    {
                        return $status->isInUse();
                    },
                    'data' => $defaultStatus !== null ? $this->em->getReference( 'App\Entity\Asset\TransferStatus', $defaultStatus->getId() ) : '',
                    'choice_translation_domain' => false
                ] )
                ->add( 'cost', MoneyType::class, [
                    'label' => 'common.cost', 'currency' => 'USD'] )
                ->add( 'from', TextType::class, [
                    'label' => 'common.from'] )
                ->add( 'source_location', AssetLocationType::class, [
                    'property_path' => 'sourceLocation'] )
                ->add( 'source_location_text', HiddenType::class, [
                    'property_path' => 'sourceLocationText'] )
                ->add( 'to', TextType::class, ['label' => 'common.to'] )
                ->add( 'destination_location', AssetLocationType::class, [
                    'property_path' => 'destinationLocation'] )
                ->add( 'destination_location_text', HiddenType::class, [
                    'property_path' => 'destinationLocationText'] )
                ->add( 'carrier', EntityType::class, [
                    'class' => 'App\Entity\Asset\Carrier',
                    'choice_label' => 'name',
                    'multiple' => false,
                    'expanded' => false,
                    'required' => true,
                    'label' => 'common.carrier',
                    'preferred_choices' => function($carrier, $key, $index)
                    {
                        return $carrier->isActive();
                    },
                    'choice_translation_domain' => false
                ] )
                ->add( 'carrier_service', TextType::class, ['label' => 'common.service', 'property_path' => 'carrierService'] )
                ->add( 'tracking_number', TextType::class, ['label' => 'common.from', 'property_path' => 'trackingNumber'] )
                ->add( 'instructions', TextType::class, ['label' => false] )
                ->add( 'items', CollectionType::class, [
                    'entry_type' => TransferItemType::class,
                    'by_reference' => false,
                    'required' => false,
                    'label' => false,
                    'empty_data' => null,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'delete_empty' => true,
                    'prototype_name' => '__item__'
                ] )
                ->add( 'bill_tos', CollectionType::class, [
                    'entry_type' => BillToType::class,
                    'by_reference' => false,
                    'required' => false,
                    'label' => false,
                    'empty_data' => null,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'delete_empty' => true,
                    'prototype_name' => '__bill_to__',
                    'property_path' => 'billTos'
                ] )
        ;
        $builder->get( 'from' )
                ->addModelTransformer( new PersonToIdTransformer( $this->em ) );
        $builder->get( 'to' )
                ->addModelTransformer( new PersonToIdTransformer( $this->em ) );
        $builder->get( 'carrier_service' )
                ->addModelTransformer( new CarrierServiceToIdTransformer( $this->em ) );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions( OptionsResolver $resolver )
    {
        $resolver->setDefaults( array(
            'data_class' => 'App\Entity\Asset\Transfer'
        ) );
    }

    public function getName()
    {
        return 'transfer';
    }

}

<?php

Namespace App\Form\Admin\Schedule;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use App\Form\Admin\Client\CategoryQuantityType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityManager;
use App\Form\Admin\Client\DataTransformer\ClientToIdTransformer;
use App\Form\Admin\Venue\DataTransformer\VenueToIdTransformer;
use App\Form\Admin\Schedule\Type\TrailerType;
use App\Form\Admin\Client\Type\ContractType;
use App\Form\Common\Type\PersonType;

class EventType extends AbstractType
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
                ->add( 'id', HiddenType::class )
                ->add( 'name', TextType::class, [
                    'label' => 'common.name'] )
                ->add( 'description', TextType::class, [
                    'label' => false
                ] )
                ->add( 'start', DateType::class, [
                    'label' => 'common.start',
                    'widget' => 'single_text',
                    'format' => 'yyyy-MM-dd',
                    'required' => false
                ] )
                ->add( 'end', DateType::class, [
                    'label' => 'common.end',
                    'widget' => 'single_text',
                    'format' => 'yyyy-MM-dd',
                    'required' => false
                ] )
                ->add( 'tentative', CheckboxType::class, ['label' => 'event.tentative'] )
                ->add( 'billable', CheckboxType::class, ['label' => 'event.billable'] )
                ->add( 'canceled', CheckboxType::class, ['label' => 'event.canceled'] )
                ->add( 'contacts', CollectionType::class, [
                    'entry_type' => PersonType::class,
                    'required' => false,
                    'label' => false,
                    'empty_data' => null,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'delete_empty' => true,
                    'prototype_name' => '__person__'
                ] )
                ->add( 'client', TextType::class, [
                    'label' => 'common.client'
                ] )
                ->add( 'venue', TextType::class, [
                    'label' => 'common.venue'
                ] )
                ->add( 'contracts', CollectionType::class, [
                    'entry_type' => ContractType::class,
                    'required' => false,
                    'label' => false,
                    'empty_data' => null,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'delete_empty' => true,
                    'prototype_name' => '__contract__'
                ] )
                ->add( 'trailers', CollectionType::class, [
                    'entry_type' => TrailerType::class,
                    'required' => false,
                    'label' => false,
                    'empty_data' => null,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'delete_empty' => true,
                    'prototype_name' => '__trailer__'
                ] )
                ->add( 'category_quantities', CollectionType::class, [
                    'entry_type' => CategoryQuantityType::class,
                    'required' => false,
                    'label' => false,
                    'empty_data' => null,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'delete_empty' => true
                ] )
                ->add( 'time_spans', CollectionType::class, [
                    'entry_type' => TimeSpanType::class,
                    'required' => false,
                    'label' => false,
                    'empty_data' => null,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'delete_empty' => true,
                    'prototype_name' => '__time_span__'
                ] )
                ->add( 'roles', CollectionType::class, [
                    'entry_type' => EventRoleType::class,
                    'required' => false,
                    'label' => false,
                    'empty_data' => null,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'delete_empty' => true,
                    'prototype_name' => '__role__'
                ] )
                ->add( 'rentals', CollectionType::class, [
                    'entry_type' => EventRentalType::class,
                    'by_reference' => false,
                    'required' => false,
                    'label' => false,
                    'empty_data' => null,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'delete_empty' => true,
                    'prototype_name' => '__rental__'
                ] )
                ->add( 'client_equipment', CollectionType::class, [
                    'entry_type' => ClientEquipmentType::class,
                    'by_reference' => false,
                    'required' => false,
                    'label' => false,
                    'empty_data' => null,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'delete_empty' => true,
                    'prototype_name' => '__client_equipment__'
                ] )
        ;

        $builder->get( 'client' )
                ->addModelTransformer( new ClientToIdTransformer( $this->em ) );
        $builder->get( 'venue' )
                ->addModelTransformer( new VenueToIdTransformer( $this->em ) );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions( OptionsResolver $resolver )
    {
        $resolver->setDefaults( array(
            'data_class' => 'App\Entity\Schedule\Event'
        ) );
    }

    public function getName()
    {
        return 'event';
    }

}

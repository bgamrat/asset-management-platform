<?php

namespace AppBundle\Form\Admin\Asset;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use AppBundle\Form\Admin\Asset\DataTransformer\LocationTypeToIdTransformer;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormEvent;
use AppBundle\Entity\Common\Contact;

class AssetLocationType extends AbstractType
{

    private $em, $entities;

    public function __construct( EntityManager $em, $entities )
    {
        $this->em = $em;
        $this->entities = $entities;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm( FormBuilderInterface $builder, array $options )
    {
        $defaultLocationType = $this->em->getRepository( 'AppBundle\Entity\Asset\LocationType' )->findOneBy( ['default' => true] );

        $builder
                ->add( 'id', HiddenType::class )
                ->add( 'ctype', EntityType::class, [
                    'class' => 'AppBundle\Entity\Asset\LocationType',
                    'choice_label' => 'name',
                    'choice_attr' => function($val, $key, $index)
                    {
                        if( $val->getUrl() !== '' )
                        {
                            return ['data-url' => $val->getUrl(), 'data-type' => strtolower( $val->getName() )];
                        }
                        else
                        {
                            return ['data-type' => strtolower( $val->getName() )];
                        }
                    },
                            'data' => $this->em->getReference( 'AppBundle\Entity\Asset\LocationType', $defaultLocationType->getId() ),
                            'multiple' => false,
                            'expanded' => true,
                            'required' => true,
                            'label' => 'asset.location_type',
                            'property_path' => 'type',
                            'choice_translation_domain' => false,
                            'mapped' => false
                        ] )
                        ->add( 'type', HiddenType::class )
                        ->add( 'entity', IntegerType::class )
                        ->add( 'person_id', HiddenType::class, [ 'mapped' => false] )
                        ->add( 'address_id', HiddenType::class, ['mapped' => false] )
                        ->add( 'address', CheckBoxType::class, ['label' => 'common.address', 'mapped' => false] )
                ;
                $builder->get( 'type' )
                        ->addModelTransformer( new LocationTypeToIdTransformer( $this->em ) );
                $builder->addEventListener( FormEvents::POST_SET_DATA, function (FormEvent $event)
                {
                    $location = $event->getData();
                    $form = $event->getForm();
                    $class = null;
                    $entityId = null;
                    if( !empty( $location ) )
                    {
                        $entityId = $location->getEntity();
                        if( !empty( $entityId ) )
                        {
                            if( isset( $this->entities[$location->getType()->getEntity()] ) )
                            {
                                $class = $this->entities[$location->getType()->getEntity()];
                            }
                        }
                    }

                    if( $class !== null && $entityId !== null )
                    {
                        if( $location->isAddress() )
                        {
                            $addressId = $location->getAddressId();
                            $address = $this->em->getRepository( 'AppBundle\Entity\Common\Address' )->find( $addressId );
                            $contactData = $this->em->getRepository( $class )->findOneByAddress( $address->getId() );
                            $data = $this->em->getReference( $class, $contactData->getId() );
                        }
                        else
                        {
                            $addressId = null;
                            $data = $this->em->getReference( $class, $entityId );
                        }
                        $location->setEntityData( $data );
                        $form->add( 'entity_data', EntityType::class, [
                            'class' => $class, 'data' => $data, 'attr' => [ 'class' => 'hidden' ]]
                        );
                    }
                    else
                    {
                        $form->add( 'entity_data', HiddenType::class, ['data' => null] );
                    }
                } );
                $builder->addEventListener( FormEvents::SUBMIT, function (FormEvent $event)
                {
                    $location = $event->getData();
                    $form = $event->getForm();

                    $class = null;
                    $entityId = null;
                    if( !empty( $location ) )
                    {

                        $entityId = $location->getEntity();
                        $locationType = $location->getType();
                        if( !empty( $entityId ) )
                        {
                            if( isset( $this->entities[$locationType->getEntity()] ) )
                            {
                                $class = $this->entities[$locationType->getEntity()];
                            }
                        }
                    }

                    if( $class !== null && $entityId !== null )
                    {
                        if( !empty($form->get( 'address_id' )->getData()) )
                        {
                            $addressId = $form->get( 'address_id' )->getData();
                            $contactData = $this->em->getRepository( $class )->findOneByAddress($addressId);
                            if( empty($contactData) )
                            {
                                $personId = $form->get( 'person_id' )->getData();
                                $contactData = new Contact();
                                $contactType = $this->em->getRepository( 'AppBundle\Entity\Common\ContactType' )->findOneByEntity( strtolower( $locationType->getName() ) );
                                $contactData->setType( $contactType );
                                $person = $this->em->getRepository( 'AppBundle\Entity\Common\Person' )->find( $personId );
                                $contactData->setPerson( $person );
                                $contactData->setName( $person->getFullName() );
                                $address = $this->em->getRepository( 'AppBundle\Entity\Common\Address' )->find( $addressId );
                                $contactData->setAddress( $address );
                                $contactData->setEntity( $form->get( 'entity' )->getData() );
                                $this->em->persist( $contactData );

                            }
                            $data = $this->em->getReference( $class, $contactData->getId() );
                        }
                        else
                        {
                            $addressId = null;
                            $data = $this->em->getReference( $class, $entityId );
                        }
                        $location->setAddressId($addressId);
                        $location->setEntityData( $data );
                        $form->add( 'entity_data', EntityType::class, [
                            'class' => $class, 'data' => $data, 'attr' => [ 'class' => 'hidden' ]]
                        );
                    }
                    else
                    {
                        $form->add( 'entity_data', HiddenType::class, ['data' => null] );
                    }
                } );
            }

            /**
             * @param OptionsResolver $resolver
             */
            public function configureOptions( OptionsResolver $resolver )
            {
                $resolver->setDefaults( ['label' => false,
                    'data_class' => 'AppBundle\Entity\Asset\Location',
                    'allow_extra_fields' => true
                ] );
            }

            public function getName()
            {
                return 'location';
            }

        }
        
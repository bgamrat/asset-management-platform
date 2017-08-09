<?php

namespace AppBundle\Form\Admin\Asset;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use AppBundle\Form\Admin\Asset\DataTransformer\LocationTypeToIdTransformer;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormEvent;

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
                ->add( 'address', CheckboxType::class )
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
                            $contactData = $this->em->getRepository( $class )->findOneByAddress( $entityId );
                            $data = $this->em->getReference( $class, $contactData->getId() );
                        }
                        else
                        {
                            $data = $this->em->getReference( $class, $entityId );
                        }
                        $location->setEntityData( $data );
                        $form->add( 'entity_data', EntityType::class, [
                            'class' => $class, 'data' => $data]
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

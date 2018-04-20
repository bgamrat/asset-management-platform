<?php

Namespace App\Form\Admin\EventListener;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Common\Contact;

class LocationFieldSubscriber implements EventSubscriberInterface
{

    private $em,$entities;

    public function __construct( EntityManagerInterface $em, $entities = [] )
    {
        $this->em = $em;
        $this->entities = $entities;
    }

    public static function getSubscribedEvents()
    {
        return [FormEvents::POST_SET_DATA => 'postSetData', FormEvents::SUBMIT => 'submit'];
    }

    public function postSetData( FormEvent $event )
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
                $address = $this->em->getRepository( 'App\Entity\Common\Address' )->find( $addressId );
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
                'class' => $class, 'data' => $data, 'attr' => [ 'class' => 'hidden']]
            );
        }
        else
        {
            $form->add( 'entity_data', HiddenType::class, ['data' => null] );
        }
    }

    public function submit( FormEvent $event )
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
            if( !empty( $form->get( 'address_id' )->getData() ) )
            {
                $addressId = $form->get( 'address_id' )->getData();
                $contactData = $this->em->getRepository( $class )->findOneByAddress( $addressId );
                if( empty( $contactData ) )
                {
                    $personId = $form->get( 'person_id' )->getData();
                    $contactData = new Contact();
                    $contactType = $this->em->getRepository( 'App\Entity\Common\ContactType' )->findOneByEntity( strtolower( $locationType->getName() ) );
                    $contactData->setType( $contactType );
                    $person = $this->em->getRepository( 'App\Entity\Common\Person' )->find( $personId );
                    $contactData->setPerson( $person );
                    $contactData->setName( $person->getFullName() );
                    $address = $this->em->getRepository( 'App\Entity\Common\Address' )->find( $addressId );
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
            $location->setAddressId( $addressId );
            $location->setEntityData( $data );
            $form->add( 'entity_data', EntityType::class, [
                'class' => $class, 'data' => $data, 'attr' => [ 'class' => 'hidden']]
            );
        }
        else
        {
            $form->add( 'entity_data', HiddenType::class, ['data' => null] );
        }
    }

}

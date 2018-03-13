<?php

namespace AppBundle\Form\Admin\EventListener;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\ORM\EntityManager;
use AppBundle\Entity\Common\Contact;

class ContactFieldSubscriber implements EventSubscriberInterface
{

    private $em, $entities;

    public function __construct( EntityManager $em, Array $entities )
    {
        $this->em = $em;
        $this->entities = $entities;
    }

    public static function getSubscribedEvents()
    {
        return [ FormEvents::PRE_SUBMIT => 'preSubmit'];
    }

    public function preSubmit( FormEvent $event )
    {
        $contact = $event->getData();
        if( !empty( $contact['id'] ) )
        {
            return;
        }
        $form = $event->getForm();

        $contactData = new Contact();
        $personId = $contact['person_id'];

        $person = $this->em->getRepository( 'AppBundle\Entity\Common\Person' )->find( $personId );
        $contactData->setPerson( $person );
        if( !empty( $contact['address_id'] ) )
        {
            $addressId = $contact['address_id'];
            $address = $this->em->getRepository( 'AppBundle\Entity\Common\Address' )->find( $addressId );
            $contactData->setAddress( $address );
        }

        $contactData->setName( $contact['name'] );

        $class = null;
        $entityId = null;
        if( !empty( $contact['contact_entity_id'] ) )
        {
            $entityId = $contact['contact_entity_id'];
            $contactType = $contact['contact_type'];
            if( isset( $this->entities[$contactType] ) )
            {
                $class = $this->entities[$contactType];
                $contactType = $this->em->getRepository( 'AppBundle\Entity\Common\ContactType' )->findOneByEntity( $contactType );
                $contactData->setType( $contactType );
                $contactData->setEntity( $entityId );
            }
        }

        $this->em->persist( $contactData );
        $contact['id'] = $contactData->getId();
        $contact['entity_id'] = $entityId;
    }

}

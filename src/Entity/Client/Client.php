<?php

Namespace App\Entity\Client;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Common\Person;
use App\Entity\Traits\Versioned\Active;
use App\Entity\Traits\Versioned\Comment;
use App\Entity\Traits\Id;
use App\Entity\Traits\Versioned\Name;

/**
 * Client
 *
 * @ORM\Table(name="client")
 * @ORM\Entity(repositoryClass="App\Repository\ClientRepository")
 * @Gedmo\Loggable
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class Client
{

    use Active,
        Comment,
        Id,
        Name,
        TimestampableEntity,
        SoftDeleteableEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Common\Person", cascade={"persist"})
     * @ORM\JoinTable(name="client_contact",
     *      joinColumns={@ORM\JoinColumn(name="client_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="person_id", referencedColumnName="id")}
     *      )
     */
    private $contacts = null;
    /**
     * @var ArrayCollection $contracts
     * @ORM\ManyToMany(targetEntity="App\Entity\Client\Contract", cascade={"persist"})
     * @ORM\JoinTable(name="client_contract",
     *      joinColumns={@ORM\JoinColumn(name="client_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="contract_id", referencedColumnName="id", onDelete="CASCADE", unique=true, nullable=false)}
     *      )
     */
    protected $contracts = null;

    public function __construct()
    {
        $this->contacts = new ArrayCollection();
        $this->contracts = new ArrayCollection();
    }

    public function getContacts()
    {
        return $this->contacts->toArray();
    }

    public function addContact( Person $contact )
    {
        if( !$this->contacts->contains( $contact ) )
        {
            $this->contacts->add( $contact );
        }
    }

    public function removeContact( Person $contact )
    {
        $this->contacts->removeElement( $contact );
    }

    public function getContracts( $deep = true )
    {
        $return = [];
        if( $deep === true )
        {
            $return = $this->contracts->toArray();
        }
        else
        {
            foreach( $this->contracts as $c )
            {
                $ct = [];
                $ct['id'] = $c->getId();
                $ct['name'] = $c->getName();
                $ct['comment'] = $c->getComment();
                $ct['active'] = $c->isActive();
                $dt = $c->getStart();
                $ct['start'] = !empty( $dt ) ? $dt->format( 'Y-m-d' ) : null;
                $dt = $c->getEnd();
                $ct['end'] = !empty( $dt ) ? $dt->format( 'Y-m-d' ) : null;
                $ct['value'] = $c->getValue();
                $return[] = $ct;
            }
        }
        return $return;
    }

    public function addContract( Contract $contract )
    {
        if( !$this->contracts->contains( $contract ) )
        {
            $this->contracts->add( $contract );
            $contract->setClient( $this );
        }
        return $this;
    }

    public function removeContract( Contract $contract )
    {
        $this->contracts->removeElement( $contract );
    }

    public function setDeletedAt( $deletedAt )
    {
        $this->deletedAt = $deletedAt;
        $this->setActive( false );
    }

}

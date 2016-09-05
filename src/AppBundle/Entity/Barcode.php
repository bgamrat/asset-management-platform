<?php

Namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Barcode
 *
 * @ORM\Table(name="barcode")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BarcodeRepository")
 * @Gedmo\Loggable(logEntryClass="AppBundle\Entity\BarcodeLog")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * 
 */
class Barcode
{

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @var string
     * 
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private $comment;
    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean")
     * 
     */
    private $active = true;
    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $created;

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    private $updated;
    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Gedmo\Versioned
     */
    private $deletedAt;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Set id
     *
     * @return integer
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Set comment
     *
     * @param string $comment
     *
     * @return Barcode
     */
    public function setComment( $comment )
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    public function setActive( $active )
    {
        $this->active = $active;
    }

    public function isActive()
    {
        return $this->active;
    }

    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    public function setDeletedAt( $deletedAt )
    {
        $this->deletedAt = $deletedAt;
        $this->setActive( false );
    }

    public function toArray()
    {
        return [
            'barcode' => $this->getBarcode(),
            'comment' => $this->getComment(),
            'active' => $this->isActive()
        ];
    }

}
<?php

Namespace AppBundle\Entity\Asset;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * IssueItem
 *
 * @ORM\Table(name="issue_item")
 * @Gedmo\Loggable
 * @ORM\Entity()
 * 
 */
class IssueItem
{

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\ManyToOne(targetEntity="Issue", inversedBy="items")
     * @ORM\JoinColumn(name="item_id", referencedColumnName="id")
     */
    private $id;
    /**
     * @ORM\ManyToOne(targetEntity="\AppBundle\Entity\Asset\Asset")
     * @ORM\JoinColumn(name="asset_id", referencedColumnName="id")
     */
    private $asset = null;
    /**
     * @var string
     * 
     * @ORM\Column(type="string", length=64, nullable=false)
     * @Gedmo\Versioned
     */
    private $name;
    /**
     * @var string
     * 
     * @ORM\Column(type="string", length=64, nullable=true)
     * @Gedmo\Versioned
     */
    private $comment;
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
    public function setId( $id )
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set asset
     *
     * @param string $asset
     *
     * @return IssueItem
     */
    public function setAsset( $asset )
    {
        $this->asset = $asset;
        
        $name = '';
        if( !empty( $asset ) )
        {
            $barcodes = $asset->getBarcodes();
            if( !empty( $barcodes ) )
            {
                foreach( $barcodes as $b )
                {
                    if( $b->isActive() )
                    {
                        $name = $b->getBarcode().' - ';
                    }
                }
            }
            $model = $asset->getModel();
            $name .= $model->getBrand()->getName().' '.$asset->getModel()->getName();
        }
        $this->name = $name;

        return $this;
    }

    /**
     * Get asset
     *
     * @return string
     */
    public function getAsset()
    {
        return $this->asset;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return IssueItem
     */
    public function setName( $name )
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set comment
     *
     * @param string $comment
     *
     * @return IssueItem
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

    public function getUpdated()
    {
        return $this->updated;
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

}

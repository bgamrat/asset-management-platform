<?php

Namespace AppBundle\Entity\Asset;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * CategoryQuantity
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
     * @ORM\Column(type="string", length=64, nullable=true)
     * @Gedmo\Versioned
     */
    private $comment;

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

}

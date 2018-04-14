<?php

Namespace App\Entity\Asset;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Entity\Traits\Id;

/**
 * IssueNote
 *
 * @ORM\Table(name="issue_note")
 * @Gedmo\Loggable(logEntryClass="Entity\Asset\IssueLog")
 * @ORM\Entity()
 * 
 */
class IssueNote
{

    use Id,
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
     * @var string
     * 
     * @ORM\Column(type="string", length=512, nullable=false)
     * @Gedmo\Versioned
     */
    private $note;

    /**
     * Set note
     *
     * @param string $note
     *
     * @return IssueNote
     */
    public function setNote( $note )
    {
        $this->note = $note;

        return $this;
    }

    /**
     * Get note
     *
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }

}

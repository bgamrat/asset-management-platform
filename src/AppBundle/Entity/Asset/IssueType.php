<?php

namespace AppBundle\Entity\Asset;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Entity\Traits\Versioned\InUse;
use AppBundle\Entity\Traits\Versioned\Comment;
use AppBundle\Entity\Traits\Id;
use AppBundle\Entity\Traits\Type;
use AppBundle\Entity\Traits\Versioned\XDefault;

/**
 * Status
 *
 * @ORM\Table(name="issue_type")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\IssueTypeRepository")
 * @Gedmo\Loggable(logEntryClass="AppBundle\Entity\Asset\IssueLog")
 * @UniqueEntity("type")
 * @UniqueEntity("id")
 */
class IssueType
{

    use InUse,
        Comment,
        Id,
        Type,
        XDefault;

    /**
     * @var int
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\OneToMany(targetEntity="Issue", mappedBy="id")
     */
    private $id;

}

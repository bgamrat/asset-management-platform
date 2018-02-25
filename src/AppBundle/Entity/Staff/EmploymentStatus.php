<?php

namespace AppBundle\Entity\Staff;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\Traits\Id;
use AppBundle\Entity\Traits\Versioned\Name;
use AppBundle\Entity\Traits\Versioned\Active;
use AppBundle\Entity\Traits\Versioned\InUse;
use AppBundle\Entity\Traits\Versioned\Comment;
use AppBundle\Entity\Traits\Versioned\XDefault;
use AppBundle\Entity\Traits\History;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EmploymentStatusRepository")
 * @ORM\Table(name="employment_status")
 * @Gedmo\Loggable(logEntryClass="AppBundle\Entity\Staff\EmploymentStatusLog")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class EmploymentStatus
{

    use Id,
        Name,
        Comment,
        Active,
        InUse,
        TimestampableEntity,
        SoftDeleteableEntity,
        History;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    public function setDeletedAt( $deletedAt )
    {
        $this->deletedAt = $deletedAt;
        $this->setInUse( false );
    }

}

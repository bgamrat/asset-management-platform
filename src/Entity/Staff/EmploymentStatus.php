<?php

Namespace App\Entity\Staff;

use Doctrine\ORM\Mapping as ORM;
use Entity\Traits\Id;
use Entity\Traits\Versioned\Name;
use Entity\Traits\Versioned\Active;
use Entity\Traits\Versioned\InUse;
use Entity\Traits\Versioned\Comment;
use Entity\Traits\Versioned\XDefault;
use Entity\Traits\History;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="Repository\EmploymentStatusRepository")
 * @ORM\Table(name="employment_status")
 * @Gedmo\Loggable(logEntryClass="Entity\Staff\EmploymentStatusLog")
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

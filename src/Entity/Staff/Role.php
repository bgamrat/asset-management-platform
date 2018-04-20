<?php

Namespace App\Entity\Staff;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\Id;
use App\Entity\Traits\Versioned\Name;
use App\Entity\Traits\Versioned\InUse;
use App\Entity\Traits\Versioned\Comment;
use App\Entity\Traits\Versioned\XDefault;
use App\Entity\Traits\History;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RoleRepository")
 * @ORM\Table(name="role")
 * @Gedmo\Loggable(logEntryClass="App\Entity\Staff\RoleLog")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class Role
{

    use Id,
        Name,
        Comment,
        InUse,
        XDefault,
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

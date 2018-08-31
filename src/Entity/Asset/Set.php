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
use App\Entity\Traits\Versioned\Active;
use App\Entity\Traits\Versioned\Comment;
use App\Entity\Traits\Id;
use App\Entity\Traits\Versioned\Name;
use App\Entity\Traits\Satisfies;
use App\Entity\Traits\Versioned\Value;
use App\Entity\Traits\History;

/**
 * Set
 *
 * A set is a collection of models which satisfies one or more categories
 * A set may include models from different manufacturers
 * A set is not an actual entity, it is a way to group items to satisfy categories
 * It defines the relationship between models
 *
 * @ORM\Table(name="xset")
 * @ORM\Entity()
 * @Gedmo\Loggable(logEntryClass="App\Entity\Asset\AssetLog")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 *
 */
class Set
{

    use Active,
        Comment,
        Id,
        Name,
        Satisfies,
        Value,
        TimestampableEntity,
        SoftDeleteableEntity,
        History;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @var ArrayCollection $models
     * @ORM\ManyToMany(targetEntity="Model", cascade={"persist"}, orphanRemoval=true)
     */
    protected $models;

    public function __construct()
    {
        $this->models = new ArrayCollection();
        $this->satisfies = new ArrayCollection();
    }

    public function getModels()
    {
        return $this->models;
    }

    public function addModel( Model $model )
    {
        if( !$this->models->contains( $model ) )
        {
            $this->models->add( $model );
        }
    }

    public function removeModel( Model $model )
    {
        $this->models->removeElement( $model );
    }

    public function setDeletedAt( $deletedAt )
    {
        $this->deletedAt = $deletedAt;
        $this->setActive( false );
    }

}

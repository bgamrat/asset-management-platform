<?php

Namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait Satisfies
{

    /**
     * @ORM\ManyToMany(targetEntity="Category", fetch="EXTRA_LAZY")
     */
    private $satisfies;

    public function setSatisfies( $categories )
    {
        foreach( $categories as $c )
        {
            $this->addSatisfies( $c );
        }
        return $this;
    }

    public function getSatisfies()
    {
        return $this->satisfies;
    }

    public function addSatisfies( Category $category )
    {
        if( !$this->satisfies->contains( $category ) )
        {
            $this->satisfies->add( $category );
        }
    }

    public function removeSatisfies( Category $category )
    {
        $this->satisfies->removeElement( $category );
    }

}

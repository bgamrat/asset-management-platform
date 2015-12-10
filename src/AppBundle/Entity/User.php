<?php

// src/AppBundle/Entity/User.php

namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Group")
     * @ORM\JoinTable(name="fos_user_user_group",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
     * )
     */
    protected $groups;
    /**
     * @ORM\OneToOne(targetEntity="Invitation")
     * @ORM\JoinColumn(referencedColumnName="code")
     * @Assert\NotNull(message="Your invitation is wrong", groups={"Registration"})
     */
    protected $invitation;

    public function __construct()
    {
        parent::__construct();
        // your own logic
    }

    public function setInvitation( Invitation $invitation )
    {
        $this->invitation = $invitation;
    }

    public function getInvitation()
    {
        return $this->invitation;
    }

    public function hasGroup( $name = '' )
    {
        if( $name !== '' )
        {
            return parent::hasGroup( $name );
        }
        else
        {
            return false;
        }
    }

    public function setGroups( $groups = [] )
    {
        // TODO
        /*
        if( is_array( $groups ) )
        {
            if( count( $groups ) > 0 )
            {
                foreach( $groups as $g )
                {
                    $this->addGroup( $g );
                }
            }
        }
        else
        {
            if( is_numeric( $groups ) )
            {
                $this->addGroup($groups);
            }
        }
         * 
         */
    }
}
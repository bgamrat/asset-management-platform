<?php

namespace AppBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * IdAndName
 *
 * @author bgamrat
 */
class IdNameURL
{
    /*
     * @var int
     *
     * @Assert\NotEmpty()
     */

    public $id;

    /*
     * @var string
     *
     * @Assert\NotBlank()
     */
    public $name;
    /*
     * @var string
     *
     * @Assert\NotBlank()
     */
    public $url;

    public function __construct( $id, $name, $url )
    {
        $this->id = $id;
        $this->name = $name;
        $this->url = $url;
    }

}

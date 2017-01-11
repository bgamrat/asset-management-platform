<?php

namespace AppBundle\Entity\Common;

use Gedmo\Loggable\Entity\LogEntry;
use AppBundle\Entity\Common\Person;
use Doctrine\ORM\Mapping as ORM;
/**
 * AssetLog
 *
 * @ORM\Table(name="person_log")
 * @ORM\Entity()
 */
class PersonLog Extends LogEntry
{
    //put your code here

}

<?php

namespace AppBundle\Entity;

use Gedmo\Loggable\Entity\LogEntry;
use Doctrine\ORM\Mapping as ORM;
/**
 * UserLog
 *
 * @ORM\Table(name="user_log")
 * @ORM\Entity()
 */
class UserLog Extends LogEntry
{
}
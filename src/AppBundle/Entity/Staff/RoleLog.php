<?php

namespace AppBundle\Entity\Staff;

use Gedmo\Loggable\Entity\LogEntry;
use Doctrine\ORM\Mapping as ORM;
/**
 * RoleLog
 *
 * @ORM\Table(name="role_log")
 * @ORM\Entity()
 */
class RoleLog Extends LogEntry
{
}
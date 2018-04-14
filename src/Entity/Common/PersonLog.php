<?php

Namespace App\Entity\Common;

use Gedmo\Loggable\Entity\LogEntry;
use Doctrine\ORM\Mapping as ORM;
/**
 * PersonLog
 *
 * @ORM\Table(name="person_log")
 * @ORM\Entity()
 */
class PersonLog Extends LogEntry
{
}

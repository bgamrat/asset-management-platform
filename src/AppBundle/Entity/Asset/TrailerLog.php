<?php

namespace AppBundle\Entity\Asset;

use Gedmo\Loggable\Entity\LogEntry;
use Doctrine\ORM\Mapping as ORM;
/**
 * TrailerLog
 *
 * @ORM\Table(name="trailer_log")
 * @ORM\Entity()
 */
class TrailerLog Extends LogEntry
{
}
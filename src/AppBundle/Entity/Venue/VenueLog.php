<?php

namespace AppBundle\Entity\Venue;

use Gedmo\Loggable\Entity\LogEntry;
use Doctrine\ORM\Mapping as ORM;
/**
 * VenueLog
 *
 * @ORM\Table(name="venue_log")
 * @ORM\Entity()
 */
class VenueLog Extends LogEntry
{
}
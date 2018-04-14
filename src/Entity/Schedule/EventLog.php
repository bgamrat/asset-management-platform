<?php

Namespace App\Entity\Schedule;

use Gedmo\Loggable\Entity\LogEntry;
use Doctrine\ORM\Mapping as ORM;
/**
 * EventLog
 *
 * @ORM\Table(name="event_log")
 * @ORM\Entity()
 */
class EventLog Extends LogEntry
{
}

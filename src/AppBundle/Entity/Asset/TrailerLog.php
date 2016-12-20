<?php

namespace AppBundle\Entity\Asset;

use Gedmo\Loggable\Entity\LogEntry;
use Doctrine\ORM\Mapping as ORM;
/**
 * AssetLog
 *
 * @ORM\Table(name="trailer_log")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TrailerLogEntryRepository")
 */
class TrailerLog Extends LogEntry
{
}
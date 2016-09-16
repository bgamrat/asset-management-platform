<?php

namespace AppBundle\Entity;

use Gedmo\Loggable\Entity\LogEntry;
use AppBundle\Entity\Asset;
use Doctrine\ORM\Mapping as ORM;
/**
 * AssetLog
 *
 * @ORM\Table(name="asset_log")
 * @ORM\Entity(repositoryClass="Gedmo\Loggable\Entity\Repository\AssetLogEntryRepository")
 */
class AssetLog Extends LogEntry
{
}
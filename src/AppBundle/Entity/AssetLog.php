<?php

namespace AppBundle\Entity;

use Gedmo\Loggable\Entity\LogEntry;
use Doctrine\ORM\Mapping as ORM;
/**
 * AssetLog
 *
 * @ORM\Table(name="asset_log")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AssetLogEntryRepository")
 */
class AssetLog Extends LogEntry
{
}
<?php

namespace AppBundle\Entity;

use Gedmo\Loggable\Entity\LogEntry;
use Doctrine\ORM\Mapping as ORM;
/**
 * AssetLog
 *
 * @ORM\Table(name="model_log")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ModelLogEntryRepository")
 */
class ModelLog Extends LogEntry
{
}
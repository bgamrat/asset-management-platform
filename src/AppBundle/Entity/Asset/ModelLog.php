<?php

namespace AppBundle\Entity\Asset;

use Gedmo\Loggable\Entity\LogEntry;
use Doctrine\ORM\Mapping as ORM;
/**
 * ModelLog
 *
 * @ORM\Table(name="model_log")
 * @ORM\Entity()
 */
class ModelLog Extends LogEntry
{
}

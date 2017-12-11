<?php

namespace AppBundle\Entity\Asset;

use Gedmo\Loggable\Entity\LogEntry;
use AppBundle\Entity\Asset\Barcode;
use Doctrine\ORM\Mapping as ORM;
/**
 * BarcodeLog
 *
 * @ORM\Table(name="barcode_log")
 * @ORM\Entity()
 */
class BarcodeLog Extends LogEntry
{
}

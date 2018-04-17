<?php

Namespace App\Entity\Asset;

use Gedmo\Loggable\Entity\LogEntry;
use App\Entity\Asset\Barcode;
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

<?php

namespace AppBundle\Entity;

use Gedmo\Loggable\Entity\LogEntry;
use AppBundle\Entity\Barcode;
use Doctrine\ORM\Mapping as ORM;
/**
 * BarcodeLog
 *
 * @ORM\Table(name="barcode_log")
 * @ORM\Entity(repositoryClass="Gedmo\Loggable\Entity\Repository\LogEntryRepository")
 */
class BarcodeLog Extends LogEntry
{
    //put your code here

}

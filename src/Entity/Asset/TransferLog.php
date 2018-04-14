<?php

Namespace App\Entity\Asset;

use Gedmo\Loggable\Entity\LogEntry;
use Doctrine\ORM\Mapping as ORM;
/**
 * TransferLog
 *
 * @ORM\Table(name="transfer_log")
 * @ORM\Entity()
 */
class TransferLog Extends LogEntry
{
}
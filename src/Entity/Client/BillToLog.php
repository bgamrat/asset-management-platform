<?php

Namespace App\Entity\Client;

use Gedmo\Loggable\Entity\LogEntry;
use Doctrine\ORM\Mapping as ORM;
/**
 * BillToLog
 *
 * @ORM\Table(name="bill_to_log")
 * @ORM\Entity(repositoryClass="Repository\BillToLogEntryRepository")
 */
class BillToLog Extends LogEntry
{
}
<?php

Namespace App\Entity\Common;

use Gedmo\Loggable\Entity\LogEntry;
use Doctrine\ORM\Mapping as ORM;
/**
 * AddressLog
 *
 * @ORM\Table(name="address_log")
 * @ORM\Entity(repositoryClass="Repository\AddressLogEntryRepository")
 */
class AddressLog Extends LogEntry
{
}

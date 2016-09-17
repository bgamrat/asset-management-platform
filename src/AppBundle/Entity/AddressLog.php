<?php

namespace AppBundle\Entity;

use Gedmo\Loggable\Entity\LogEntry;
use Doctrine\ORM\Mapping as ORM;
/**
 * AddressLog
 *
 * @ORM\Table(name="address_log")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AddressLogEntryRepository")
 */
class AddressLog Extends LogEntry
{
}
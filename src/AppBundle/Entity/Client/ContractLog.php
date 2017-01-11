<?php

namespace AppBundle\Entity\Client;

use Gedmo\Loggable\Entity\LogEntry;
use Doctrine\ORM\Mapping as ORM;
/**
 * ContractLog
 *
 * @ORM\Table(name="contract_log")
 * @ORM\Entity()
 */
class ContractLog Extends LogEntry
{
}
<?php

namespace AppBundle\Entity\Asset;

use Gedmo\Loggable\Entity\LogEntry;
use Doctrine\ORM\Mapping as ORM;
/**
 * IssueLog
 *
 * @ORM\Table(name="issue_log")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\IssueLogEntryRepository")
 */
class IssueLog Extends LogEntry
{
}
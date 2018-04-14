<?php

Namespace App\Entity\Asset;

use Gedmo\Loggable\Entity\LogEntry;
use Doctrine\ORM\Mapping as ORM;
/**
 * IssueLog
 *
 * @ORM\Table(name="issue_log")
 * @ORM\Entity(repositoryClass="Repository\IssueLogEntryRepository")
 */
class IssueLog Extends LogEntry
{
}
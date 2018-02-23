<?php

namespace AppBundle\Entity\Staff;

use Gedmo\Loggable\Entity\LogEntry;
use Doctrine\ORM\Mapping as ORM;
/**
 * EmploymentStatusLog
 *
 * @ORM\Table(name="employment_status_log")
 * @ORM\Entity()
 */
class EmploymentStatusLog Extends LogEntry
{
}
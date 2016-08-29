<?php

namespace AppBundle\Entity;

use Gedmo\Loggable\Entity\LogEntry;
use AppBundle\Entity\Person;
use Doctrine\ORM\Mapping as ORM;
/**
 * AssetLog
 *
 * @ORM\Table(name="person_log")
 * @ORM\Entity(repositoryClass="Gedmo\Loggable\Entity\Repository\LogEntryRepository")
 */
class PersonLog Extends LogEntry
{
    //put your code here

}
<?php


namespace AppBundle\Repository;

use Gedmo\Loggable\Entity\Repository\LogEntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query;
use Gedmo\Loggable\Entity\LogEntry;
use Gedmo\Tool\Wrapper\EntityWrapper;
use Doctrine\ORM\EntityRepository;
use Gedmo\Loggable\LoggableListener;


class AssetLogEntryRepository extends LogEntityRepository {
    
}
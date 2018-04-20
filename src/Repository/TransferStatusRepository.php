<?php

Namespace App\Repository;

use App\Entity\Asset\IssueStatus;

/**
 * TransferStatusApp\Repository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TransferStatusRepository extends \Doctrine\ORM\EntityRepository
{

    public function findAll()
    {
        return $this->getEntityManager()
                        ->createQuery(
                                "SELECT ts FROM Entity\Asset\TransferStatus ts ORDER BY ts.name ASC"
                        )
                        ->getResult();
    }

}

<?php

namespace App\Repository;

use App\Entity\CreatedTickets;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CreatedTickets|null find($id, $lockMode = null, $lockVersion = null)
 * @method CreatedTickets|null findOneBy(array $criteria, array $orderBy = null)
 * @method CreatedTickets[]    findAll()
 * @method CreatedTickets[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CreatedTicketsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CreatedTickets::class);
    }

    // /**
    //  * @return CreatedTickets[] Returns an array of CreatedTickets objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CreatedTickets
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

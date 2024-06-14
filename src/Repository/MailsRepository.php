<?php

namespace App\Repository;

use App\Entity\Mails;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Mails>
 *
 * @method Mails|null find($id, $lockMode = null, $lockVersion = null)
 * @method Mails|null findOneBy(array $criteria, array $orderBy = null)
 * @method Mails[]    findAll()
 * @method Mails[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MailsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mails::class);
    }

    /**
     * @return Mails[] Returns an array of Mails objects
     */
    public function findBetween(string $start, string $end, int $site_id, int $limit = 10) : array
    {
        return $this->createQueryBuilder('m')
            ->andWhere("m.date BETWEEN '$start' AND '$end'")
            ->andWhere("m.site_id = $site_id")
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }

//    public function findOneBySomeField($value): ?Mails
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

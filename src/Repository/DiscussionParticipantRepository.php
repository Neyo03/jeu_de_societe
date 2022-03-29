<?php

namespace App\Repository;

use App\Entity\DiscussionParticipant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DiscussionParticipant|null find($id, $lockMode = null, $lockVersion = null)
 * @method DiscussionParticipant|null findOneBy(array $criteria, array $orderBy = null)
 * @method DiscussionParticipant[]    findAll()
 * @method DiscussionParticipant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DiscussionParticipantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DiscussionParticipant::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(DiscussionParticipant $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(DiscussionParticipant $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return DiscussionParticipant[] Returns an array of DiscussionParticipant objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?DiscussionParticipant
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

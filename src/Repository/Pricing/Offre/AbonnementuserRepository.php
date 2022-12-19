<?php

namespace App\Repository\Pricing\Offre;

use App\Entity\Pricing\Offre\Abonnementuser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Abonnementuser|null find($id, $lockMode = null, $lockVersion = null)
 * @method Abonnementuser|null findOneBy(array $criteria, array $orderBy = null)
 * @method Abonnementuser[]    findAll()
 * @method Abonnementuser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AbonnementuserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Abonnementuser::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Abonnementuser $entity, bool $flush = true): void
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
    public function remove(Abonnementuser $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return Abonnementuser[] Returns an array of Abonnementuser objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Abonnementuser
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

<?php

namespace App\Repository\Pricing\Offre;

use Doctrine\ORM\EntityRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Pricing\Offre\Caracteristique;

/**
 * CaracteristiqueRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CaracteristiqueRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Caracteristique::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Caracteristique $entity, bool $flush = true): void
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
    public function remove(Caracteristique $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

	public function getSelectList()
	{
		$qb = $this->createQueryBuilder('c')
					->orderBy('c.nom','ASC');
		return $qb;
	}

	public function myFindList($rang, $limit)
	{
		$query = $this->createQueryBuilder('c')
					->leftJoin('c.souscategorie','s')
					->addSelect('s')
					->where('s.rang = :rang')
					->setParameter('rang',$rang)
					->orderBy('c.rang','ASC')
					->getQuery();
		return $query->getResult();
	}
}

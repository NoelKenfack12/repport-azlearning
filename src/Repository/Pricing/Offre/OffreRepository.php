<?php

namespace App\Repository\Pricing\Offre;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Pricing\Offre\Offre;

/**
 * OffreRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
*/

class OffreRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Offre::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Offre $entity, bool $flush = true): void
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
    public function remove(Produit $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

	public function myFindBy()
	{
		$query = $this->createQueryBuilder('o')
					->orderBy('o.rang','ASC')
					->getQuery();
		return $query->getResult();
	}

    public function findLastOffer()
    {
        $query = $this->createQueryBuilder('o')
                    ->where('o.newprise > 0')
					->orderBy('o.rang','ASC')
					->getQuery();
		return $query->getOneOrNullResult();
    }
}

<?php
namespace App\Repository\Produit\Produit;

use Doctrine\ORM\EntityRepository;

/**
 * ComposexerciceRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ComposexerciceRepository extends EntityRepository
{
public function myfindComposexercice($idpropan,$idchapter)
{
	$query = $this->createQueryBuilder('c')
	              ->leftJoin('c.produitpanier','p')
	              ->leftJoin('c.exercicepartie','e')
	              ->leftJoin('e.chapitrecours','h')
				  ->addSelect('p')
				  ->addSelect('e')
				  ->addSelect('h')
				  ->where('p.id = :id')
				  ->andWhere('h.id = :idh')
				  ->setParameter('id',$idpropan)
				  ->setParameter('idh',$idchapter)
	              ->orderBy('c.date','DESC')
                  ->getQuery();
	return $query->getResult();
}
}
<?php
namespace App\Repository\Produit\Produit;

use Doctrine\ORM\EntityRepository;

/**
 * ExercicepartieRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ExercicepartieRepository extends EntityRepository
{
public function myFindByCours($id)
{
	$query = $this->createQueryBuilder('e')
	              ->leftJoin('e.chapitrecours','c')
	              ->leftJoin('c.partiecours','p')
	              ->leftJoin('p.produit','d')
				  ->addSelect('c')
				  ->addSelect('p')
				  ->addSelect('d')
				  ->where('d.id = :id')
				  ->setParameter('id',$id)
	              ->orderBy('d.rang','ASC')
                  ->getQuery();
	return $query->getResult();
}
}

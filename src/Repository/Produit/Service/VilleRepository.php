<?php
namespace App\Repository\Produit\Service;

use Doctrine\ORM\EntityRepository;

/**
 * VilleRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class VilleRepository extends EntityRepository
{
public function myFindAll()
{
	$query = $this->createQueryBuilder('v')
	              ->orderBy('v.nom','DESC')
                  ->getQuery();
	return $query->getResult();
}

}
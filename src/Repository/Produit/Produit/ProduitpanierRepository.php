<?php
namespace App\Repository\Produit\Produit;

use Doctrine\ORM\EntityRepository;

/**
 * ProduitpanierRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ProduitpanierRepository extends EntityRepository
{
public function myfindBy($id)
{
	$query = $this->createQueryBuilder('pp')
	              ->leftJoin('pp.produit','p')
	              ->leftJoin('pp.panier','pa')
				  ->addSelect('p')
				  ->where('pa.id = :id')
				  ->setParameter('id',$id)
	              ->orderBy('pp.date','DESC')
                  ->getQuery();
	return $query->getResult();
}
public function getPanierProduit($id)
{
	$query = $this->createQueryBuilder('pp')
	              ->leftJoin('pp.produit','p')
	              ->leftJoin('pp.panier','pa')
				  ->addSelect('p')
				  ->addSelect('pa')
				  ->where('p.id = :id')
				  ->andWhere('pa.payer = 1')
				  ->setParameter('id',$id)
	              ->orderBy('pp.date','DESC')
                  ->getQuery();
	return $query->getResult();
}
}
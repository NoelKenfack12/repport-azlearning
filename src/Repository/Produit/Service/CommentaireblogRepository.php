<?php

namespace App\Repository\Produit\Service;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * CommentaireblogRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CommentaireblogRepository extends EntityRepository
{
public function findSujetForum($id,$page,$nombreParPage)
{
	// On déplace la vérification du numéro de page dans cette méthode
    if ($page < 1) {
    throw new \InvalidArgumentException('Page inexistant');
     }
    // La construction de la requête reste inchangée
    $query = $this->createQueryBuilder('c')
	              ->leftJoin('c.service', 's')
				  ->addSelect('s')
				  ->where('s.id = :id')
				  ->setParameter('id', $id)
                  ->orderBy('c.date', 'DESC')
                  ->getQuery();
    // On définit l'établissemnt à partir duquel commencer la liste
    $query->setFirstResult(($page-1) * $nombreParPage)
    // Ainsi que le nombre d'établissement à afficher
          ->setMaxResults($nombreParPage);
    // Enfin, on retourne l'objet Paginator correspondant à la requête construite
return new Paginator($query);
}

public function searchSujetForum($donnee,$page,$nombreParPage)
{
	// On déplace la vérification du numéro de page dans cette méthode
    if ($page < 1) {
    throw new \InvalidArgumentException('Page inexistant');
     }
    // La construction de la requête reste inchangée
    $query = $this->createQueryBuilder('c')
	              ->leftJoin('c.service', 's')
	              ->leftJoin('c.user', 'u')
	              ->leftJoin('c.interventions', 'i')
				  ->addSelect('s')
				  ->addSelect('i')
				  ->addSelect('u')
				  ->where('s.nom LIKE :n AND s.themeforum = 1')
				  ->orWhere('s.description LIKE :n AND s.themeforum = 1')
				  ->orWhere('c.titre LIKE :n')
				  ->orWhere('c.contenu LIKE :n')
				  ->orWhere('i.contenu LIKE :n')
				  ->orWhere('u.nom LIKE :n')
				  ->setParameter('n', '%'.$donnee.'%')
                  ->orderBy('c.date', 'DESC')
                  ->getQuery();
    // On définit l'établissemnt à partir duquel commencer la liste
    $query->setFirstResult(($page-1) * $nombreParPage)
    // Ainsi que le nombre d'établissement à afficher
          ->setMaxResults($nombreParPage);
    // Enfin, on retourne l'objet Paginator correspondant à la requête construite
return new Paginator($query);
}
}

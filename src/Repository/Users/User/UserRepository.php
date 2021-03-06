<?php
namespace App\Repository\Users\User;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * UserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserRepository extends EntityRepository
{
public function findAllGagnant()
{
	$query = $this->createQueryBuilder('u')
				  ->where('u.soldetransit > 10')
                  ->getQuery();
	return $query->getResult();
}
public function listeformateur($page,$nombreParPage)
{
	// On déplace la vérification du numéro de page dans cette méthode
    if ($page < 1){
    throw new \InvalidArgumentException('Page inexistant');
    }
    // La construction de la requête reste inchangée
    $query = $this->createQueryBuilder('u')
				  ->where('u.formateur = 1')
	              ->orderBy('u.nom','ASC')
                  ->getQuery();
    // On définit l'établissemnt à partir duquel commencer la liste
    $query->setFirstResult(($page-1) * $nombreParPage)
    // Ainsi que le nombre d'établissement à afficher
          ->setMaxResults($nombreParPage);
    // Enfin, on retourne l'objet Paginator correspondant à la requête construite
return new Paginator($query);
}

public function findFormateurs()
{
    $query = $this->createQueryBuilder('u')
				  ->where("u.formateur = 1 AND u.poste != ''")
	              ->orderBy('u.datebeg','ASC')
                  ->getQuery();
return $query->getResult();
}
public function myFindAll($page,$nombreParPage)
{
     // On déplace la vérification du numéro de page dans cette méthode
    if ($page < 1) {
    throw new \InvalidArgumentException('Page inexistant');
     }
    // La construction de la requête reste inchangée
    $query = $this->createQueryBuilder('u')
                  ->orderBy('u.datebeg', 'ASC')
                  ->getQuery();
    // On définit l'établissemnt à partir duquel commencer la liste
    $query->setFirstResult(($page-1) * $nombreParPage)
    // Ainsi que le nombre d'établissement à afficher
          ->setMaxResults($nombreParPage);
    // Enfin, on retourne l'objet Paginator correspondant à la requête construite
return new Paginator($query);
}

public function myFindAllUser($page,$nombreParPage, $search)
{
     // On déplace la vérification du numéro de page dans cette méthode
    if ($page < 1) {
    throw new \InvalidArgumentException('Page inexistant');
    }
    // La construction de la requête reste inchangée
    $query = $this->createQueryBuilder('u')
                  ->where("u.nom Like :nom")
                  ->orWhere("u.prenom Like :nom")
                  ->orWhere("u.username Like :nom")
                  ->setParameter('nom', '%'.$search.'%')
                  ->orderBy('u.datebeg', 'ASC')
                  ->getQuery();
    // On définit l'établissemnt à partir duquel commencer la liste
    $query->setFirstResult(($page-1) * $nombreParPage)
    // Ainsi que le nombre d'établissement à afficher
          ->setMaxResults($nombreParPage);
    // Enfin, on retourne l'objet Paginator correspondant à la requête construite
return new Paginator($query);
}
}

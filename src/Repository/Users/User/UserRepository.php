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

public function myFindAllFormateur($page,$nombreParPage, $search)
{
     // On déplace la vérification du numéro de page dans cette méthode
    if ($page < 1) {
    throw new \InvalidArgumentException('Page inexistant');
    }
    // La construction de la requête reste inchangée
    $query = $this->createQueryBuilder('u')
                  ->where("u.nom Like :nom AND u.formateur = 1")
                  ->orWhere("u.prenom Like :nom AND u.formateur = 1")
                  ->orWhere("u.username Like :nom AND u.formateur = 1")
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

public function myFindLatestFormateur($page,$nombreParPage, $search, $signAfter)
{
     // On déplace la vérification du numéro de page dans cette méthode
    if ($page < 1) {
    throw new \InvalidArgumentException('Page inexistant');
    }
    // La construction de la requête reste inchangée
    $query = $this->createQueryBuilder('u')
                  ->where("u.nom Like :nom AND u.formateur = 1 AND u.dateins >= :date")
                  ->orWhere("u.prenom Like :nom AND u.formateur = 1 AND u.dateins >= :date")
                  ->orWhere("u.username Like :nom AND u.formateur = 1 AND u.dateins >= :date")
                  ->setParameter('nom', '%'.$search.'%')
                  ->setParameter('date', $signAfter)
                  ->orderBy('u.datebeg', 'ASC')
                  ->getQuery();
    // On définit l'établissemnt à partir duquel commencer la liste
    $query->setFirstResult(($page-1) * $nombreParPage)
    // Ainsi que le nombre d'établissement à afficher
          ->setMaxResults($nombreParPage);
    // Enfin, on retourne l'objet Paginator correspondant à la requête construite
return new Paginator($query);
}

public function myFindAllAdmin($page,$nombreParPage, $search)
{
     // On déplace la vérification du numéro de page dans cette méthode
    if ($page < 1) {
    throw new \InvalidArgumentException('Page inexistant');
    }
    // La construction de la requête reste inchangée
    $query = $this->createQueryBuilder('u')
                  ->where("u.nom Like :nom AND (u.roles LIKE :admin OR u.roles LIKE :gestion)")
                  ->orWhere("u.prenom Like :nom AND (u.roles LIKE :admin OR u.roles LIKE :gestion)")
                  ->orWhere("u.username Like :nom AND (u.roles LIKE :admin OR u.roles LIKE :gestion)")
                  ->setParameter('nom', '%'.$search.'%')
                  ->setParameter('admin', '%ROLE_ADMIN%')
                  ->setParameter('gestion', '%ROLE_GESTION%')
                  ->orderBy('u.datebeg', 'ASC')
                  ->getQuery();
    // On définit l'établissemnt à partir duquel commencer la liste
    $query->setFirstResult(($page-1) * $nombreParPage)
    // Ainsi que le nombre d'établissement à afficher
          ->setMaxResults($nombreParPage);
    // Enfin, on retourne l'objet Paginator correspondant à la requête construite
return new Paginator($query);
}

public function myFindLatestAdmin($page,$nombreParPage, $search, $signAfter)
{
     // On déplace la vérification du numéro de page dans cette méthode
    if ($page < 1) {
    throw new \InvalidArgumentException('Page inexistant');
    }
    // La construction de la requête reste inchangée
    $query = $this->createQueryBuilder('u')
                  ->where("u.nom Like :nom AND (u.roles LIKE :admin OR u.roles LIKE :gestion) AND u.dateins >= :date")
                  ->orWhere("u.prenom Like :nom AND (u.roles LIKE :admin OR u.roles LIKE :gestion) AND u.dateins >= :date")
                  ->orWhere("u.username Like :nom AND (u.roles LIKE :admin OR u.roles LIKE :gestion) AND u.dateins >= :date")
                  ->setParameter('nom', '%'.$search.'%')
                  ->setParameter('admin', '%ROLE_ADMIN%')
                  ->setParameter('gestion', '%ROLE_GESTION%')
                  ->setParameter('date', $signAfter)
                  ->orderBy('u.datebeg', 'ASC')
                  ->getQuery();
    // On définit l'établissemnt à partir duquel commencer la liste
    $query->setFirstResult(($page-1) * $nombreParPage)
    // Ainsi que le nombre d'établissement à afficher
          ->setMaxResults($nombreParPage);
    // Enfin, on retourne l'objet Paginator correspondant à la requête construite
return new Paginator($query);
}

public function myFindLatestUser($page,$nombreParPage, $search, $signAfter)
{
     // On déplace la vérification du numéro de page dans cette méthode
    if ($page < 1) {
    throw new \InvalidArgumentException('Page inexistant');
    }
    // La construction de la requête reste inchangée
    $query = $this->createQueryBuilder('u')
                  ->where("u.nom Like :nom AND (u.roles NOT LIKE :admin OR u.roles NOT LIKE :gestion) AND u.dateins >= :date")
                  ->orWhere("u.prenom Like :nom AND (u.roles NOT LIKE :admin OR u.roles NOT LIKE :gestion) AND u.dateins >= :date")
                  ->orWhere("u.username Like :nom AND (u.roles NOT LIKE :admin OR u.roles NOT LIKE :gestion) AND u.dateins >= :date")
                  ->setParameter('nom', '%'.$search.'%')
                  ->setParameter('admin', '%ROLE_ADMIN%')
                  ->setParameter('gestion', '%ROLE_GESTION%')
                  ->setParameter('date', $signAfter)
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

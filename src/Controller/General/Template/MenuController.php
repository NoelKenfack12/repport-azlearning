<?php
/*
  (c) Noel Kenfack <noel.kenfack@yahoo.fr>
*/

namespace App\Controller\General\Template;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\General\Template\Recherche;
use App\Entity\Users\User\Newsletter;
use App\Form\Users\User\NewsletterType;
use App\Service\Servicetext\GeneralServicetext;
use App\Entity\Produit\Produit\Panier;
use App\Entity\Users\User\Notification;
use App\Entity\Produit\Produit\Categorie;
use App\Entity\Produit\Service\Service;
use App\Entity\Produit\Service\Commentaireblog;
use App\Entity\Users\User\User;
use App\Entity\Produit\Produit\Souscategorie;
use App\Entity\Produit\Produit\Produit;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class MenuController extends AbstractController
{
private $params;

public function __construct(ParameterBagInterface $params)
{
	$this->params = $params;
}

public function menubare(GeneralServicetext $service, $position='accueil')
{
	$em = $this->getDoctrine()->getManager();
	$session = $this->get('session');
	$actualiseformation = $session->get('actualiseformation');

	if($this->getUser() == null and isset($_COOKIE["PIDSESSREM"]) and $_COOKIE["PIDSESSREM"] != 'delete')
	{
		$cookies = $_COOKIE["PIDSESSREM"];
		$username = trim($service->decrypt($cookies, $this->params->get('saltcookies')));
		
		$repository = $em->getRepository(User::class);
		$user = $repository->findOneBy(array('username'=>$username));
		
		if($user != null)
		{
			$token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
			// On passe le token crée au service security context afin que l'utilisateur soit authentifié
			$this->get('security.token_storage')->setToken($token);
			$this->get('session')->set('_security_users', serialize($token));
		}
	}

	if($this->getUser() != null and $actualiseformation != 100)
	{
		$liste_oldpanier = $em->getRepository(Panier::class)
							  ->findBy(array('user'=>$this->getUser(),'valide'=>1));
		foreach($liste_oldpanier as $panier)
		{
			if($panier->getService() != null)
			{
				$periode = ($panier->getService()->getDureeacces() - $panier->getDate()->diff(new \Datetime())->days);
				if($periode <= 0)
				{
					$panier->setValide(false);
				}
			}
			if($panier->getChapitrecours() != null)
			{
				$periode = ($panier->getChapitrecours()->getPartiecours()->getProduit()->getDureeacces() - $panier->getDate()->diff(new \Datetime())->days);
				if($periode <= 0)
				{
					$panier->setValide(false);
				}
			}
			if($panier->getChapitrecours() == null and $panier->getService() == null)
			{
				$produit = null;
				foreach($panier->getProduitpaniers() as $propan)
				{
					$produit = $propan->getProduit();
					break;
				}
				if($produit != null)
				{
				$periode = ($produit->getDureeacces() - $panier->getDate()->diff(new \Datetime())->days);
				if($periode <= 0)
				{
					$panier->setValide(false);
				}
				}
			}
		}
		$em->flush();
		$session->set('actualiseformation',100);
	}	
	
	$liste_notification = new \Doctrine\Common\Collections\ArrayCollection();
	if($this->getUser() != null)
	{
		$liste_notification = $em->getRepository(Notification::class)
							     ->findBy(array('user'=>$this->getUser(),'lut'=>0));
	}
	
	$liste_categorie = $em->getRepository(Categorie::class)
	                      ->myFindAll();
	foreach($liste_categorie as $cat)
	{
		$cat->setEm($em);
		foreach($cat->getSouscategories() as $scat)
		{
			$scat->setEm($em);
		}
	}
	
	$liste_formation = $em->getRepository(Service::class)
	                      ->listeformation(1,8);
	$liste_message = new \Doctrine\Common\Collections\ArrayCollection();
	if($this->getUser() != null)
	{
		$liste_message = $em->getRepository(Commentaireblog::class)
							->findBy(array('dest'=>$this->getUser(),'lut'=>0));
	}
	return $this->render('Theme/General/Template/Menu/menubare.html.twig',
	array('liste_notification'=>$liste_notification,'position'=>$position,'liste_categorie'=>$liste_categorie,
	'liste_formation'=>$liste_formation, 'liste_message'=>$liste_message));
}

public function footer($position='accueil')
{
	$em = $this->getDoctrine()->getManager();
	$topcat = $em->getRepository(Souscategorie::class)
	             ->topsouscategorie(5);
				 
	$repositorie = $em->getRepository(Produit::class);
	$plus_vendu = $repositorie->topProduit(5);
	$plus_like = $repositorie->topLike(5);
	$topservice = $em->getRepository(Service::class)
	             ->topservice(5);
	$nbprod = 0;
	$produitpanier = null;
	$newsletter = new Newsletter();
	$form = $this->createForm(NewsletterType::class, $newsletter);
	if($this->getUser() != null)
	{
		$panier = $em->getRepository(Panier::class)
	                      ->findOneBy(array('user'=>$this->getUser(),'payer'=>0));
			if($panier != null)
			{
				$produitpanier = $panier->getProduitpaniers();
				foreach($produitpanier as $prodpan)
				{
					$nbprod = $nbprod + $prodpan->getQuantite();
				}
			}
	}
	
	$liste_categorie = $em->getRepository(Categorie::class)
	                      ->myFindAll();
						  
	return $this->render('Theme/General/Template/Menu/footer.html.twig',
	array('topcat'=>$topcat,'plus_vendu'=>$plus_vendu,'plus_like'=>$plus_like,'nbprod'=>$nbprod,
	'topservice'=>$topservice,'position'=>$position,'liste_categorie'=>$liste_categorie,'form'=>$form->createView()));
}

public function testinscriptionnewsletter()
{
	$session = $this->get('session');
	$envoi = $session->get('test_newsletter');
	
	if($envoi !== 100)
	{
		$newsletter = new Newsletter();
		$form = $this->createForm(NewsletterType::class, $newsletter);
		
		return $this->render('Theme/General/Template/Menu/testinscriptionnewsletter.html.twig',
		array('form'=>$form->createView()));
	}
	return new Response(' ');
}

public function parametreuser()
{

	$em = $this->getDoctrine()->getManager();
	$liste_message = $em->getRepository(Commentaireblog::class)
						->findBy(array('dest'=>$this->getUser(),'lut'=>0), array('date'=>'desc'));
						
	$liste_notification = $em->getRepository(Notification::class)
						         ->findBy(array('user'=>$this->getUser(),'lut'=>0), array('date'=>'desc'));
	return $this->render('Theme/General/Template/Menu/parametreuser.html.twig',
	array('liste_message'=>$liste_message, 'liste_notification'=>$liste_notification));

}

public function stopAlertNewletterAction()
{
	$session = $this->get('session');
	$session->set('test_newsletter',100);
	echo 1;
	exit;
}
}
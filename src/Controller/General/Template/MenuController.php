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
use App\Entity\Users\Adminuser\Parametreadmin;
use Symfony\Component\HttpFoundation\Request;
use App\Security\TokenAuthenticator;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class MenuController extends AbstractController
{
private $params;
private $authenticator;
private $guardHandler;

public function __construct(ParameterBagInterface $params, TokenAuthenticator $authenticator, GuardAuthenticatorHandler $guardHandler)
{
	$this->params = $params;
	$this->authenticator = $authenticator;
    $this->guardHandler = $guardHandler;
}

public function menubare(GeneralServicetext $service, Request $request, $position='accueil')
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
			$response = $this->guardHandler->authenticateUserAndHandleSuccess(
				$user,          // the User object you just created
				$request,
				$this->authenticator, // authenticator whose onAuthenticationSuccess you want to use
				'main'          // the name of your firewall in security.yaml
			);
		}
	}

	if($this->getUser() != null and $actualiseformation != 100)
	{
		$liste_oldpanier = $em->getRepository(Panier::class)
							  ->findBy(array('user'=>$this->getUser()));
		foreach($liste_oldpanier as $panier)
		{
			if($panier->getService() != null and ($panier->getDureeFormation() == null || $panier->getDureeFormation() == 0)){
				$panier->setDureeFormation($panier->getService()->getDureeacces());
			}else if($panier->getChapitrecours() != null)
			{
				$panier->setDureeFormation($panier->getChapitrecours()->getPartiecours()->getProduit()->getDureeacces());
			}else if($panier->getChapitrecours() == null and $panier->getService() == null)
			{
				$produit = null;
				foreach($panier->getProduitpaniers() as $propan)
				{
					$produit = $propan->getProduit();
					break;
				}
				
				if($produit != null)
				{
					$panier->setDureeFormation($produit->getDureeacces());
				}
			}

			///
			/*
			if($panier->getService() != null)
			{
				$periode = ($panier->getService()->getDureeacces() - $panier->getDate()->diff(new \Datetime())->days);
				if($periode <= 0)
				{
					$panier->setValide(true);
					$panier->setLivrer(true);
				}
			}
			if($panier->getChapitrecours() != null)
			{
				$periode = ($panier->getChapitrecours()->getPartiecours()->getProduit()->getDureeacces() - $panier->getDate()->diff(new \Datetime())->days);
				if($periode <= 0)
				{
					$panier->setValide(true);
					$panier->setLivrer(true);
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
					$panier->setValide(true);
					$panier->setLivrer(true);
				}
				}
			}*/
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

	$paramlogosm = $em->getRepository(Parametreadmin::class)
	                   ->findOneBy(array('type'=>'logosm'));
    $paramlogomd = $em->getRepository(Parametreadmin::class)
	                   ->findOneBy(array('type'=>'logomd'));

	if($this->isGranted('ROLE_GESTION'))//Mise à jour des formations expirées
	{
		$liste_panier = $em->getRepository(Panier::class)
					       ->searchpanierinvalide(1, 500, '');
		foreach($liste_panier as $panier)
		{
			$periode = ($panier->getDureeFormation() - $panier->getDate()->diff(new \Datetime())->days);
			if($periode <= 0)
			{
				$panier->setValide(true);
				$panier->setLivrer(true);

				if($panier->getAbonnementuser() != null)
				{
					$panier->getAbonnementuser()->setActive(false);
				}
			}
		}
		$em->flush();
	}
	//$service->getThemeDirectory()
	return $this->render('Theme/General/Template/Menu/menubare.html.twig',
	array('liste_notification'=>$liste_notification,'position'=>$position,'liste_categorie'=>$liste_categorie,
	'liste_formation'=>$liste_formation, 'liste_message'=>$liste_message, 'paramlogosm'=>$paramlogosm, 'paramlogomd'=>$paramlogomd));
}

public function footer(GeneralServicetext $service, $position = 'accueil')
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
	
	$paramlogosm = $em->getRepository(Parametreadmin::class)
	                   ->findOneBy(array('type'=>'logosm'));
	$aproposfooter = $em->getRepository(Parametreadmin::class)
	                    ->findOneBy(array('type'=>'aproposfooter'));
	$paramtel = $em->getRepository(Parametreadmin::class)
	                   ->findOneBy(array('type'=>'telprincipal'));
	$paramemail = $em->getRepository(Parametreadmin::class)
	                   ->findOneBy(array('type'=>'emailprincipal'));

	$telwhatsapp = $em->getRepository(Parametreadmin::class)
	                  ->findOneBy(array('type'=>'telwhatsapp'));
	$adresse = $em->getRepository(Parametreadmin::class)
	                   ->findOneBy(array('type'=>'adresse'));

	$paramlogomd = $em->getRepository(Parametreadmin::class)
	                   ->findOneBy(array('type'=>'logomd'));
	//$service->getThemeDirectory()
	return $this->render('Theme/General/Template/Menu/footer.html.twig',
	array('topcat'=>$topcat,'plus_vendu'=>$plus_vendu,'plus_like'=>$plus_like,'nbprod'=>$nbprod,
	'topservice'=>$topservice,'position'=>$position,'liste_categorie'=>$liste_categorie,
	'form'=>$form->createView(), 'paramlogosm'=>$paramlogosm, 'aproposfooter'=>$aproposfooter,'paramlogomd'=>$paramlogomd, 
	'paramtel'=>$paramtel, 'paramemail'=>$paramemail, 'telwhatsapp'=>$telwhatsapp, 'adresse'=>$adresse));
}

public function testinscriptionnewsletter(GeneralServicetext $service)
{
	return $this->render($service->getThemeDirectory().'/General/Template/Menu/testinscriptionnewsletter.html.twig');
}

public function parametreuser(GeneralServicetext $service)
{
	$em = $this->getDoctrine()->getManager();
	$liste_message = $em->getRepository(Commentaireblog::class)
						->findBy(array('dest'=>$this->getUser(),'lut'=>0), array('date'=>'desc'));
						
	$liste_notification = $em->getRepository(Notification::class)
						         ->findBy(array('user'=>$this->getUser(),'lut'=>0), array('date'=>'desc'));
	return $this->render($service->getThemeDirectory().'/General/Template/Menu/parametreuser.html.twig',
	array('liste_message'=>$liste_message, 'liste_notification'=>$liste_notification));
}

public function stopAlertNewletterAction()
{
	$session = $this->get('session');
	$session->set('test_newsletter',100);
	echo 1;
	exit;
}

public function headermodal(GeneralServicetext $service, $title)
{
	$em = $this->getDoctrine()->getManager();
					   
	return $this->render($service->getThemeDirectory().'/General/Template/Menu/headermodal.html.twig', array('title'=>$title));
}

public function relicon(GeneralServicetext $service, $position = 'ficon')
{
	$em = $this->getDoctrine()->getManager();
	$paramlogosm = $em->getRepository(Parametreadmin::class)
	                   ->findOneBy(array('type'=>'logosm'));

	return $this->render($service->getThemeDirectory().'/General/Template/Menu/relicon.html.twig',
	 array('paramlogosm'=>$paramlogosm,'position'=>$position));
}
}
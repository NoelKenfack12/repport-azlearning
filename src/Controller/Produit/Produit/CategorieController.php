<?php
/*(c) Noel Kenfack <noel.kenfack@yahoo.fr> Février 2016
*/
namespace App\Controller\Produit\Produit;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Produit\Produit\Categorie;
use App\Form\Produit\Produit\CategorieType;
use App\Entity\Produit\Produit\Souscategorie;
use App\Form\Produit\Produit\SouscategorieType;
use App\Entity\Produit\Produit\Produit;
use App\Form\Produit\Produit\ProduitType;
use App\Service\AfMail\Afmail;
use App\Service\AfMail\fileAttachment;
use App\Entity\Users\User\Notification;
use App\Service\Servicetext\GeneralServicetext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use App\Entity\Produit\Produit\Panier;
use App\Entity\Users\User\User;
use App\Service\Email\Singleemail;

class CategorieController extends AbstractController
{

private $params;
private $_servicemail;

public function __construct(ParameterBagInterface $params, Singleemail $servicemail)
{
	$this->params = $params;
	$this->_servicemail = $servicemail;
}
	

public function savecategorie(GeneralServicetext $service, Request $request)
{
	$em = $this->getDoctrine()->getManager();
	$categorie = new Categorie($service);
	$form = $this->createForm(CategorieType::class, $categorie);
	if($request->getMethod() == 'POST')
	{
		$form->handleRequest($request);
		$categorie->setUser($this->getUser());
		$categorie->setServicetext($service);
		$nbcategorie = $em->getRepository(Categorie::class)
	                      ->FindAll();
		$nbcategoriemax = $this->params->get('nbcategorie');
		if ($form->isValid() and count($nbcategorie) <= $nbcategoriemax){
			$em->persist($categorie);
			$em->flush();
		}else{
			if(count($nbcategorie) > $nbcategoriemax)
			{
				$this->get('session')->getFlashBag()->add('image','Trop de catégorie.');
			}else{
				$this->get('session')->getFlashBag()->add('image','Données invalides.');
			}
		}
	}else{
	$this->get('session')->getFlashBag()->add('image','Données en mode get.');
	}
	return $this->redirect($this->generateUrl('users_adminuser_categorisation_produit_plateforme'));
}

public function ajoutersouscategorie(GeneralServicetext $service, Request $request)
{
	$em = $this->getDoctrine()->getManager();
	$souscategorie = new Souscategorie($service);
	$form = $this->createForm(SousCategorieType::class, $souscategorie);
	if($request->getMethod() == 'POST')
	{
		$form->handleRequest($request);
		$souscategorie->setUser($this->getUser());
		$souscategorie->setServicetext($service);
		$liste_souscategorie = $em->getRepository(Souscategorie::class)
	                              ->FindBy(array('categorie'=>$souscategorie->getCategorie()));
		$nbcategoriemax = $this->params->get('nbsouscategorie');
		if($form->isValid() and count($liste_souscategorie) <= $nbcategoriemax){

			$em->persist($souscategorie);
			$em->flush();

		}else{
			if( count($liste_souscategorie) > $nbcategoriemax )
			{
			  $this->get('session')->getFlashBag()->add('images','Trop de sous-catégorie.');
			}else{
			  $this->get('session')->getFlashBag()->add('images','Données invalides.');
			}
		}
	}
	return $this->redirect($this->generateUrl('users_adminuser_categorisation_produit_plateforme'));
}

public function listecategorie(Categorie $cat, $pagesuivante)
{
	if(isset($_POST['page']))
	{
		$pagesuivante = $_POST['page'];
	}else{
		$pagesuivante = $pagesuivante;
	}
	
	$em = $this->getDoctrine()->getManager();
	$nbcategorie = $em->getRepository(Souscategorie::class)
	                  ->findScatcourantCat($cat->getId(), $pagesuivante, 10);
	$repository = $em->getRepository(Produit::class);
	$repositorypan = $em->getRepository(Panier::class);
	foreach($nbcategorie as $scat)
	{
		$cours_inval = $repository->listeProduitInval($scat->getId());
		$scat->setnbprodinval(count($cours_inval));
		
		$cours_val = $repository->listeProduitVal($scat->getId());
		$scat->setNbprodval(count($cours_val));
		
		$cours_pan = $repositorypan->getPanierScategorie($scat->getId());
		$scat->setNbprodachive(count($cours_pan));
	}
	return $this->render('Theme/Users/Adminuser/Categorie/listecategorie.html.twig',
	array('cat'=>$cat,'liste_scat'=>$nbcategorie,'nombrepage' => ceil(count($nbcategorie)/10),'pagesuivante'=>$pagesuivante));
}

public function modificationcategorie(Categorie $categorie, GeneralServicetext $service, Request $request)
{
	$em = $this->getDoctrine()->getManager();
    $formcat = $this->createForm(CategorieType::class, $categorie);
	if ($request->getMethod() == 'POST'){
		$formcat->handleRequest($request);
		$categorie->setServicetext($service);
		if ($formcat->isValid()){
			$em->flush();
			$this->get('session')->getFlashBag()->add('information','Modification effectuée avec succès');
		}else{
			$this->get('session')->getFlashBag()->add('information','Une ereur a été rencontrée!');
		}
	}
	return $this->render('Theme/Users/Adminuser/Categorie/modificationcategorie.html.twig',
	array('form'=>$formcat->createView(),'cat'=>$categorie));
}

public function Supprimercategorie(Categorie $categorie)
{
	$em = $this->getDoctrine()->getManager();
	if(count($categorie->getSouscategories()) == 0)
	{
		$em->remove($categorie);
		$em->flush();
		$this->get('session')->getFlashBag()->add('information','Suppression effectuée avec succès');
	}else{
		$this->get('session')->getFlashBag()->add('information','Echec ! Cette catégorie contient les sous-catégories, Supprimez lès en premier.');
	}
	return $this->redirect($this->generateUrl('users_adminuser_categorisation_produit_plateforme'));
}

public function Supprimersouscategorie(Souscategorie $scategorie)
{
	$em = $this->getDoctrine()->getManager();
	if(count($scategorie->getProduits()) == 0)
	{
		$em->remove($scategorie);
		$em->flush();
		$this->get('session')->getFlashBag()->add('information','Suppression effectuée avec succès');
	}else{
		$this->get('session')->getFlashBag()->add('information','Echec ! Cette sous-catégorie contient les produits, Supprimez lès en premier.');
	}
	return $this->redirect($this->generateUrl('users_adminuser_categorisation_produit_plateforme'));
}

public function listeproduitadmin(Souscategorie $souscategorie, GeneralServicetext $service, Request $request)
{
	$em = $this->getDoctrine()->getManager();
	$produit = new Produit($service);
	$formpro = $this->createForm(ProduitType::class, $produit);
	if($request->getMethod() == 'POST')
	{
		$liste_produit = $em->getRepository(Produit::class)
	                    ->myFindBy($souscategorie->getId());
		$nbprodsouscat = $this->params->get('nbproduitsouscat');
		if($formpro->isValid()){
			$em->flush();
			$this->get('session')->getFlashBag()->add('information','Rencontre enregirtrée avec succès !');
		}else{
			 $this->get('session')->getFlashBag()->add('information','Données invalides.');
		}
	}
	
	$liste_produit = $em->getRepository(Produit::class)
	                    ->myFindBy($souscategorie->getId());
	$form = $this->createFormBuilder()->getForm();
	return $this->render('Theme/Users/Adminuser/Categorie/listeproduitadmin.html.twig', 
	array('souscategorie'=>$souscategorie,'liste_produit'=>$liste_produit,'form'=>$form->createView()));
}

public function listeproduitinvalide(Souscategorie $souscategorie)
{
	$em = $this->getDoctrine()->getManager();
	$liste_produit = $em->getRepository(Produit::class)
	                    ->listeProduitInval($souscategorie->getId());

	$form = $this->createFormBuilder()->getForm();
	return $this->render('Theme/Users/Adminuser/Categorie/listeproduitinvalide.html.twig', 
	array('souscategorie'=>$souscategorie,'liste_produit'=>$liste_produit,'form'=>$form->createView()));
}

public function demandegagnant(Souscategorie $souscategorie, GeneralServicetext $service)
{
	$em = $this->getDoctrine()->getManager();
	$souscategorie->setServicetext($service);
	$liste_produit = $em->getRepository(Produit::class)
	                    ->findBySouscategorie($souscategorie->getId());
	$listevalide = true;
	foreach($liste_produit as $produit)
	{
		if($produit->getUpdatescore() == false)
		{
			$listevalide = false;
			break;
		}
	}
	if($listevalide == true and $souscategorie->getFermer() == false)
	{
		$liste_panier = $em->getRepository(Panier::class)
	                       ->getPanierScategorie($souscategorie->getId());
		foreach($liste_panier as $panier)
		{
			$nbgagner = 0;
			foreach($panier->getProduitpaniers() as $propan)
			{
				$produit = $em->getRepository(Produit::class)
	                          ->find($propan->getProduit()->getId());
				if($produit !=  null and $produit->getIssue() == $propan->getQuantite())
				{
					$nbgagner = $nbgagner + 1;
				}
			}
			$panier->setNbgagner($nbgagner);
			$em->flush();
		}
		
		//---------------------------------------------------
		$panier_gagnant = $em->getRepository(Panier::class) //Les gagnants de trois matchs
	                       ->getPanierScategorieGagnant($souscategorie->getId());
		$nbgagnant = 0; //nombre de gaganants
		foreach($panier_gagnant as $gagnant)
		{
			$nbgagnant = $nbgagnant + $gagnant->getNbticket();
		}
		$motiercagnote = floor(($souscategorie->getCagnote()*50)/100); //montant à partager
		if($nbgagnant > 0)
		{
			$montantgagnant = floor($motiercagnote/$nbgagnant); //montant par gagnant. 
		}else{
			$montantgagnant = 0;
		}
		foreach($panier_gagnant as $gagnant)
		{
			$gagnant->getUser()->setSoldetransit($gagnant->getUser()->getSoldetransit() + ($montantgagnant * $gagnant->getNbticket()));
			$gagnant->setGain($gagnant->getGain() + ($montantgagnant * $gagnant->getNbticket()));
		}
		$souscategorie->setNbgagnant($nbgagnant);
		$em->flush();
		
		
		//---------------------------------------------------
		$panier_gagnant = $em->getRepository(Panier::class) //Les gagnants de quatre maths
	                         ->getPanierScategorieGagnantQuartre($souscategorie->getId());
		$nbgagnant = 0; //nombre de gaganants
		foreach($panier_gagnant as $gagnant)
		{
			$nbgagnant = $nbgagnant + $gagnant->getNbticket();
		}
		$motiercagnote = floor(($souscategorie->getCagnote()*40)/100); //montant à partager
		if($nbgagnant > 0)
		{
			$montantgagnant = floor($motiercagnote/$nbgagnant); //montant par gagnant. 
		}else{
			$montantgagnant = 0;
		}
		foreach($panier_gagnant as $gagnant)
		{
			$gagnant->getUser()->setSoldetransit($gagnant->getUser()->getSoldetransit() + ($montantgagnant * $gagnant->getNbticket()));
			$gagnant->setGain($gagnant->getGain() + ($montantgagnant * $gagnant->getNbticket()));
		}
		$em->flush();
		
		
		//---------------------------------------------------
		$panier_gagnant = $em->getRepository(Panier::class) //Les gagnants de cinq maths
	                         ->getPanierScategorieGagnantCinq($souscategorie->getId());
		$nbgagnant = 0; //nombre de gaganants
		foreach($panier_gagnant as $gagnant)
		{
			$nbgagnant = $nbgagnant + $gagnant->getNbticket();
		}
		$motiercagnote = floor(($souscategorie->getCagnote()*10)/100); //montant à partager
		if($nbgagnant > 0)
		{
			$montantgagnant = floor($motiercagnote/$nbgagnant); //montant par gagnant. 
		}else{
			$montantgagnant = 0;
		}
		foreach($panier_gagnant as $gagnant)
		{
			$gagnant->getUser()->setSoldetransit($gagnant->getUser()->getSoldetransit() + ($montantgagnant * $gagnant->getNbticket()));
			$gagnant->setGain($gagnant->getGain() + ($montantgagnant * $gagnant->getNbticket()));
		}
		$souscategorie->setFermer(true);
		$em->flush();
		
		$liste_gagnant = $em->getRepository(User::class) //Les gagnants de cinq maths
	                         ->findAllGagnant();
		foreach($liste_gagnant as $user)
		{
			$total = $user->getSoldetransit();
			$principal = floor(($user->getSoldetransit()*25)/100);
			$gain = $user->getSoldetransit() - $principal;
			$user->setSoldeprincipal($user->getSoldeprincipal() + $principal);
			$user->setSoldegain($user->getSoldegain() + $gain);
			$user->setNbgaim($user->getNbgaim() + 1);
			$user->setSoldetransit(0);
			
			//envoie d'email
			$siteweb = $this->params->get('siteweb');
			$sitename = $this->params->get('sitename');
			$emailadmin = $this->params->get('emailadmin');
			
			$notif = new Notification();
			$notif->setTitre('Vous avez parié et gagné sur '.$sitename.' !');
			$notif->setContenu('Votre ticket de la journée du'.$souscategorie->getDatetext().' vous a permis de gagner une somme de '.$total.' Fcfa, repartie comme suit: </br> Compte principale : '.$principal.'Fcfa, </br> Compte Gains: '.$gain.'Fcfa');
			$notif->setUser($user);
			$em->persist($notif);
			$em->flush();
			
			
			if($service->email($user->getUsername()))
			{
				$response = $this->_servicemail->sendNotifEmail(
					$user->name(30), //Nom du destinataire
					$user->getUsername(), //Email Destinataire
					'Vous avez parié et gagné sur '.$sitename.' !', //Objet de l'email
					'Vous avez parié et gagné sur '.$sitename.' !', //Grand Titre de l'email
					'Votre ticket de la journée du'.$souscategorie->getDatetext().' vous a permis de gagner une somme de '.$total.' Fcfa, repartie comme suit: </br> Compte principale : '.$principal.'Fcfa, </br> Compte Gains: '.$gain.'Fcfa',  //Contenu de l'email
					 ''  //Lien à suivre
				);
			}
		}
		$this->get('session')->getFlashBag()->add('information','Succès !');
		return $this->redirect($this->generateUrl('users_adminuser_categorisation_produit_plateforme',array('id'=>$souscategorie->getId())));		
	}else{
		$this->get('session')->getFlashBag()->add('information','Echec, L\'issue de toutes les rencontres de cette liste n\'est pas encore connue.');
		return $this->redirect($this->generateUrl('users_adminuser_categorisation_produit_plateforme',array('id'=>$souscategorie->getId())));
	}
}

public function listeprodarchive(Souscategorie $souscategorie)
{
	$em = $this->getDoctrine()->getManager();
	$panier_trois = $em->getRepository(Panier::class)
	                    ->getPanierTroisGagnant($souscategorie->getId());
	$panier_quatre = $em->getRepository(Panier::class)
	                    ->getPanierQuartreGagnant($souscategorie->getId());
	$panier_cinq = $em->getRepository(Panier::class)
	                    ->getPanierCinqGagnant($souscategorie->getId());
	$form = $this->createFormBuilder()->getForm();
	return $this->render('Theme/Users/Adminuser/Categorie/listeprodarchive.html.twig', 
	array('souscategorie'=>$souscategorie,'panier_trois'=>$panier_trois,'panier_quatre'=>$panier_quatre,'panier_cinq'=>$panier_cinq,'form'=>$form->createView()));
}

public function addproduitaccueil(Produit $produit, GeneralServicetext $service, Request $request)
{
	$em = $this->getDoctrine()->getManager();
	$form = $this->createFormBuilder()->getForm();
	if ($request->getMethod() == 'POST'){
	$form->handleRequest($request);
    if ($form->isValid()){
		if($produit->getValide() == true)
		{
			if($produit->getAvant() == true)
			{
				$produit->setAvant(false);
			}else{
				$produit->setAvant(true);
			}
			$this->get('session')->getFlashBag()->add('information','Modification effectuée avec succès');
		}else{
			$this->get('session')->getFlashBag()->add('information','Echec ! état du produit invalide.');
		}
		$em->flush();
		return $this->redirect($this->generateUrl('users_adminuser_liste_produit_souscategorie',array('id'=>$produit->getSouscategorie()->getId())));
	}else{
		$this->get('session')->getFlashBag()->add('information','Une ereur a été rencontrée!');
	}
	}
	$this->get('session')->getFlashBag()->add('upgrade_prod',$produit->getId());
	$this->get('session')->getFlashBag()->add('upgrade_prod',$produit->getTitre());

	return $this->redirect($this->generateUrl('users_adminuser_liste_produit_souscategorie',array('id'=>$produit->getSouscategorie()->getId())));
}

public function publierproduitadmin(Produit $produit, $guide, GeneralServicetext $service, Request $request)
{
	$em = $this->getDoctrine()->getManager();

	$form = $this->createFormBuilder()->getForm();
	if ($request->getMethod() == 'POST'){
	$form->handleRequest($request);
    if ($form->isValid()){
	if($guide == 0)
	{
		if($produit->getGuide() == false)
		{
			if($produit->getValide() == true)
			{
				$produit->setValide(false);
			}else{
				$produit->setValide(true);
			}
			$this->get('session')->getFlashBag()->add('information','Modification effectuée avec succès');
		}else{
			$this->get('session')->getFlashBag()->add('information','Echec ! Vous ne pouvez pas publier un guide de rédaction de cours.');
		}
	}else{
		if($produit->getGuide() == true)
		{
			$produit->setGuide(false);
		}else{
			$produit->setGuide(true);
		}
		$this->get('session')->getFlashBag()->add('information','Modification effectuée avec succès !');
	}
	
	$em->flush();
	
	return $this->redirect($this->generateUrl('users_adminuser_prod_invalide_courant_souscategorie',array('id'=>$produit->getSouscategorie()->getId())));
	}else{
	$this->get('session')->getFlashBag()->add('information','Une ereur a été rencontrée!');
	}
	}
	$this->get('session')->getFlashBag()->add('valide_prod',$produit->getId());
	$this->get('session')->getFlashBag()->add('valide_prod',$guide);
	$this->get('session')->getFlashBag()->add('valide_prod',$produit->getTitre());
	
	if($produit->getValide() == true)
	{
		return $this->redirect($this->generateUrl('users_adminuser_liste_produit_souscategorie',array('id'=>$produit->getSouscategorie()->getId())));
	}else{
		return $this->redirect($this->generateUrl('users_adminuser_prod_invalide_courant_souscategorie',array('id'=>$produit->getSouscategorie()->getId())));
	}
}

public function altersouscategorie(Souscategorie $souscategorie, GeneralServicetext $service, Request $request)
{
	$em = $this->getDoctrine()->getManager();
    $formsouscat = $this->createForm(SouscategorieType::class, $souscategorie);
	if ($request->getMethod() == 'POST'){
		$formsouscat->handleRequest($request);
		$souscategorie->setServicetext($service);
		if ($formsouscat->isValid()){
			$em->flush();
			$this->get('session')->getFlashBag()->add('information','Modification effectuée avec succès');
		}else{
			$this->get('session')->getFlashBag()->add('information','Une ereur a été rencontrée!');
		}
	}
	return $this->render('Theme/Users/Adminuser/Categorie/modificationsouscategorie.html.twig',
	array('formcat'=>$formsouscat->createView(),'scat'=>$souscategorie));
}

public function catalogue()
{
	$em = $this->getDoctrine()->getManager();
	$liste_cat = $em->getRepository(Categorie::class)
	                ->myFindAll();
	$repositorie = $em->getRepository(Produit::class);
	$plus_vendu = $repositorie->topProduit(5);
	$plus_like = $repositorie->topLike(5);
	return $this->render('Theme/Produit/Produit/Categorie/catalogue.html.twig',
	array('liste_cat'=>$liste_cat,'plus_vendu'=>$plus_vendu,'plus_like'=>$plus_like));
}
}
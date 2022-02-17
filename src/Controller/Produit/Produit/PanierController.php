<?php
/*(c) Noel Kenfack <noel.kenfack@yahoo.fr> Février 2015
*/
namespace App\Controller\Produit\Produit;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Users\User\User;
use App\Entity\Produit\Produit\Panier;
use App\Entity\Produit\Produit\Produitpanier;
use App\Entity\Produit\Produit\Composexercice;
use App\Entity\Produit\Produit\Compospratique;
use App\Form\Produit\Produit\ComposexerciceType;
use App\Form\Produit\Produit\CompospratiqueType;
use App\Entity\Produit\Produit\Produit;
use App\Entity\Produit\Produit\Exercicepartie;
use App\Entity\Produit\Produit\Pratiquechapitre;
use App\Entity\Users\User\Notification;

use App\Service\AfMail\Afmail;
use App\Service\AfMail\fileAttachment;
use App\Service\AfPdf\PDF;

use App\Service\Servicetext\GeneralServicetext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use App\Service\Email\Singleemail;
use App\Entity\Produit\Produit\Categorie;
use App\Entity\Produit\Produit\Souscategorie;
use App\Entity\Produit\Produit\Chapitrecours;
use App\Entity\Produit\Service\Ville;
use App\Entity\Produit\Produit\Partiecours;

class PanierController extends AbstractController
{

private $params;
private $_servicemail;

public function __construct(ParameterBagInterface $params, Singleemail $servicemail)
{
	$this->params = $params;
	$this->_servicemail = $servicemail;
}

public function validationpayement(User $user, GeneralServicetext $service)
{
	$em = $this->getDoctrine()->getManager();
	$panier = $em->getRepository(Panier::class)
				 ->findOneBy(array('user'=>$user,'payer'=>0));
	$coutpartie = 200;
	if($this->getUser() == $user and $panier != null and isset($_POST['montantparier']) and isset($_POST['monpanier']))
	{
		$scat = null;
		if($_POST['monpanier'] == $panier->numFacture() and count($panier->getProduitpaniers()) == 5)
		{
			$panier->getUser()->setSoldeprincipal($panier->getUser()->getSoldeprincipal() - ($_POST['montantparier']*$coutpartie));
			$panier->setNbticket($_POST['montantparier']);
			$panier->setMontant($coutpartie);
			$panier->setPayer(true);
			$panier->setDate(new \Datetime());
		
			$soustraire = 50*$_POST['montantparier'];
			$reste = $_POST['montantparier']*$coutpartie - $soustraire;

			foreach($panier->getProduitpaniers() as $propan)
			{
				$scat = $propan->getProduit()->getSouscategorie();
				break;
			}
			
			if($scat != null)
			{
				$scat->setCagnote($scat->getCagnote() + $reste);
				$scat->setGain($scat->getGain() + $soustraire);
				$scat->setServicetext($service);
			}
			$em->flush();
			
			$notif = new Notification();
			$notif->setTitre('Votre ticket N° '.$panier->numFacture().' a été validé avec succès !');
			$notif->setContenu('Nous vous tiendrons informer de vos gains une fois les résultats des rencontres de votre ticket connus.');
			$notif->setUser($panier->getUser());
			$em->persist($notif);
			$em->flush();
			
			//envoie d'email
			$siteweb = $this->params->get('siteweb');
			$sitename = $this->params->get('sitename');
			$emailadmin = $this->params->get('emailadmin');
			if($service->email($panier->getUser()->getUsername()))
			{
				$response = $this->_servicemail->sendNotifEmail(
					$panier->getUser()->name(40), //Nom du destinataire
					$panier->getUser()->getUsername(), //Email Destinataire
					'Votre ticket N° '.$panier->numFacture().' a été validé avec succès !', //Objet de l'email
					'Votre ticket N° '.$panier->numFacture().' a été validé avec succès !', //Grand Titre de l'email
					'Nous vous tiendrons informer de vos gains une fois les résultats des rencontres de votre ticket connus.',  //Contenu de l'email
					 ''  //Lien à suivre
				);
			}
		}else{
			if(count($panier->getProduitpaniers()) != 5)
			{
				$this->get('session')->getFlashBag()->add('information','Vous devez avoir exactement 5rencontres sur votre ticket avant de valider.');
			}else{
				$this->get('session')->getFlashBag()->add('information','Erreur ! Les données reçues sont invalides.');
			}
		}
		
		if($scat != null)
		{
			return $this->redirect($this->generateUrl('produit_produit_liste_produit_souscategorie', array('id'=>$scat->getCategorie()->getId())));
		}
	}else{
		$this->get('session')->getFlashBag()->add('information','Erreur ! Les données reçues sont invalides.');
	}
	return $this->redirect($this->generateUrl('produit_produit_liste_produit_souscategorie'));
}

public function paniernonlivrer($page)
{
	$em = $this->getDoctrine()->getManager();
	$liste_panier = $em->getRepository(Panier::class)
					   ->listepanierinvalide($page, 10);
	$formsupp = $this->createFormBuilder()->getForm();
	return $this->render('Theme/Users/Adminuser/Panier/paniernonlivrer.html.twig',
	array('liste_panier'=>$liste_panier,'formsupp'=>$formsupp->createView(), 'nombrepage' => ceil(count($liste_panier)/10),'page'=>$page));
}

public function contenupanier(Panier $panier)
{
	$em = $this->getDoctrine()->getManager();
	$liste_produit = $em->getRepository(Produitpanier::class)
				       ->myfindBy($panier->getId());
	return $this->render('Theme/Users/Adminuser/Panier/contenupanier.html.twig', 
	array('liste_produit'=>$liste_produit,'panier'=>$panier));
}

public function detailpartie()
{
	$em = $this->getDoctrine()->getManager();
	$session = $this->get('session');
	$id_categorie = $session->get('id_categorie');
	if($id_categorie == 0 or $id_categorie == null)
	{
		$categorie = $em->getRepository(Categorie::class)
	                    ->myFindOne();
	}else{
		$categorie = $em->getRepository(Categorie::class)
	                    ->find($id_categorie);
	}
	
	$liste_scat = new \Doctrine\Common\Collections\ArrayCollection();
	if($categorie != null)
	{
		$liste_scat = $em->getRepository(Souscategorie::class)
	                    ->findValideScatcourantCat($categorie->getId(),1,1);
	}
	
	$souscategorie = null;
	$compt = 1;
	foreach($liste_scat as $scat)
	{
		if($compt == 1)
		{
			$souscategorie = $scat;
		}
		$compt++;
	}
	if($souscategorie != null)
	{
		if($souscategorie->getDatetext() == date('d').'-'.date('m').'-'.date('Y'))
		{
			$souscategorie->setActive(true);
		}else{
			$souscategorie->setActive(false);
		}
	}
	if($this->getUser() != null)
	{
		
	$panier = $em->getRepository(Panier::class)
	                 ->findOneBy(array('user'=>$this->getUser(),'payer'=>0));
	}else{
	$panier = null;	
	}
	$scatpan = null;
	if($panier != null)
	{
		foreach($panier->getProduitpaniers() as $propan)
		{
			$scatpan = $propan->getProduit()->getSouscategorie();
			if($scatpan->getDatetext() == date('d').'-'.date('m').'-'.date('Y'))
			{
				if(($propan->getProduit()->getHeure() > date('H')) or ($propan->getProduit()->getHeure() == date('H') and $propan->getProduit()->getMinute() >= date('i')))
				{
					$scatpan->setActive(true);
				}else{
					$scatpan->setActive(false);
					break;
				}
			}else{
				$scatpan->setActive(false);
				break;
			}
		}
	}
	return $this->render('Theme/Produit/Produit/Panier/detailpartie.html.twig', 
	array('souscategorie'=>$souscategorie,'panier'=>$panier,'scatpan'=>$scatpan));
}

public function livraisonpanier(Panier $panier, Request $request)
{
	$em = $this->getDoctrine()->getManager();
	$formsupp = $this->createFormBuilder()->getForm(); 
	
	if ($request->getMethod() == 'POST'){
		if($panier->getLivrer() == false)
		{
			$panier->setLivrer(true);
			$panier->setPayer(true);
			$em->flush();
			$this->get('session')->getFlashBag()->add('information','Validation effectuée avec succès !');
		}
	}else{
		$this->get('session')->getFlashBag()->add('valide_prod',$panier->getId());
	    $this->get('session')->getFlashBag()->add('valide_prod',$panier->numFacture());
	}
	return $this->redirect($this->generateUrl('users_adminuser_liste_panier_non_livrer'));
}

public function panierlivrer()
{
	$em = $this->getDoctrine()->getManager();
	$liste_panier = $em->getRepository(Panier::class)
				       ->findBy(array('payer'=>1,'livrer'=>1),array('date'=>'desc'));
	return $this->render('Theme/Users/Adminuser/Panier/panierlivrer.html.twig', 
	array('liste_panier'=>$liste_panier));
}

public function detailpanieruser(Panier $panier, Produit $produit, Generalservicetext $service)
{
	$em = $this->getDoctrine()->getManager();
	$liste_produit = $em->getRepository(Produitpanier::class)
				       ->myfindBy($panier->getId());
	$topcat = $em->getRepository(Souscategorie::class)
	             ->topsouscategorie(8);
					 
	$liste_part = $em->getRepository(Partiecours::class)
	                      ->findBy(array('produit'=>$produit), array('rang'=>'asc'));
	foreach($liste_part as $part)
	{
		$part->setEm($em);
	}
	
	$liste_chapter = $em->getRepository(Chapitrecours::class)
						->listechapitrecours($produit->getId());
	$bareme = $service->getBareme();

	foreach($liste_chapter as $chap)
	{
		$chap->setEm($em);
	}
	
	$prodpan = null;
	foreach($panier->getProduitpaniers() as $propan)
	{
		if($propan->getProduit() == $produit)
		{
			$prodpan = $propan;
			break;
		}
	}

	return $this->render('Theme/Produit/Produit/Panier/detailpanieruser.html.twig',
	array('liste_produit'=>$liste_produit,'prodpan'=>$prodpan,'user'=>$panier->getUser(),
	'bareme'=>$bareme,'produit'=>$produit,'panier'=>$panier,'liste_part'=>$liste_part,'liste_chapter'=>$liste_chapter,
	'topcat'=>$topcat));
}

public function detailexercice(Exercicepartie $exercice, Panier $panier, $ident, GeneralServicetext $service)
{
	$chapitre = $exercice->getChapitrecours();
	$em = $this->getDoctrine()->getManager();
	if(($panier->getValide() == true and $panier->getChapitrecours() == null) or ($panier->getValide() == true and $panier->getChapitrecours() == $chapitre) or $this->getUser() == $chapitre->getPartiecours()->getProduit()->getUser())
	{
		$accessressource = true;
	}else{
		$accessressource = false;
	}
	
	$liste_chapter = $em->getRepository(Chapitrecours::class)
						->listechapitrecours($chapitre->getPartiecours()->getProduit()->getId());
	$prechapter = null;			
	foreach($liste_chapter as $chapter)
	{
		if($chapter == $chapitre)
		{
			break;
		}else{
			$prechapter = $chapter;
			$chapter->setEm($em);
		}
	}
	$chapitre->setEm($em);
	
	$prodpan = null;
	foreach($panier->getProduitpaniers() as $propan)
	{
		if($propan->getProduit() == $chapitre->getPartiecours()->getProduit())
		{
			$prodpan = $propan;
			break;
		}
	}
	
	$composexercice = null;
	$formmodif = null;
	if($prodpan != null)
	{
		$composexercice = $em->getRepository(Composexercice::class)
						     ->findOneBy(array('exercicepartie'=>$exercice,'produitpanier'=>$prodpan));
		$formmodif = $this->createForm(ComposexerciceType::class, $composexercice);
		$formmodif = $formmodif->createView();
	}
	$compos = new Composexercice($service);
	$form = $this->createForm(ComposexerciceType::class, $compos); 
	$bareme = $service->getBareme();
	$noteminexo = $this->params->get('noteminexo');
	$repeat = $this->params->get('repeattime');
	$waittime = 0;
	if($composexercice != null)
	{
		$waittime = floor($repeat - floor((time() - $composexercice->getLastvalidation())/3600));
	}
	return $this->render('Theme/Produit/Produit/Panier/detailexercice.html.twig',
	array('exercice'=>$exercice,'chapitre'=>$chapitre,'waittime'=>$waittime,'bareme'=>$bareme,'form'=>$form->createView(),
	'formmodif'=>$formmodif,'composexercice'=>$composexercice,'prechapter'=>$prechapter,'prodpan'=>$prodpan,'noteminexo'=>$noteminexo,
	'panier'=>$panier,'accessressource'=>$accessressource,'ident'=>$ident));
}

public function addnewcopieexercice(Exercicepartie $exercice, Produitpanier $prodpan, GeneralServicetext $service, Request $request)
{
	$em = $this->getDoctrine()->getManager();
	$compos = new Composexercice($service);
	$form = $this->createForm(ComposexerciceType::class, $compos); 

	if($request->getMethod() == 'POST' and $this->getUser() == $prodpan->getPanier()->getUser()){
		$form->handleRequest($request);
		if($form->isValid()){
			$compos->setProduitpanier($prodpan);
			$compos->setExercicepartie($exercice);
			$em->persist($compos);
			$em->flush();

			$this->get('session')->getFlashBag()->add('information','Votre copie a été enregistrée avec succès !');

		}else{
			$this->get('session')->getFlashBag()->add('information','Une erreur a été rencontrée !!!');
		}
	}
	return $this->redirect($this->generateUrl('users_user_detail_panier_user', 
	array('panier'=>$prodpan->getPanier()->getId(),'produit'=>$prodpan->getProduit()->getId())));
}

public function ajoutcopietp(Pratiquechapitre $pratique, Produitpanier $prodpan, GeneralServicetext $service, Request $request)
{
	$em = $this->getDoctrine()->getManager();
	$compos = new Compospratique($service);
	$form = $this->createForm(CompospratiqueType::class, $compos);
	if($request->getMethod() == 'POST' and $this->getUser() == $prodpan->getPanier()->getUser()){
		$form->handleRequest($request);
		if($form->isValid()){
			$compos->setProduitpanier($prodpan);
			$compos->setPratiquechapitre($pratique);
			$em->persist($compos);
			$em->flush();

			$this->get('session')->getFlashBag()->add('information','Votre copie a été enregistrée avec succès !');

		}else{
			$this->get('session')->getFlashBag()->add('information','Une erreur a été rencontrée !!!');
		}
	}
	return $this->redirect($this->generateUrl('users_user_detail_panier_user', array('panier'=>$prodpan->getPanier()->getId(),'produit'=>$prodpan->getProduit()->getId())));
}

public function altercopieexercice(Composexercice $compos, GeneralServicetext $service, Request $request)
{
	$em = $this->getDoctrine()->getManager();
	$prodpan = $compos->getProduitpanier();
	$formmodif = $this->createForm(ComposexerciceType::class, $compos); 
	if($request->getMethod() == 'POST' and $this->getUser() == $prodpan->getPanier()->getUser()){
		$formmodif->handleRequest($request);
		$compos->setServicetext($service);
		if($formmodif->isValid()){
			$em->flush();
			$this->get('session')->getFlashBag()->add('information','Modification de votre copie effectuée avec succès !');
		}else{
			$this->get('session')->getFlashBag()->add('information','Une erreur a été rencontrée !!!');
		}
	}
	return $this->redirect($this->generateUrl('users_user_detail_panier_user', 
	array('panier'=>$prodpan->getPanier()->getId(),'produit'=>$prodpan->getProduit()->getId())));
}

public function modificationcopietp(Compospratique $compos, Request $request)
{
	$em = $this->getDoctrine()->getManager();
	$prodpan = $compos->getProduitpanier();
	$formmodif = $this->createForm(CompospratiqueType::class, $compos); 
	if($request->getMethod() == 'POST' and $this->getUser() == $prodpan->getPanier()->getUser()){
		$formmodif->handleRequest($request);
		$compos->setServicetext($service);
		if($formmodif->isValid()){
			$em->flush();

			$this->get('session')->getFlashBag()->add('information','Modification de votre copie effectuée avec succès !');

		}else{
			$this->get('session')->getFlashBag()->add('information','Une erreur a été rencontrée !!!');
		}
	}
	return $this->redirect($this->generateUrl('users_user_detail_panier_user', 
	array('panier'=>$prodpan->getPanier()->getId(),'produit'=>$prodpan->getProduit()->getId())));
}

public function updatenoteexercice(Composexercice $compos)
{
	$em = $this->getDoctrine()->getManager();
	if(isset($_POST['val']) and $compos->getProduitpanier()->getProduit()->getUser() == $this->getUser())
	{
		$compos->setNote($_POST['val']);
		$compos->setLastvalidation(time());
		$em->flush();
		echo 1;
		exit;
	}else{
		echo 1;
		exit;
	}
}

public function updatenotetp(Compospratique $compos)
{
	$em = $this->getDoctrine()->getManager();
	if(isset($_POST['val']) and $compos->getProduitpanier()->getProduit()->getUser() == $this->getUser())
	{
		$compos->setNote($_POST['val']);
		$compos->setLastvalidation(time());
		$em->flush();
		echo 1;
		exit;
	}else{
		echo 1;
		exit;
	}
}

public function detailpartique(Pratiquechapitre $pratique, Panier $panier, $ident, GeneralServicetext $service)
{
	$chapitre = $pratique->getChapitrecours();
	$em = $this->getDoctrine()->getManager();
	if(($panier->getValide() == true and $panier->getChapitrecours() == null) or ($panier->getValide() == true and $panier->getChapitrecours() == $chapitre) or $this->getUser() == $chapitre->getPartiecours()->getProduit()->getUser())
	{
		$accessressource = true;
	}else{
		$accessressource = false;
	}
	
	$liste_chapter = $em->getRepository(Chapitrecours::class)
						->listechapitrecours($chapitre->getPartiecours()->getProduit()->getId());
	$prechapter = null;			
	foreach($liste_chapter as $chapter)
	{
		if($chapter == $chapitre)
		{
			break;
		}else{
			$prechapter = $chapter;
			$chapter->setEm($em);
		}
	}
	$chapitre->setEm($em);
	
	$prodpan = null;
	foreach($panier->getProduitpaniers() as $propan)
	{
		if($propan->getProduit() == $chapitre->getPartiecours()->getProduit())
		{
			$prodpan = $propan;
			break;
		}
	}
	
	$compospratique = null;
	$formmodif = null;
	if($prodpan != null)
	{
		$compospratique = $em->getRepository(Compospratique::class)
						     ->findOneBy(array('pratiquechapitre'=>$pratique,'produitpanier'=>$prodpan));
		$formmodif = $this->createForm(CompospratiqueType::class, $compospratique);
		$formmodif = $formmodif->createView();
	}

	$compos = new Compospratique($service);
	$form = $this->createForm(CompospratiqueType::class, $compos); 
	$bareme = $service->getBareme();
	$noteminexo = $this->params->get('noteminexo');
	$repeat = $this->params->get('repeattime');
	$waittime = 0;
	if($compospratique != null)
	{
		$waittime = floor($repeat - floor((time() - $compospratique->getLastvalidation())/3600));
	}
	return $this->render('Theme/Produit/Produit/Panier/detailpartique.html.twig',
	array('pratique'=>$pratique,'chapitre'=>$chapitre,'waittime'=>$waittime,'bareme'=>$bareme,'form'=>$form->createView(),
	'formmodif'=>$formmodif,'compospratique'=>$compospratique,'prechapter'=>$prechapter,'prodpan'=>$prodpan,'noteminexo'=>$noteminexo,
	'panier'=>$panier,'accessressource'=>$accessressource,'ident'=>$ident));
}

public function modifierlieulivraison(Panier $panier)
{
	$em = $this->getDoctrine()->getManager();
	if(isset($_POST['ville']))
	{
		$ville = $em->getRepository(Ville::class)
				       ->findOneBy(array('nom'=>$_POST['ville']));
			if($ville != null)
			{
				$panier->setVille($ville);
				$em->flush();
			}
	}
	return $this->redirect($this->generateUrl('produit_produit_reglement_commande_du_panier', 
	array('id'=>$this->getUser()->getId())));
}

public function telechargerpanier(Panier $panier)
{
	$nameoffile = '/../../../'.$panier->getWebPath();
	return $this->redirect($nameoffile);
}

public function supprimerpanier(Panier $panier, Request $request)
{
	$em = $this->getDoctrine()->getManager();
	$formsupp = $this->createFormBuilder()->getForm(); 
	if ($request->getMethod() == 'POST'){
		foreach($panier->getProduitpaniers() as $propan)
		{
			$em->remove($propan);
			if(file_exists ($panier->getUploadRootDir().'/'.$panier->numFacture().'.pdf'))
			{
			unlink($panier->getUploadRootDir().'/'.$panier->numFacture().'.pdf');
			}
			$em->remove($panier);
			$em->flush();
		}
		$this->get('session')->getFlashBag()->add('information','Réservation Supprimé avec succès !');
	}else{
		$this->get('session')->getFlashBag()->add('supprime_prod',$panier->getId());
	    $this->get('session')->getFlashBag()->add('supprime_prod',$panier->numFacture());
		}
	return $this->redirect($this->generateUrl('users_adminuser_liste_panier_non_livrer'));
}
}
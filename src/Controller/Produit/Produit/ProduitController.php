<?php
/*
	(c) Noel Kenfack <noel.kenfack@yahoo.fr> Février 2015
*/
namespace App\Controller\Produit\Produit;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Produit\Produit\Produit;
use App\Entity\Produit\Produit\Partiecours;
use App\Entity\Produit\Produit\Coutlivraison;
use App\Entity\Produit\Produit\Panier;
use App\Entity\Produit\Service\Quartier;
use App\Entity\Produit\Service\Ville;
use App\Entity\Users\User\User;
use App\Entity\Users\User\Sujetepingle;
use App\Entity\Produit\Produit\Produitpanier;
use App\Entity\Produit\Produit\Imgproduit;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Form\Produit\Produit\ProduitType;
use App\Form\Produit\Produit\PartiecoursType;
use App\Form\Produit\Produit\CoutlivraisonType;
use App\Entity\Produit\Produit\Categorie;
use App\Entity\Produit\Produit\Souscategorie;
use App\Entity\General\Template\Recherche;
use App\Entity\Users\User\Newsletter;

use App\Service\AfMail\Afmail;
use App\Service\AfMail\fileAttachment;
use App\Service\AfPdf\PDF;

use App\Entity\Produit\Produit\Animationproduit;
use App\Entity\Users\User\Imgslide;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use App\Entity\Produit\Service\Produitformation;
use App\Service\Servicetext\GeneralServicetext;
use App\Entity\Produit\Service\Commentaireblog;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use App\Service\Email\Singleemail;

class ProduitController extends AbstractController
{
private $params;
private $_servicemail;

public function __construct(ParameterBagInterface $params, Singleemail $servicemail)
{
	$this->params = $params;
	$this->_servicemail = $servicemail;
}

public function ajouterproduit(GeneralServicetext $service, Request $request)
{
	$em = $this->getDoctrine()->getManager();
	$produit = new Produit($service);
	$formpro = $this->createForm(ProduitType::class, $produit); 

	if($request->getMethod() == 'POST' and $this->getUser() != null){
		$formpro->handleRequest($request);
		if($formpro->isValid()){
			
			if(isset($_POST['dureeminute']))
			{
				$produit->setDureeminute($_POST['dureeminute']);
			}
			if(isset($_POST['dureeseconde']))
			{
				$produit->setDureeseconde($_POST['dureeseconde']);
			}
			if(isset($_POST['typecours']))
			{
				$produit->setTypecours($_POST['typecours']);
			}
			
			if($produit->getImgproduit() != null)
			{
				$produit->getImgproduit()->setServicetext($service);
			}
			if($produit->getVideoproduit() != null)
			{
				$produit->getVideoproduit()->setServicetext($service);
			}
			$produit->setUser($this->getUser());
			$em->persist($produit);
			$em->flush();

			$this->get('session')->getFlashBag()->add('information','Votre cours a été enregistré avec succès.');
			
			// envoie d'email
			$siteweb = $this->params->get('siteweb');
			$sitename = $this->params->get('sitename');
			$emailadmin = $this->params->get('emailadmin');
			// if($service->email($emailadmin))
			// {
				// $mail = new Afmail();
				
				// $mail->setFrom($this->getUser()->name(30).' <'.$this->getUser()->getUsername().'>'); 
				// $mail->setSubject($this->getUser()->name(30).' viens de démarrer avec la rédaction d\'un nouveau cours sur '.$sitename); 
				// $mail->setHTML($this->renderView('UsersUserBundle:User:envoiemail.html.twig', array('nom'=>'Team '.$sitename,'titre' => $this->getUser()->name(30).' viens de démarrer avec la rédaction d\'un nouveau cours sur '.$sitename ,'contenu'=> 'Vérifier l\'état de ce cours en cliquant ce lien <a href="'.$siteweb.'/packagewebsiteadmin/liste/produit/souscategorie/'.$produit->getSouscategorie()->getId().'">'.$siteweb.'/packagewebsiteadmin/liste/produit/souscategorie/'.$produit->getSouscategorie()->getId().'</a>')));
				// $mail->setTextCharset('utf-8');
				// $mail->setHTMLCharset('utf-8');
				// $result = $mail->send(array($emailadmin));
			// }
			return $this->redirect($this->generateUrl('produit_produit_detail_produit_market', array('id'=>$produit->getId())));
		}else{
			$this->get('session')->getFlashBag()->add('information','Une erreur a été rencontrée !!!');
		}
	}
				  
	return $this->render('Theme/Produit/Produit/Produit/ajouterproduit.html.twig', 
	array('formpro'=>$formpro->createView()));
}

public function modifierproduit(Produit $produit, GeneralServicetext $service, Request $request)
{
	$em = $this->getDoctrine()->getManager();
	$formpro = $this->createForm(ProduitType::class, $produit); 
		
	if(($produit->getUser() != null and $produit->getUser() == $this->getUser()) or $this->isGranted('ROLE_GESTION'))
	{
		if ($request->getMethod() == 'POST'){
		$formpro->handleRequest($request);
		
		if($produit->getImgproduit() != null)
		{
			$produit->getImgproduit()->setServicetext($service);
		}
		if($produit->getVideoproduit() != null)
		{
			$produit->getVideoproduit()->setServicetext($service);
		}
		
		$produit->setServicetext($service);

		if($formpro->isValid()){
			
			if(isset($_POST['dureeminute']))
			{
				$produit->setDureeminute($_POST['dureeminute']);
			}
			if(isset($_POST['dureeseconde']))
			{
				$produit->setDureeseconde($_POST['dureeseconde']);
			}
			if(isset($_POST['typecours']))
			{
				$produit->setTypecours($_POST['typecours']);
			}
		
			$em->flush();
			$this->get('session')->getFlashBag()->add('information','Votre cours a été modifié avec succès.');
			return $this->redirect($this->generateUrl('produit_produit_detail_produit_market',array('id'=>$produit->getId())));
		}else{
			$this->get('session')->getFlashBag()->add('information','Une erreur a été rencontrée !!!');
		}
		}

		return $this->render('Theme/Produit/Produit/Produit/modifierproduit.html.twig',
		array('produit'=>$produit,'formpro'=>$formpro->createView()));
	}else{
		//envoie d'email
		$this->get('session')->getFlashBag()->add('information','Echec ! vous n\'avez pas le droit d\'effectuer cette action.');
	}
	return $this->redirect($this->generateUrl('produit_produit_ajouter_nouveau_produit'));
}

public function menucours()
{
	$em = $this->getDoctrine()->getManager();
	$liste_categorie = $em->getRepository(Categorie::class)
	                      ->findAll();
	$categorie = $service->selectEntity($liste_categorie);
	$nbproduit = '';
	$liste_scat = new \Doctrine\Common\Collections\ArrayCollection();
	if($categorie != null)
	{
		$produit_cat = $em->getRepository(Souscategorie::class)
	                      ->findNbProduitCat($categorie->getId());
		foreach($produit_cat as $valeur)
		{
			$nbproduit = $valeur['val'];
		}
		$liste_scat = $em->getRepository(Souscategorie::class)
	                      ->findBy(array('categorie'=>$categorie), array('rang'=>'desc'));
	}
	
	return $this->render('Theme/Produit/Produit/Produit/menucours.html.twig',
	array('categorie'=>$categorie,'nbproduit'=>$nbproduit,'liste_scat'=>$liste_scat));
}

public function miseajourproduit(Produit $produit, GeneralServicetext $service, Request $request)
{
	$em = $this->getDoctrine()->getManager();
	$formpro = $this->createForm(new ProduitType($produit->getsouscategorie()->getCategorie()), $produit);
	if($request->getMethod() == 'POST')
	{
		$formpro->handleRequest($request);
		$produit->setUser($this->getUser());
		$imgpro = $produit->getImgpro();
		$imgpro->setServicetext($service);
		$imgpro->setProduit($produit);
		$liste_img = $produit->getImgproduits();
		$nbimgproduit = $this->container->getParameter('nbimgparproduit');
		if($formpro->isValid() and count($liste_img) <= $nbimgproduit){
			$em->persist($imgpro);
			$em->flush();
		}else{
			if( count($liste_img) > $nbimgproduit )
			{
			  $this->get('session')->getFlashBag()->add('images','Trop d\'image pour ce produit.');
			}else{
			  $this->get('session')->getFlashBag()->add('images','Données invalides.');
			}
		}
	}
	return $this->redirect($this->generateUrl('users_adminuser_update_courant_produit',array('id'=>$produit->getId())));
}

public function supprimerimage(Imgproduit $imgproduit, Request $request)
{
	$em = $this->getDoctrine()->getManager();
	$formsupp = $this->createFormBuilder()->getForm(); 
	$produit = $imgproduit->getProduit();
	if ($request->getMethod() == 'POST') {
    $formsupp->handleRequest($request);
    if ($formsupp->isValid()){
		if(count($produit->getImgproduits()) > 1)
		{
			$em->remove($imgproduit);
			$em->flush();
			$this->get('session')->getFlashBag()->add('informationsupp','Suppression effectuée avec succés');
		}else{
			$this->get('session')->getFlashBag()->add('informationsupp','Un produit doit avoir au mions une images');
		}
	return $this->redirect($this->generateUrl('users_adminuser_update_courant_produit',array('id'=>$produit->getId())));
	}
	}
    $this->get('session')->getFlashBag()->add('supprime_image',$imgproduit->getId());
	$this->get('session')->getFlashBag()->add('supprime_image',$imgproduit->getProduit()->getNom());
	return $this->redirect($this->generateUrl('users_adminuser_update_courant_produit',array('id'=>$produit->getId())));
}

public function listeproduituser(Categorie $cat, $page)
{
	$em = $this->getDoctrine()->getManager();
	$liste_produit = $em->getRepository(Produit::class)
	                    ->listeproduituser($cat->getId(),$page,16);
						
	$nombrepage = ceil(count($liste_produit)/16);

	return $this->render('Theme/Produit/Produit/Produit/listeproduituser.html.twig', 
	array('cat'=>$cat,'liste_produit'=>$liste_produit,'nombrepage'=>$nombrepage,'page'=>$page));
}

public function alterprixproduit(Produit $produit, GeneralServicetext $service, Request $request)
{
	$em = $this->getDoctrine()->getManager();
	$souscategorie = $produit->getSouscategorie();

	if($request->getMethod() == 'POST' and isset($_POST['prixproduit']) and isset($_POST['dureeformation']) and is_numeric($_POST['prixproduit']) and is_numeric($_POST['dureeformation'])){
		$produit->setNewprise($_POST['prixproduit']);
		$produit->setDureeacces($_POST['dureeformation']);
		$em->flush();
		$this->get('session')->getFlashBag()->add('information','Succès ! Prix mis à jour avec succès.');
	}else{
		$this->get('session')->getFlashBag()->add('information','Echec de validation du prix.');
	}
	if($produit->getValide() == true)
	{
	return $this->redirect($this->generateUrl('users_adminuser_liste_produit_souscategorie',array('id'=>$produit->getSouscategorie()->getId())));
	}else{
	return $this->redirect($this->generateUrl('users_adminuser_prod_invalide_courant_souscategorie',array('id'=>$produit->getSouscategorie()->getId())));
	}
}

public function moduleformation(GeneralServicetext $service)
{
	$em = $this->getDoctrine()->getManager();
	$liste_categorie = $em->getRepository(Categorie::class)
	                      ->myFindAll();
	foreach($liste_categorie as $cat)
	{
		$cat->setEm($em);
	}					  

	return $this->render('Theme/Produit/Produit/Produit/moduleformation.html.twig', 
	array('liste_categorie'=>$liste_categorie));
}

public function detailproduit(Produit $produit, GeneralServicetext $service, $mess)
{
	if($produit->getValide() == true or ($produit->getValide() == false and $produit->getUser() == $this->getUser()) or ($produit->getValide() == false and $this->isGranted('ROLE_GESTION')) or (isset($_GET['codeadmin']) and $_GET['codeadmin'] == 10001))
	{
	if(isset($_GET['codeadmin']))
	{
		$codeadmin = $_GET['codeadmin'];
	}else{
		$codeadmin = 0;
	}
	$em = $this->getDoctrine()->getManager();
	
	$partie = new Partiecours();
	$form = $this->createForm(PartiecoursType::class, $partie); 
	

	$formsupp = $this->createFormBuilder()->getForm(); 
	
	$session = $this->get('session');

	if($this->getUser() != null)
	{
	$listelikes = $produit->getUserlikes();
	$trouve = false;
	foreach($listelikes as $ser)
	{
		if($this->getUser() == $ser)
		{
			$trouve = true;
			break;
		}
	}
	
	if($trouve == false)
	{
		$produit->addUserlike($this->getUser());
	}
	}else{
		$liste_prod = $session->get('like_produit');
		if($liste_prod != null)
		{
		$tabprod = explode('-',$liste_prod);
		$addlike = true;
		for($i = 0; $i < count($tabprod); $i++)
		{
			if($tabprod[$i] == $produit->getId())
			{
				$addlike = false;
				break;
			}
		}
		
		if($addlike == true)
		{
			$session->set('like_produit',$session->get('like_produit').'-'.$produit->getId());
		}
		
		}else{
			$session->set('like_produit',$produit->getId());
			$addlike = true;
		}
		
		if($addlike == true)
		{
			$produit->setNblike($produit->getNblike() + 1);
		}
	}
	$em->flush();
	
	$bestpanier = null;
	$prodpan = null;
	if($this->getUser() != null)
	{
	
	$liste_oldpanier = $em->getRepository(Panier::class)
						  ->findBy(array('user'=>$this->getUser(),'valide'=>1));
	//on cherche à retenir le bon panier .
	foreach($liste_oldpanier as $panier)  //uno    -    le panier lié à un service (une offre de formation) est prio
	{
		$trouve = false;
		foreach($panier->getProduitpaniers() as $propan)
		{
			if($propan->getProduit() == $produit)
			{
				$trouve = true;
				break;
			}
		}
		if($panier->getservice() != null and $trouve == true)
		{
			$bestpanier = $panier;
			break;
		}
	}
	
	if($bestpanier == null)
	{
		foreach($liste_oldpanier as $panier)  // secondo    -    Le panier lié à un produit est bon, s'il ya aucun lié à un service
		{
			$trouve = false;
			foreach($panier->getProduitpaniers() as $propan)
			{
				if($propan->getProduit() == $produit)
				{
					$trouve = true;
					break;
				}
			}
			if($panier->getservice() == null and $panier->getChapitrecours() == null and $trouve == true)
			{
				$bestpanier = $panier;
				break;
			}
		}
	}
	}
	
	if($bestpanier != null)
	{
		foreach($bestpanier->getProduitpaniers() as $propan)
		{
			if($propan->getProduit() == $produit)
			{
				$prodpan = $propan;
				break;
			}
		}
	}

	$produit->setEm($em);
	$supportchapitres = new \Doctrine\Common\Collections\ArrayCollection();
	$pratiquechapitres = new \Doctrine\Common\Collections\ArrayCollection();
	$exerciceparties = new \Doctrine\Common\Collections\ArrayCollection();
	$liste_part = $em->getRepository(Partiecours::class)
	                 ->findBy(array('produit'=>$produit), array('rang'=>'asc'));
	foreach($liste_part as $partie)
	{
		$partie->setEm($em);
		foreach($partie->getAllChapitre() as $chapitre)
		{
			foreach($chapitre->getSupportchapitres() as $support)
			{
				$supportchapitres[] = $support;
			}
			foreach($chapitre->getPratiquechapitres() as $pratique)
			{
				$pratiquechapitres[] = $pratique;
			}
			foreach($chapitre->getExerciceparties() as $exercice)
			{
				$exerciceparties[] = $exercice;
			}
		}
	}
	
    $oldservice =  null; //On cherche à obtenir le service approprié auquel ce cours est associé pour affichier la liste des cours.
	if($bestpanier == null)
	{
		$produitformation = $em->getRepository(Produitformation::class)
	                           ->findOneBy(array('produit'=>$produit), array('date'=>'desc'));
		if($produitformation != null)
		{
			$oldservice = $produitformation->getService();
		}
	}else{
		if($bestpanier->getservice() != null)
		{
			$oldservice = $bestpanier->getservice();
		}else if($panier->getChapitrecours() != null)
		{
			$produitformation = $em->getRepository(Produitformation::class)
								   ->findOneBy(array('produit'=>$panier->getChapitrecours()->getPartiecours()->getProduit()), array('date'=>'desc'));
			if($produitformation != null)
			{
				$oldservice = $produitformation->getService();
			}
		}else{
			$produitformation = $em->getRepository(Produitformation::class)
								   ->findOneBy(array('produit'=>$prodpan->getProduit()), array('date'=>'desc'));
			if($produitformation != null)
			{
				$oldservice = $produitformation->getService();
			}
		}
	}
	$cours_formation = new \Doctrine\Common\Collections\ArrayCollection();
	if($oldservice != null)
	{
		$cours_formation = $em->getRepository(Produitformation::class)
	                           ->findBy(array('service'=>$oldservice), array('rang'=>'asc'));
		foreach($cours_formation as $forma)
		{
			$forma->getProduit()->setEm($em);
		}
	}
	
	$messages_cours = new \Doctrine\Common\Collections\ArrayCollection();
	if($this->getUser() != null)
	{
		
		$messages_cours = $em->getRepository(Commentaireblog::class)
							   ->findBy(array('user'=>$this->getUser(), 'produit'=>$produit), array('date'=>'desc'));
	}
	return $this->render('Theme/Produit/Produit/Produit/detailproduit.html.twig', 
	array('produit'=>$produit,'prodpan'=>$prodpan,'codeadmin'=>$codeadmin,'bareme'=>$service->getBareme(),'formsupp'=>$formsupp->createView(),
	'form'=>$form->createView(),'liste_part'=>$liste_part,'supportchapitres'=>$supportchapitres,'pratiquechapitres'=>$pratiquechapitres,
	'exerciceparties'=>$exerciceparties,'mess'=>$mess,'cours_formation'=>$cours_formation,'messages_cours'=>$messages_cours,'oldservice'=>$oldservice));
	}else{
	
	$this->get('session')->getFlashBag()->add('alertnewsletter','<span class="fa fa-warning"></span> Echec ! vous n\\\'avez pas le droit d\\\'accéder à cette ressource !');

	}
	return $this->redirect($this->generateUrl('users_user_acces_plateforme'));
}

public function addquartiervillen(Produit $produit, GeneralServicetext $service)
{
	$em = $this->getDoctrine()->getManager();
	if(isset($_POST['ville']) and is_numeric($_POST['ville']) and isset($_POST['quartier']))
	{
		$ville = $em->getRepository(Ville::class)
	                ->find($_POST['ville']);
		if($ville != null)
		{
			$quartier = $em->getRepository(Quartier::class)
	                       ->findOneBy(array('nom'=>$_POST['quartier'], 'ville'=>$ville));
			if($quartier != null)
			{
				$produit->setQuartier($quartier);
				$em->flush();
			}else{
				$quartier = new Quartier($service);
				$quartier->setNom($_POST['quartier']);
				$quartier->setVille($ville);
				$em->persist($quartier);
				$produit->setQuartier($quartier);
				$em->flush();
			}
			$this->get('session')->getFlashBag()->add('information','Enregistrez avec succès !');
		}
	}else{
		$this->get('session')->getFlashBag()->add('information','Echec ! Toutes les données n\'ont pas été reçu.');
	}
	return $this->redirect($this->generateUrl('produit_produit_detail_produit_market', array('id'=>$produit->getId())));
}

public function inscriptionuser(Produit $produit, GeneralServicetext $service)
{
	$em = $this->getDoctrine()->getManager();
	if(isset($_POST['ville']) and is_numeric($_POST['ville']) and isset($_POST['password']))
	{
		$ville = $em->getRepository(Ville::class)
	                ->find($_POST['ville']);
		if($ville != null)
		{
			$oluser = $em->getRepository(User::class)
	                       ->findOneBy(array('username'=>$produit->getMail()));
			if($oluser == null)
			{
				$user = new User($service);
				$user->setNom($produit->getUsername());
				$user->setUsername($produit->getMail());
				$user->setPassword($produit->getTel());
				$user->setTel($_POST['password']);
				$user->setVille($ville);
				$user->setNbannonce(1);
				$em->persist($user);
				$em->flush();
				$produit->setUser($user);
				$em->flush();
				$this->get('session')->getFlashBag()->add('information','Inscription effectué avec succès !');

				$token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
				// On passe le token crée au service security context afin que l'utilisateur soit authentifié
				$this->get('security.context')->setToken($token);
				return $this->redirect($this->generateUrl('users_user_user_accueil',array('id'=>$user->getId())));
			}else{
				$this->get('session')->getFlashBag()->add('information','Un utilisateur à déjà cette adresse e-mail');
				return $this->redirect($this->generateUrl('produit_produit_detail_produit_market', array('id'=>$produit->getId(),'verifier'=>1)));
			}
		}else{
			$this->get('session')->getFlashBag()->add('information','Aucune ville sélectionnée !');
		}
	}else{
		$this->get('session')->getFlashBag()->add('information','Echec ! Toutes les données n\'ont pas été reçu.');
	}
	return $this->redirect($this->generateUrl('produit_produit_detail_produit_market', array('id'=>$produit->getId())));
}

public function connexionuser(Produit $produit, GeneralServicetext $service)
{
	$em = $this->getDoctrine()->getManager();
	if(isset($_POST['username']) and isset($_POST['password']))
	{
		$user = $em->getRepository(User::class)
	                       ->findOneBy(array('username'=>$produit->getMail(),'password'=>$_POST['password']));
		if($user != null)
		{
			$user->setNbannonce($user->getNbannonce() + 1);
			$produit->setUser($user);
			$produit->setMail($user->getUsername());
			$em->flush();
			$this->get('session')->getFlashBag()->add('information','Connexion effectué avec succès !');

			$token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
			// On passe le token crée au service security context afin que l'utilisateur soit authentifié
			$this->get('security.context')->setToken($token);
			return $this->redirect($this->generateUrl('users_user_user_accueil',array('id'=>$user->getId())));
		}else{
			$this->get('session')->getFlashBag()->add('information','Echec de connexion');
		}
	}else{
		$this->get('session')->getFlashBag()->add('information','Echec ! Toutes les données n\'ont pas été reçu.');
	}
	return $this->redirect($this->generateUrl('produit_produit_detail_produit_market', array('id'=>$produit->getId(),'verifier'=>2)));
}

public function likeproduct()
{
	if(isset($_GET['id']))
	{
		$id = $_GET['id'];
	}else{
		$id = 0;
	}
	
	$em = $this->getDoctrine()->getManager();
	$service = $em->getRepository(Commentaireblog::class)
	              ->find($id);
				  
	if($service != null and $this->getUser() != null){
		$oldepingle = $em->getRepository(Sujetepingle::class)
						 ->findOneBy(array('user'=>$this->getUser(),'commentaireblog'=>$service),array('date'=>'desc'),1);
		if($oldepingle == null)
		{
			$epingle = new Sujetepingle();
			$epingle->setCommentaireblog($service);
			$epingle->setUser($this->getUser());
			$em->persist($epingle);
			$em->flush();
		}
		echo 1;
		exit;
	}else{
	echo 0;
	exit;
	}
}

public function removeepingle()
{
	if(isset($_GET['id']))
	{
		$id = $_GET['id'];
	}else{
		$id = 0;
	}
	
	$em = $this->getDoctrine()->getManager();
	$epingle = $em->getRepository(Sujetepingle::class)
	              ->find($id);
	if($epingle != null and $this->getUser() == $epingle->getUser()){
		$em->remove($epingle);
		$em->flush();
		
		echo 1;
		exit;
	}else{
	echo 0;
	exit;
	}
}

public function ajouterpanier(Produit $produit, GeneralServicetext $service)
{
if(isset($_POST['_password']))
{
	$em = $this->getDoctrine()->getManager();
	//$nbjour = $this->date->diff(new \Datetime())->days;
	if($this->getUser()->getSoldeprincipal() >= $produit->getNewprise())
	{
		if($_POST['_password'] == $service->decrypt($this->getUser()->getPassword(),$this->getUser()->getSalt()))
		{
			$liste_oldpanier = $em->getRepository(Panier::class)
							      ->findBy(array('user'=>$this->getUser(),'valide'=>1));
			$souscription = true;
			foreach($liste_oldpanier as $panier)
			{
				$produitpaniers = $panier->getProduitpaniers();
				foreach($produitpaniers as $propan)
				{
					if($propan->getProduit() == $produit and $panier->getChapitrecours() == null)
					{
						$souscription = false;
						break;
					}
				}
				if($souscription == false)
				{
					break;
				}
			}
			
			if($souscription == true and ($this->getUser()->getSoldeprincipal() >= $produit->getNewprise()))
			{
				$this->getUser()->setSoldeprincipal($this->getUser()->getSoldeprincipal() - $produit->getNewprise());
				
				//s'il n'est pas inscrit au cours courant, on vérifie s'il a déjà été inscrit à ce cours.
				
				$liste_oldpanier = $em->getRepository(Panier::class)
							      ->findBy(array('user'=>$this->getUser(),'valide'=>0));
				$lastpanier = null;
				foreach($liste_oldpanier as $panier)
				{
					$produitpaniers = $panier->getProduitpaniers();
					foreach($produitpaniers as $propan)
					{
						if($propan->getProduit() == $produit and $panier->getChapitrecours() == null)
						{
							$lastpanier = $panier;
							break;
						}
					}
					if($lastpanier != null)
					{
						break;
					}
				}

				if($lastpanier == null or $lastpanier->getService() != null) //s'il n'a jamais été inscrit à cour ou bien il a été inscrit à une formation contenant ce cours et d'autres cours, il recrait la formation
				{
					$panier = new Panier();
					$panier->setUser($this->getUser());
					$panier->setValide(true);
					$panier->setMontantttc($produit->getNewprise());

					$em->persist($panier);
					$produitpanier = new Produitpanier();
					$produitpanier->setPanier($panier);
					$produitpanier->setProduit($produit);
					$produitpanier->setQuantite(1);
					$em->persist($produitpanier);
					$produit->setNbcertificat($produit->getNbcertificat() + 1);
					
					if($produit->getTypecours() == 'coursspecialise')
					{
						$panier->setMontantspecial($produit->getNewprise());
					}
					
					//envoie d'email
					$siteweb = $this->params->get('siteweb');
					$sitename = $this->params->get('sitename');
					$emailadmin = $this->params->get('emailadmin');
					if($service->email($emailadmin))
					{
						$response = $this->_servicemail->sendNotifEmail(
							'Team '.$sitename, //Nom du destinataire
							$emailadmin, //Email Destinataire
							$this->getUser()->name(30).' vient de s\'inscrire au cours '.$produit->getTitre().' sur '.$sitename, //Objet de l'email
							$this->getUser()->name(30).' vient de s\'inscrire au cours '.$produit->getTitre().' sur '.$sitename, //Grand Titre de l'email
							'Suivez la progression de cette formation via le lien ci-dessus.</br><a href="'.$siteweb.'/user/detail/user/commande/'.$panier->getId().'/'.$produit->getId().'">Suivez la formation de'.$this->getUser()->name(35).'.</a>',  //Contenu de l'email
							 ''  //Lien à suivre
						);
					}
					
					if($service->email($produit->getUser()->getUsername()))
					{

						$response = $this->_servicemail->sendNotifEmail(
							$produit->getUser()->name(50), //Nom du destinataire
							$produit->getUser()->getUsername(), //Email Destinataire
							$this->getUser()->name(30).' vient de s\'inscrire au cours '.$produit->getTitre().' sur '.$sitename, //Objet de l'email
							$this->getUser()->name(30).' vient de s\'inscrire au cours '.$produit->getTitre().' sur '.$sitename, //Grand Titre de l'email
							'Suivez la progression de cette formation via le lien ci-dessus.</br><a href="'.$siteweb.'/user/detail/user/commande/'.$panier->getId().'/'.$produit->getId().'">Suivez la formation de'.$this->getUser()->name(35).'.</a>',  //Contenu de l'email
							 ''  //Lien à suivre
						);

					}
					
					if($service->email($this->getUser()->getUsername()))
					{

						$response = $this->_servicemail->sendNotifEmail(
							$this->getUser()->name(50), //Nom du destinataire
							$this->getUser()->getUsername(), //Email Destinataire
							'Votre inscription au cours '.$produit->getTitre().' a été effectuée avec succès sur '.$sitename.' !', //Objet de l'email
							'Votre inscription au cours '.$produit->getTitre().' a été effectuée avec succès sur '.$sitename.' !', //Grand Titre de l'email
							'Accédez à votre bableau de bord pour suivre votre progression à ce cours .</br><a href="'.$siteweb.'/accueil/user/'.$this->getUser()->getId().'">Lien vers votre tableau de bord.</a>',  //Contenu de l'email
							 ''  //Lien à suivre
						);
					}
				}else{
					$lastpanier->setDate(new \Datetime());
					$lastpanier->setValide(true);
					$lastpanier->setMontantttc($produit->getNewprise());
					// envoie d'email
					$siteweb = $this->params->get('siteweb');
					$sitename = $this->params->get('sitename');
					$emailadmin = $this->params->get('emailadmin');
					if($service->email($emailadmin))
					{
						$response = $this->_servicemail->sendNotifEmail(
							'Team '.$sitename, //Nom du destinataire
							$emailadmin, //Email Destinataire
							$this->getUser()->name(30).' vient de Renouveler son inscription au cours '.$produit->getTitre().' sur '.$sitename, //Objet de l'email
							$this->getUser()->name(30).' vient de Renouveler son inscription au cours '.$produit->getTitre().' sur '.$sitename, //Grand Titre de l'email
							'Suivez la progression de cette formation via le lien ci-dessus.</br><a href="'.$siteweb.'/user/detail/user/commande/'.$lastpanier->getId().'/'.$produit->getId().'">Suivez la formation de'.$this->getUser()->name(35).'.</a>',  //Contenu de l'email
							 ''  //Lien à suivre
						);

					}
					
					if($service->email($produit->getUser()->getUsername()))
					{
						$response = $this->_servicemail->sendNotifEmail(
							$produit->getUser()->name(50), //Nom du destinataire
							$produit->getUser()->getUsername(), //Email Destinataire
							$this->getUser()->name(30).' vient de Renouveler son inscription au cours '.$produit->getTitre().' sur '.$sitename, //Objet de l'email
							$this->getUser()->name(30).' vient de Renouveler son inscription au cours '.$produit->getTitre().' sur '.$sitename, //Grand Titre de l'email
							'Suivez la progression de cette formation via le lien ci-dessus.</br><a href="'.$siteweb.'/user/detail/user/commande/'.$lastpanier->getId().'/'.$produit->getId().'">Suivez la formation de'.$this->getUser()->name(35).'.</a>',  //Contenu de l'email
							 ''  //Lien à suivre
						);
					}
					
					if($service->email($this->getUser()->getUsername()))
					{

						$response = $this->_servicemail->sendNotifEmail(
							$this->getUser()->name(50), //Nom du destinataire
							$this->getUser()->getUsername(), //Email Destinataire
							'Votre inscription au cours '.$produit->getTitre().' a été renouvelé avec succès sur '.$sitename.' !', //Objet de l'email
							'Votre inscription au cours '.$produit->getTitre().' a été renouvelé avec succès sur '.$sitename.' !', //Grand Titre de l'email
							'Accédez à votre bableau de bord pour suivre votre progression à ce cours .</br><a href="'.$siteweb.'/accueil/user/'.$this->getUser()->getId().'">Lien vers votre tableau de bord.</a>',  //Contenu de l'email
							 ''  //Lien à suivre
						);
					}
				}
				$this->get('session')->getFlashBag()->add('information','Inscription au cours effectuée avec succès !');
				$em->flush();
			}else{
				$this->get('session')->getFlashBag()->add('information','Echec d\'enregistrement !! Vous êtes déjà inscrit(e) à une formation contenant ce cours ou vous n\'avez pas assez de fonds');
			}
				 
		}else{
			$this->get('session')->getFlashBag()->add('information','Echec d\'enregistrement !! Le mot de passe que vous avez entré est invalide.');
		}
	}else{
		$this->get('session')->getFlashBag()->add('information','Echec d\'enregistrement !! Vous n\'avez pas assez de fonds pour souscrire à cette formation.');
	}
}else{
	$this->get('session')->getFlashBag()->add('information','Echec d\'enregistrement !! Toutes les données n\'ont pas été reçu.');
}

return $this->redirect($this->generateUrl('produit_produit_detail_produit_market', array('id'=>$produit->getId())));
}

public function checkticket()
{
	if($this->getUser() !=  null)
	{
		$em = $this->getDoctrine()->getManager();
		$oldpanier = $em->getRepository(Panier::class)
						 ->findOneBy(array('user'=>$this->getUser(),'payer'=>0));
		if($oldpanier != null)
		{
			foreach($oldpanier->getProduitpaniers() as $prodpan)
			{
				$em->remove($prodpan);
			}
			$em->flush();
		}else{
			$oldpanier = new Panier();
			$oldpanier->setUser($this->getUser());
			$em->persist($oldpanier);
			$em->flush();
		}			
		$session = $this->get('session');
		$produit_ticket = $session->get('produit_ticket');
		$tabprod = explode('-',$produit_ticket);
		if(count($tabprod) > 0)
		{
			for($i = 0; $i < count($tabprod); $i++)
			{
				$oldproduit = $em->getRepository(Produit::class)
								->find($tabprod[$i]);
				if($oldproduit != null)
				{
					$produitpanier = new Produitpanier();
					$produitpanier->setPanier($oldpanier);
					$produitpanier->setProduit($oldproduit);
					$produitpanier->setQuantite($oldproduit->getPronostique());
					$em->persist($produitpanier);
					$em->flush();
				}
			}
		}
		if($oldpanier != null)
		{
			if(count($oldpanier->getProduitpaniers()) < 5)
			{
				$this->get('session')->getFlashBag()->add('nbmatchsuffisant','Vous devez avoir exactement 5rencontres sur votre ticket avant de valider.');
			}
		}
	}else{
		$this->get('session')->getFlashBag()->add('information','Vous devez vous connectez pour jouer !');
	}
	return $this->redirect($this->generateUrl('produit_produit_liste_produit_souscategorie'));
}

public function addpanier(Produit $produit)
{
	$em = $this->getDoctrine()->getManager();
	if($this->getUser() != null)
	{
	$oldpanier = $em->getRepository(Panier::class)
	                 ->findOneBy(array('user'=>$this->getUser(),'payer'=>0));
		if($oldpanier == null)
		{
			$panier = new Panier();
			$panier->setUser($this->getUser());
			$em->persist($panier);
			$produitpanier = new Produitpanier();
			$produitpanier->setPanier($panier);
			$produitpanier->setProduit($produit);
			$em->persist($produitpanier);
			$em->flush();
		}else{
			if(count($oldpanier->getProduitpaniers()) != 0)
			{
				$listprod = $oldpanier->getProduitpaniers();
				$trouve = false;
				foreach($listprod as $prod)
				{
					if($prod->getProduit() == $produit)
					{
						$prod->setQuantite($prod->getQuantite() + 1);
						$trouve = true;
						break;
					}
				}
				if($trouve == false)
				{
					$produitpanier = new Produitpanier();
					$produitpanier->setPanier($oldpanier);
					$produitpanier->setProduit($produit);
					$em->persist($produitpanier);
				}
			}else{
				$produitpanier = new Produitpanier();
				$produitpanier->setPanier($oldpanier);
				$produitpanier->setProduit($produit);
				$em->persist($produitpanier);
			}
			$em->flush();
		}
	$this->get('session')->getFlashBag()->add('ajoutproduit','Produit ajouter avec succès');
	}else{
	$this->get('session')->getFlashBag()->add('ajoutproduit','Une erreur a été rencontrée !');
	}
	return $this->redirect($this->generateUrl('produit_produit_liste_produit_souscategorie',array('id'=>$produit->getSouscategorie()->getId())));
}

public function reglementcommande(Produit $produit, GeneralServicetext $service)
{
	$em = $this->getDoctrine()->getManager();
	if($produit->getActive() == true)
	{
		$oldcommande = $em->getRepository(Produitpanier::class)
						 ->findBy(array('produit'=>$produit));
		$affiche = true;
		foreach($oldcommande as $com)
		{
			if($com->getPanier()->getLivrer() == true)
			{
				$affiche = false;
				break;
			}
		}		
		if($affiche == true)
		{
			$panier = null;
			foreach($oldcommande as $com)
			{
				if(isset($_POST['mailuser']) and isset($_POST['teluser']) and $com->getPanier()->getEmail() == $_POST['mailuser'] and $com->getPanier()->getTel() == $_POST['teluser'])
				{
					$panier = $com->getPanier();
					break;
				}
			}			 
			
			if($panier == null)
			{
				$panier = new Panier();
				if($this->getUser() != null)
				{
					$panier->setUser($this->getUser());
				}
				$em->persist($panier);
				
				$produitpanier = new Produitpanier();
				$produitpanier->setPanier($panier);
				$produitpanier->setProduit($produit);
				
				$em->persist($produitpanier);
				$em->flush();
			}
			$this->get('session')->getFlashBag()->add('information','Votre bon de commande a été généré avec succès !');

			if(isset($_POST['nomuser']) and isset($_POST['mailuser']) and isset($_POST['teluser']))
			{
				$panier->setNomuser(htmlspecialchars($_POST['nomuser']));
				$panier->setEmail(htmlspecialchars($_POST['mailuser']));
				$panier->setTel(htmlspecialchars($_POST['teluser']));
				$panier->setPayer(true);
				$em->flush();
			}

			//envoie d'email
			$siteweb = $this->params->get('siteweb');
			$conditionuserlink = $this->params->get('conditionuserlink');
			$sitename = $this->params->get('sitename');
			$emailadmin = $this->params->get('emailadmin');
			
			$orangemoney = $this->params->get('orangemoney');
			$mtnmobile = $this->params->get('mtnmobile');
				
			// Instanciation de la classe dérivée
			$pdf = new PDF('L','mm','A5');
			$pdf->AliasNbPages();
			$pdf->AddPage();
			$pdf->SetFont('Times','',12);
			$pdf->setSiteweb($siteweb);
			$pdf->setEmail($emailadmin);
			$pdf->setClient($panier->getNomuser());
			$pdf->setLien($conditionuserlink);
				
				$pdf->description($produit->getSouscategorie()->getPrix(),$orangemoney,$mtnmobile,$panier->getTel(),$produit->name(30).' - '.$produit->getSouscategorie()->getNom(),$produit->getQuartiertext(),$panier->dateFacture());
				$pdf->completeBorder($panier->numFacture(),date('Y'));
				$pdf->SetAuthor('Noel Kenfack');
			
			if(!file_exists ($panier->getUploadRootDir()))
			{
				mkdir($panier->getUploadRootDir(),0777,true);
			}
			if(!file_exists ($panier->getUploadRootDir().'/'.$panier->numFacture().'.pdf'))
			{
			$pdf->Output('F',$panier->getWebPath());
			}
			
			//Email Administration
			if($service->email($emailadmin) and $panier->getMessadmin() == false)
			{

				$response = $this->_servicemail->sendNotifEmail(
					'Hello Team '.$sitename, //Nom du destinataire
					$emailadmin, //Email Destinataire
					''.$panier->getNomuser().' vient de passer une commande via '.$sitename.' !', //Objet de l'email
					''.$panier->getNomuser().' vient de passer une commande via '.$sitename.' !', //Grand Titre de l'email
					'Contacter le client. Retrouvez ses contacts sur la fiche d\'ouverture de dossier via le document ci-joint.',  //Contenu de l'email
					 ''  //Lien à suivre
				);
			}
				
			//Email Administration
			if($service->email($panier->getEmail()))
			{

				$response = $this->_servicemail->sendNotifEmail(
					$panier->getNomuser(), //Nom du destinataire
					$panier->getEmail(), //Email Destinataire
					'Vous avez reservé un produit avec succès sur '.$sitename.' !', //Objet de l'email
					'Vous avez reservé un produit avec succès sur '.$sitename.' !', //Grand Titre de l'email
					'Retrouvez ci-joint votre FICHE D\'OUVERTURE DE DOSSIER, Favorisez la prise en charge immédiate de votre dossier en payant à temps votre réservation.',  //Contenu de l'email
					 ''  //Lien à suivre
				);
			}

			return $this->render('Theme/Produit/Produit/Produit/reglementcommande.html.twig', 
			array('panier'=>$panier,'produit'=>$produit));
		}else{
			$this->get('session')->getFlashBag()->add('information','Ce produit a déjà été réservé par un client de confiance !');
			return $this->redirect($this->generateUrl('produit_produit_detail_produit_market',array('id'=>$produit->getId())));
		}
	
	}else{
		$this->get('session')->getFlashBag()->add('information','Ce produit a déjà été réservé par un client de confiance !');
		return $this->redirect($this->generateUrl('produit_produit_detail_produit_market',array('id'=>$produit->getId())));
	}
}

public function editcommande()
{
	if(isset($_GET['id']))
	{
		$id = $_GET['id'];
	}else{
		$id = 0;
	}
	if(isset($_GET['quantite']))
	{
		$quantite = $_GET['quantite'];
	}else{
		$quantite = 0;
	}

	$em = $this->getDoctrine()->getManager();
	$prodpan = $em->getRepository(Produitpanier::class)
	                 ->find($id);
	if($prodpan != null)
	{
		$prodpan->setQuantite($quantite);
		$em->flush();
	echo 1;
	exit;
	}else{
	echo 0;
	exit;
	}
}


public function eleveproduit(Produitpanier $prodpan)
{
	$produit = $prodpan->getProduit();
	if($this->getUser() == $prodpan->getpanier()->getUser())
	{
		$em = $this->getDoctrine()->getManager();
		$em->remove($prodpan);
		$em->flush();
		$this->get('session')->getFlashBag()->add('matchenleve','La rencontre a été enlevée de votre ticket avec succès !');
		return $this->redirect($this->generateUrl('produit_produit_liste_produit_souscategorie',array('id'=>$produit->getSouscategorie()->getCategorie()->getId())));
	}
	return $this->redirect($this->generateUrl('login'));
}

public function supprimerproduit(Produit $produit, GeneralServicetext $service, Request $request)
{
	$em = $this->getDoctrine()->getManager();
	$prodpan = $em->getRepository(Produitpanier::class)
	              ->findBy(array('produit'=>$produit));
	$categorie = $produit->getSouscategorie();
	$user = $produit->getUser();
		$formsupp = $this->createFormBuilder()->getForm();
		if ($request->getMethod() == 'POST'){
		$formsupp->handleRequest($request);
		if ($formsupp->isValid()){
			$produit->setServicetext($service);
			if(($produit->getUser() != null and $produit->getUser() == $this->getUser()) or $this->get('security.context')->isGranted('ROLE_GESTION'))
			{
				if(count($prodpan) == 0){
					$partie_cours = $em->getRepository(Partiecours::class)
	                                   ->findBy(array('produit'=>$produit));
					if(count($partie_cours) == 0)
					{
						
						$partie_animation = $em->getRepository(Animationproduit::class)
	                                           ->findBy(array('produit'=>$produit));
						foreach($partie_animation as $animation)
						{
							$em->remove($animation);
						}
						$img_slide = $em->getRepository(Imgslide::class)
	                                           ->findBy(array('produit'=>$produit));
						foreach($img_slide as $slide)
						{
							$em->remove($slide);
						}							
						$em->remove($produit);
						$em->flush();

					}else{
						$this->get('session')->getFlashBag()->add('information','Action réfusée ! Supprimer toutes les parties de ce cours d\'abord.');
					}
				}else{
					$this->get('session')->getFlashBag()->add('information','Action réfusée ! ce cours est déjà reservé par un utilisateur.');
				}
			}else{
				//envoie d'email
				$this->get('session')->getFlashBag()->add('information','Action réfusée ! vous n\'avez pas le droit de supprimer ce cours.');
			}
		}
		}else{
		$this->get('session')->getFlashBag()->add('supprime_prod',$produit->getId());
	    $this->get('session')->getFlashBag()->add('supprime_prod',$produit->getTitre());
		}
	return $this->redirect($this->generateUrl('users_adminuser_prod_invalide_courant_souscategorie',array('id'=>$produit->getSouscategorie()->getId())));
}

public function rechercheproduit($position)
{
	if(isset($_GET['donnee']))
	{
		$donnee = $_GET['donnee'];
	}else{
		$donnee = '';
	}
	$em = $this->getDoctrine()->getManager();
	$liste_produit = $em->getRepository(Produit::class)
						->findProduit($donnee);
	
	return $this->render('Theme/Produit/Produit/Produit/recherche.html.twig', 
	array('liste_produit'=>$liste_produit, 'donnee'=>$donnee,'position'=>$position));
}

public function addcoutlivraison(Produit $produit, GeneralServicetext $service, Request $request)
{
	$em = $this->getDoctrine()->getManager();
	$coutlivraison = new Coutlivraison($service);
	$formlivraison = $this->createForm(CoutlivraisonType::class, $coutlivraison);
	if ($request->getMethod() == 'POST'){
	$formlivraison->bind($request);
	$coutlivraison->setUser($this->getUser());
	$coutlivraison->setProduit($produit);
	$oldcout = $em->getRepository(Coutlivraison::class)
						     ->findOneBy(array('ville'=>$coutlivraison->getVille(),'produit'=>$produit));
    if ($formlivraison->isValid() and $oldcout == null){
		$em->persist($coutlivraison);
		$em->flush();
		$this->get('session')->getFlashBag()->add('informationsupp','Enregistrement effectué avec succès');
	}
	}
	return $this->redirect($this->generateUrl('users_adminuser_update_courant_produit',array('id'=>$produit->getId())));
}

public function modifcoutlivraison(Coutlivraison $coutlivraison)
{
	$em = $this->getDoctrine()->getManager();
    if (isset($_POST['coutlivraison']) and is_numeric($_POST['coutlivraison'])){
		$coutlivraison->setMontant(htmlspecialchars($_POST['coutlivraison']));
		$em->flush();
		$this->get('session')->getFlashBag()->add('informationsupp','Enregistrement effectué avec succès');
	}
	return $this->redirect($this->generateUrl('users_adminuser_update_courant_produit', 
	array('id'=>$coutlivraison->getProduit()->getId())));
}

public function supprimercoutlivraison(Coutlivraison $coutlivraison)
{
	$produit = $coutlivraison->getProduit();
	$em = $this->getDoctrine()->getManager();
	$em->remove($coutlivraison);
	$em->flush();
	return $this->redirect($this->generateUrl('users_adminuser_update_courant_produit',array('id'=>$produit->getId())));
}

public function accueilboutique($page)
{
	$em = $this->getDoctrine()->getManager();
	$liste_produit = $em->getRepository(Produit::class)
	                      ->findProduitValide($page,16);
	return $this->render('Theme/Produit/Produit/Produit/accueilboutique.html.twig',
	array('liste_produit'=>$liste_produit,'page'=>$page,'nombrepage' =>ceil(count($liste_produit)/16)));
}

public function autorecherchecours($taille=300)
{
	$em = $this->getDoctrine()->getManager();
	$devise = $this->params->get('devise');
	if(isset($_POST['donnee']))
	{
		$donnee = $_POST['donnee'];
	}else{
		$donnee = '';
	}
	
	$liste_produit = $em->getRepository(Produit::class)
				        ->findProduit($donnee, $taille);
	$tab = array();
	foreach($liste_produit as $produit){
		$d = array();
		if($produit->getImgproduit() != null and $produit->getImgproduit()->getSrc() != 'source')
		{
			$d['drapeau'] = $produit->getImgproduit()->getWebPath();
		}else{
			$d['drapeau'] = 'template/images/valid.png';
		}
		$d['nom'] = $produit->getTitre();
		$d['idprod'] = $produit->getId();
		$d['prix'] = $produit->getNewprise().' '.$devise;
		$d['categorie'] = $produit->getSouscategorie()->getNom();
		$tab[] = $d;
	}
	return new Response(json_encode($tab));
}

public function downloadvideocours(Produit $produit)
{
	$em = $this->getDoctrine()->getManager();
	$produit->getVideoproduit()->setNbtele($produit->getVideoproduit()->getNbtele() + 1);
	$em->flush();
	$nameoffile = '/../../../'.$produit->getVideoproduit()->getWebPath();
	return $this->redirect($nameoffile);
}

public function guideformateur(Produit $produit)
{
	return $this->redirect($this->generateUrl('produit_produit_detail_produit_market', array('id'=>$produit->getId(),'codeadmin'=>10001)));
}

public function positionnementchapter(Produit $produit, Produitpanier $prodpan)
{
	$em = $this->getDoctrine()->getManager();
	$liste_part = $em->getRepository(Partiecours::class)
					 ->findBy(array('produit'=>$produit), array('rang'=>'asc'));

	$trouver = false;
	$firstchapter = null;
	$nextchapter = null;
	$lastchapter  = null;
	foreach($liste_part as $part)
	{
		$part->setEm($em);
		$all_chapter = $part->getAllChapitre();
		foreach($all_chapter as $chapter)
		{
			$firstchapter = $chapter;
			$oldanimation = $em->getRepository(Animationproduit::class)
					   		   ->findOneBy(array('user'=>$this->getUser(),'chapitrecours'=>$chapter,'produitpanier'=>$prodpan,'voir'=>1));
			if($oldanimation != null){
				$trouver = true;
				$lastchapter  = $chapter;
			}else{
				$nextchapter = $chapter;
				break;
			}
		}
	}
	if($nextchapter != null){
		return $this->redirect($this->generateUrl('produit_produit_presentation_chapter', array('id'=>$nextchapter->getId())));
	}else{
		if($firstchapter != null and $lastchapter != null)
		{
			//go to $lastchapter
			return $this->redirect($this->generateUrl('produit_produit_presentation_chapter', array('id'=>$lastchapter->getId())));
		}else if($firstchapter != null)
		{
			//go to  $firstchapter
			return $this->redirect($this->generateUrl('produit_produit_presentation_chapter', array('id'=>$firstchapter->getId())));
		}else{
			//Go to présentation course
			return $this->redirect($this->generateUrl('produit_produit_detail_produit_market', array('id'=>$produit->getId())));
		}
	}
}
}
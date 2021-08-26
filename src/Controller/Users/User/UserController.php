<?php
/*
    *(c) Noel Kenfack <noel.kenfack@yahoo.fr> Février 2015
	*ce fichier est la proprieté de Zentsoft entreprise.
*/
namespace App\Controller\Users\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Users\User\User;
use App\Form\Users\User\UserType;
use App\Entity\Users\User\Imgprofil;
use App\Entity\Users\User\Imgcouverture;
use App\Form\Users\User\ImgprofilType;
use App\Form\Users\User\ImgcouvertureType;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\SecurityContext;
use App\Entity\Users\User\Newsletter;
use App\entity\Produit\Service\Curentwebsite;
use App\Entity\Produit\Produit\Souscategorie;
use App\Service\AfMail\Afmail;
use App\Service\AfMail\fileAttachment;
use App\Entity\Users\User\Notification;
use App\Service\Servicetext\GeneralServicetext;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Users\User\Sujetepingle;
use App\Entity\Produit\Produit\Panier;
use App\Entity\Produit\Produit\Produit;
use App\Entity\Produit\Produit\Chapitrecours;
use App\Entity\Produit\Service\Service;
use App\Entity\Produit\Service\Commentaireblog;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use App\Service\Email\Singleemail;
use App\Entity\Produit\Service\Ville;
use App\Entity\Produit\Service\Infoentreprise;
use App\Entity\Produit\Service\Continent;
use App\Entity\Produit\Service\Pays;

class UserController extends AbstractController
{
private $params;
private $_servicemail;

public function __construct(ParameterBagInterface $params, Singleemail $servicemail)
{
	$this->params = $params;
	$this->_servicemail = $servicemail;
}

public function inscriptionuser(GeneralServicetext $service, Request $request)
{
	$em = $this->getDoctrine()->getManager();
	// Si le visiteur est déjà identifié, on le redirige vers l'accueil
	if($this->isGranted('IS_AUTHENTICATED_REMEMBERED')){
		return $this->redirect($this->generateUrl('users_user_acces_plateforme'));
	}
	
	$valid = false;	
	$user = new User($service);
	$form = $this->createForm(UserType::class, $user);
	if($request->getMethod() == 'POST' and $this->getUser() == null){
		$form->handleRequest($request);

		
		if($form->isValid() and isset($_POST['pays'])){
			
			$pays = $em->getRepository(Pays::class)
				   	   ->find($_POST['pays']);
			if($pays != null)
			{
				$user->setCountry($pays);
			}
			//sécurisation du mot de passe utilisateur
			$passuser = $user->getPassword();
			
			$salt = substr(crypt($passuser,''), 0, 16);
			$user->setSalt($salt);
			$newpassword = $service->encrypt($passuser,$salt);
			$user->setPassword($newpassword);
			
			$em->persist($user);
			$em->flush();
			
			if($service->email($user->getUsername()) == true)
			{
				$user->setMailformateur($user->getUsername());
			}else if($service->telephone($user->getUsername()) == true){
				$user->setTel($user->getUsername());
			}
			$oldemail = $em->getRepository(Newsletter::class)
							->findBy(array('email'=>$user->getUsername()));
			if($oldemail == null)
			{
				$newsletter = new Newsletter();
				$newsletter->SetNom($user->getNom());
				$newsletter->SetEmail($user->getUsername());
				$em->persist($newsletter);
				$em->flush();
			}
				
			$token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
			// On passe le token crée au service security context afin que l'utilisateur soit authentifié
			$this->get('security.token_storage')->setToken($token);
			$this->get('session')->set('_security_users', serialize($token));
			
			return $this->redirect($this->generateUrl('users_user_user_accueil',array('id'=>$user->getId())));
		}
		$this->get('session')->getFlashBag()->add('inscription','Une erreur a été rencontrée !!!');
	}
	
	$liste_continent = $em->getRepository(Continent::class)
				   		  ->findAll();
	foreach($liste_continent as $continent)
	{
		$continent->setEm($em);
	}
	return $this->render('Theme/Users/User/User/inscriptionuser.html.twig',
	array('form'=>$form->createview(), 'liste_continent'=>$liste_continent));
}

public function accueiluser(User $user, GeneralServicetext $service)
{
	if($this->getUser() == $user or $user->getFormateur() == true)
	{

	$em = $this->getDoctrine()->getManager();
	if($this->getUser() == $user)
	{
		$user->setDatebeg(time());
		$em->flush();
	}
	
	$panier_payer = $em->getRepository(Panier::class)
					   ->findBy(array('user'=>$user,'payer'=>1), array('date'=>'desc'));
	
	$profil = new Imgprofil($service);
	$form = $this->createForm(ImgprofilType::class, $profil);
	
	if($this->getUser() == $user)
	{
	$liste_produit = $em->getRepository(Produit::class)
					   ->findBy(array('user'=>$user), array('date'=>'desc'));
	}else{
	$liste_produit = $em->getRepository(Produit::class)
					   ->findBy(array('user'=>$user,'valide'=>true), array('date'=>'desc'));
	}
	
	$tab_scat = array();
	$i = 0;
	foreach($liste_produit as $produit)
	{
		if(!in_array($produit->getSouscategorie()->getId(),$tab_scat))
		{
			$tab_scat[$i] = $produit->getSouscategorie()->getId();
			$produit->getSouscategorie()->setNbprodinval($produit->getSouscategorie()->getNbprodinval() + 1);
			$i++;
		}else{
			$produit->getSouscategorie()->setNbprodinval($produit->getSouscategorie()->getNbprodinval() + 1);
		}
	}
	
	$liste_scat = $em->getRepository(Souscategorie::class)
						  ->findBy(array('id'=>$tab_scat));	

	foreach($panier_payer as $panier)
	{
		foreach($panier->getProduitpaniers() as $propan)
		{
			$liste_chap = $em->getRepository(Chapitrecours::class)
					            ->listechapitrecours($propan->getProduit()->getId());
			foreach($liste_chap as $chap)
			{
				$chap->setEm($em);
			}
			$propan->getProduit()->setEm($em);
		}
	}
	$notemin = $this->params->get('notemin');
	
	$liste_formation = $em->getRepository(Service::class)
	                      ->findBy(array('principal'=>1),array('rang'=>'desc'));
	$liste_cours = $em->getRepository(Produit::class)
	                      ->findBy(array('guide'=>0), array('rang'=>'desc'));
	$sujet_forum = $em->getRepository(Commentaireblog::class)
	                      ->findBy(array('user'=>$user));
	$all_forum = $em->getRepository(Commentaireblog::class)
	                      ->findAll();
	return $this->render('Theme/Users/User/User/accueiluser.html.twig',
	array('user'=>$user,'form'=>$form->createView(),'sujet_forum'=>$sujet_forum,'all_forum'=>$all_forum,
	'liste_formation'=>$liste_formation,'liste_cours'=>$liste_cours,'bareme'=>$service->getBareme(),'notemin'=>$notemin,'liste_produit'=>$liste_produit,'liste_scat'=>$liste_scat,'panier_payer'=>$panier_payer));
    }else{
	return $this->redirect($this->generateUrl('users_user_acces_plateforme'));
	}
}

public function forumepingle(User $user)
{
	$em = $this->getDoctrine()->getManager();
	if($this->getUser() == $user)
	{
		$liste_epingle = $em->getRepository(Sujetepingle::class)
						 ->findBy(array('user'=>$user),array('date'=>'desc'));
		return $this->render('Theme/Users/User/User/forumepingle.html.twig',
		array('user'=>$user, 'liste_epingle'=>$liste_epingle));
	}else{
		return $this->redirect($this->generateUrl('users_user_acces_plateforme'));
	}
}

public function alertnoel()
{
	$em = $this->getDoctrine()->getManager();
	$oldprofil = $em->getRepository(Curentwebsite::class)
	                ->FindAll();
	foreach($oldprofil as $prof)
	{
		$em->remove($prof);
		$em->flush();
	}
	return $this->redirect($this->generateUrl('users_user_acces_plateforme'));
}

public function modifierprofil(User $user, GeneralServicetext $service, Request $request)
{
	if($this->getUser() != $user)
	{
		return $this->redirect($this->generateUrl('users_user_acces_plateforme'));
	}
	
	$em = $this->getDoctrine()->getManager();
	$profil = new Imgprofil($service);
	$formprofil = $this->createForm(ImgprofilType::class, $profil);
	
	$couverture = new Imgcouverture($service);
	$formcouverture = $this->createForm(ImgcouvertureType::class, $couverture);
	if ($request->getMethod() == 'POST')
	{
		$formcouverture->handleRequest($request);
		if ($formcouverture->isValid()){
			$oldcouverture = $em->getRepository('UsersUser\Imgcouverture')
							->FindOneBy(array('user'=>$user));
			if(isset($_POST['public']) and isset($_POST['contact']) and isset($_POST['emailprof']))
			{
				if($_POST['public'] == true)
				{
					$user->setTelpublic(true);
				}else{
					$user->setTelpublic(true);
				}
				
				if($service->telephone($_POST['contact']))
				{
					$user->setTel($_POST['contact']);
				}
				if($service->email($_POST['emailprof']))
				{
					$user->setMailformateur($_POST['emailprof']);
				}
			}
			
			if(isset($_POST['diplome']))
			{
				$user->setDiplome(htmlspecialchars($_POST['diplome']));
			}
			
			if(isset($_POST['poste']))
			{
				$user->setPoste(htmlspecialchars($_POST['poste']));
			}
			
			if(isset($_POST['message']))
			{
				$user->setMessage($_POST['message']);
			}
			
			if ($oldcouverture === null)
			{
				$couverture->setUser($user);
				$couverture->setId($user->getId());
				$couverture->setServicetext($service);
				$em->persist($couverture);
				$em->flush();
			}else{
				$em->remove($oldcouverture);
				$em->flush();
				$couverture->setUser($user);
				$couverture->setId($user->getId());
				$couverture->setServicetext($service);
				$em->persist($couverture);
				$em->flush();
			}
			
			$this->get('session')->getFlashBag()->add('information','Modification effectuée avec succès !!!');
		}else{
			$this->get('session')->getFlashBag()->add('information','Une erreur a été rencontrée !!! Vérifier la type du fichier');
		}
	}
	
	$liste_ville = $em->getRepository(Ville::class)
					  ->myFindAll();
							
	return $this->render('Theme/Users/User/User/modifierprofil.html.twig',
	array('formprofil'=>$formprofil->createView(),'liste_ville'=>$liste_ville,'user'=>$user,
	'formcouverture'=>$formcouverture->createView()));
}

public function updateprofil(User $user, GeneralServicetext $service, Request $request)
{
	if($this->getUser() != $user)
	{
		return $this->redirect($this->generateUrl('users_user_acces_plateforme'));
	}
	
	$em = $this->getDoctrine()->getManager();
	$profil = new Imgprofil($service);
	$formprofil = $this->createForm(ImgprofilType::class, $profil);

	if ($request->getMethod() == 'POST')
	{
		$formprofil->handleRequest($request);
		if ($formprofil->isValid()){
			$oldprofil = $em->getRepository(Imgprofil::class)
							->FindOneBy(array('user'=>$user));
			if(isset($_POST['contact']))
			{
				if($service->telephone($_POST['contact']))
				{
					$user->setTel($_POST['contact']);
				}
			}

			if(isset($_POST['ville']))
			{
				$ville = $em->getRepository(Ville::class)
					        ->find($_POST['ville']);
				if($ville != null)
				{
					$user->setVille($ville);
				}
			}
			
			if(isset($_POST['nomuser']))
			{
				$user->setNom($_POST['nomuser']);
			}
			if(isset($_POST['prenomuser']))
			{
				$user->setPrenom($_POST['prenomuser']);
			}
			
			if(isset($_POST['sexe']))
			{
				if($_POST['sexe'] == 'homme')
				{
					$user->setSexe(true);
				}else{
					$user->setSexe(false);
				}
			}
			
			if(isset($_POST['datenaiss']))
			{
				$tab = explode('/', $_POST['datenaiss']);
				if(count($tab) == 3)
				{
					$date = $tab[2].'-'.$tab[1].'-'.$tab[0];
					$date = new \Datetime($date);
					$user->setDatenaiss($date);
				}
			}
			
			if($oldprofil === null)
			{
				$profil->setUser($user);
				$profil->setId($user->getId());
				$profil->setServicetext($service);
				$em->persist($profil);
				$em->flush();
			}else{
				$em->remove($oldprofil);
				$em->flush();
				$profil->setUser($user);
				$profil->setId($user->getId());
				$profil->setServicetext($service);
				$em->persist($profil);
				$em->flush();
			}
			$this->get('session')->getFlashBag()->add('information','Modification effectuée avec succès !!!');
		}else{
			$this->get('session')->getFlashBag()->add('information','Une erreur a été rencontrée !!! Vérifier la type du fichier');
		}
	}
	return $this->redirect($this->generateUrl('users_user_modifier_profil', array('id'=>$user->getId())));
}

public function banniereuser(User $user, GeneralServicetext $service)
{
	$em = $this->getDoctrine()->getManager();
	$liste_produit = $em->getRepository(Produit::class)
					    ->findBy(array('user'=>$user,'valide'=>true));
	$liste_panier = $em->getRepository(Panier::class)
					   ->getPanierProduitUser($user->getId());
	$shot_list = $service->selectEntities($liste_produit, 4);
	
	$article_galerie = $em->getRepository(Infoentreprise::class)
                       ->findBy(array('type'=>'article-galerie'), array('rang'=>'desc'));
	$article_galerie = $service->selectEntities($article_galerie, 4);

	return $this->render('Theme/Users/User/User/banniereuser.html.twig',
	array('user'=>$user,'liste_produit'=>$shot_list,'liste_panier'=>$liste_panier, 'article_galerie'=>$article_galerie));
}

public function ajouteradmin(Request $request)
{
	$em = $this->getDoctrine()->getManager();
	if ($request->getMethod() == 'POST' and isset($_POST['_username']) and isset($_POST['_password'])){
		$username = $this->params->get('username');
		$password = $this->params->get('password');
		if($_POST['_username'] == $username and $_POST['_password'] == $password and $this->getUser() != null)
		{
			$this->getUser()->addRole('ROLE_ADMIN');
			$em->flush();
			$this->get('session')->getFlashBag()->add('information','Administrateur enregistré avec succès');
		}else{
			$this->get('session')->getFlashBag()->add('information','Le mot de passe ou le nom d\'utilisateur est incorect.');
		}
		return $this->render('Theme/Users/User/User/ajouteradmin.html.twig');
    }
	$liste_user = $em->getRepository(User::class)
	                 ->findAll();
	$exist = false;
	foreach($liste_user as $user)
	{
		if (in_array('ROLE_ADMIN', $user->getRoles())){
            $exist = true;
			break;
        }
	}
	if($exist == true)
	{
		return $this->redirect($this->generateUrl('users_user_acces_plateforme'));
	}else{
		return $this->render('Theme/Users/User/User/ajouteradmin.html.twig');
	}
}

public function nouveauadmin(Request $request)
{
	$em = $this->getDoctrine()->getManager();
	$formsupp = $this->createFormBuilder()->getForm(); 
	if ($request->getMethod() == 'POST' and isset($_POST['username']) and isset($_POST['roleuser']))
	{
		$userrole = $em->getRepository(User::class)
	                 ->findOneBy(array('username'=>$_POST['username']));
		if($userrole != null and !in_array($_POST['roleuser'], ($userrole->getRoles())))
		{
			$userrole->addRole($_POST['roleuser']);
			if($_POST['roleuser'] == 'ROLE_FORMATEUR')
			{
				$userrole->setFormateur(true);
			}
			$em->flush();
			$this->get('session')->getFlashBag()->add('information','Rôle '.$_POST['roleuser'].' ajouté avec succès à '.$userrole->name(20));
		}
	}
	$liste_user = $em->getRepository(User::class)
	                 ->findAll();
	$newcollection = new \Doctrine\Common\Collections\ArrayCollection();
	foreach($liste_user as $user)
	{
		if(in_array('ROLE_ADMIN', $user->getRoles()) or in_array('ROLE_GESTION', $user->getRoles()) or in_array('ROLE_FORMATEUR', $user->getRoles())){
            $newcollection[] = $user;
        }
	}
	return $this->render('Theme/Users/Adminuser/User/nouveauadmin.html.twig',
	array('liste_user'=>$newcollection,'formsupp'=>$formsupp->createView()));
}

public function eleverole(User $user, $idrole, Request $request)
{
	$formsupp = $this->createFormBuilder()->getForm();
	
	$em = $this->getDoctrine()->getManager();
	if ($request->getMethod() == 'POST'){
	$formsupp->handleRequest($request);
	if ($formsupp->isValid()){
		if($idrole == 1)
		{
			$user->removeRole('ROLE_GESTION');
		}
		if($idrole == 2)
		{
			$user->removeRole('ROLE_FORMATEUR');
			$user->setFormateur(false);
		}
		$em->flush();
		$this->get('session')->getFlashBag()->add('information','Rôle supprimé avec succès !!!');
	}
	}else{
		if($idrole == 1)
		{
			$role = "ROLE_GESTION à ";
		}
		if($idrole == 2)
		{
			$role = "ROLE_FORMATEUR à ";
		}
		$this->get('session')->getFlashBag()->add('supprime_role',$user->getId());
		$this->get('session')->getFlashBag()->add('supprime_role',$idrole);
		$this->get('session')->getFlashBag()->add('supprime_role',$role.' '.$user->name(20));
	}
	return $this->redirect($this->generateUrl('users_adminuser_ajout_nouveau_admin'));
}
public function accueilcommandeuser(User $user, GeneralServicetext $service)
{
	if($this->getUser() == $user or $this->isGranted('ROLE_GESTION'))
	{
		$em = $this->getDoctrine()->getManager();
	    $liste_panier = $em->getRepository(Panier::class)
						   ->getPanierProduitUser($user->getId());
		
		foreach($liste_panier as $panier)
		{
			foreach($panier->getProduitpaniers() as $propan)
			{
				$liste_chap = $em->getRepository(Chapitrecours::class)
								->listechapitrecours($propan->getProduit()->getId());
				foreach($liste_chap as $chap)
				{
					$chap->setEm($em);
				}
				$propan->getProduit()->setEm($em);
			}
		}

		$notemin = $this->params->get('notemin');
		return $this->render('Theme/Users/User/User/accueilcommandeuser.html.twig',
		array('user'=>$user,'liste_panier'=>$liste_panier,'bareme'=>$service->getBareme(),'notemin'=>$notemin));
	}else{
		return $this->redirect($this->generateUrl('users_user_acces_plateforme'));
	}
}

public function soldergainuser(Souscategorie $scat, User $user, GeneralServicetext $service)
{
	$em = $this->getDoctrine()->getManager();
	if(isset($_POST['montant']) and is_numeric($_POST['montant']))
	{
		if($user->getSoldegain() >= $_POST['montant'])
		{
			$user->setSoldegain($user->getSoldegain() - $_POST['montant']);
			$em->flush();
			$this->get('session')->getFlashBag()->add('information','Le solde des gain de '.$user->name(20).' a été mis à jour avec succès');
			
			//envoie d'email
			$siteweb = $this->params->get('siteweb');
			$sitename = $this->params->get('sitename');
			$emailadmin = $this->params->get('emailadmin');
			
			$notif = new Notification();
			$notif->setTitre('Vous avez reçu un tranfert de '.$_POST['montant'].'FCFA de la part de '.$sitename.' !');
			$notif->setContenu('Merci pour votre confiance renouvelée !');
			$notif->setUser($user);
			$em->persist($notif);
			$em->flush();
			
			
			if($service->email($user->getUsername()))
			{

				$response = $this->_servicemail->sendNotifEmail(
					$user->name(40), //Nom du destinataire
					$user->getUsername(), //Email Destinataire
					'Vous avez reçu un tranfert de '.$_POST['montant'].'FCFA de la part de '.$sitename.' !', //Objet de l'email
					'Vous avez reçu un tranfert de '.$_POST['montant'].'FCFA de la part de '.$sitename.' !', //Grand Titre de l'email
					'Merci pour votre confiance renouvelée !',  //Contenu de l'email
					 ''  //Lien à suivre
				);
			}
		}else{
			$this->get('session')->getFlashBag()->add('information','Echec ! Le montant indiqué est superieur au solde gain de '.$user->name(20).'.');
		}
	}else{
		$this->get('session')->getFlashBag()->add('information','Echec ! Les données envoyés sont invalides.');
	}
	return $this->redirect($this->generateUrl('users_adminuser_allprod_archive_courant_souscategorie', array('id'=>$scat->getId())));
}

public function listealluser()
{
	$searchitem = '';
	if(isset($_POST['search']))
	{
		$searchitem = $_POST['search'];
	}else{
		$searchitem = '';
	}
	return $this->render('Theme/Users/Adminuser/User/listealluser.html.twig', array('searchitem'=>$searchitem));
}

public function searchinguser($page, $searchitem)
{
	if(isset($_GET['page']))
	{
	 $page = $_GET['page'];
	}else{
	 $page = $page;
	}
	
	$em = $this->getDoctrine()->getManager();
	if($searchitem == '')
	{
		$liste_user = $em->getRepository(User::class)
						 ->myFindAll($page,6);
	}else{
		$liste_user = $em->getRepository(User::class)
						 ->myFindAllUser($page,6, $searchitem);
	}
	
	return $this->render('Theme/Users/Adminuser/User/searchinguser.html.twig',
	array('nombrepage' => ceil(count($liste_user)/6),'page'=>$page,'liste_user'=>$liste_user, 'searchitem'=>$searchitem));
}

public function authoverlay()
{
	return $this->render('Theme/Users/User/User/authoverlay.html.twig');
}
}
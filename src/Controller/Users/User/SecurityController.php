<?php
namespace App\Controller\Users\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

use General\ServiceBundle\AfMail\Afmail;
use General\ServiceBundle\AfMail\fileAttachment;

use App\Entity\Users\User\Imgslide;
use App\Entity\Users\User\User;
use App\Entity\Produit\Produit\Produit;
use App\Entity\Produit\Service\Infoentreprise;
use App\Entity\Produit\Produit\Animationproduit;
use App\Entity\Produit\Produit\Categorie;
use App\Entity\Produit\Service\Service;
use App\Service\Servicetext\GeneralServicetext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use App\Service\Email\Singleemail;

class SecurityController extends AbstractController
{

private $params;
private $_servicemail;

public function __construct(ParameterBagInterface $params, Singleemail $servicemail)
{
	$this->params = $params;
	$this->_servicemail = $servicemail;
}

public function login(GeneralServicetext $service, Request $request)
{
	$em = $this->getDoctrine()->getManager();
	// Si le visiteur est déjà identifié, on le redirige vers l'accueil
	if($this->getUser() != null){ //IS_AUTHENTICATED_REMEMBERED
		return $this->redirect($this->generateUrl('users_user_acces_plateforme'));
	}

	//connexion sécurisé user.
	$error_login = '';
	$last_username = null;
	if($request->getMethod() == 'POST' and $this->getUser() == null){
		if (isset($_POST['_username']) and isset($_POST['_password'])){
			$repository = $em->getRepository(User::class);
			$user = $repository->findOneBy(array('username'=>$_POST['_username']));
			
			if($user != null)
			{
				if($_POST['_password'] == $service->decrypt($user->getPassword(),$user->getSalt()))
				{
					$token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
					$this->get('security.token_storage')->setToken($token);
					$this->get('session')->set('_security_main', serialize($token));

					// Verifie si le cookie n existe pas
					if((!isset($_COOKIE["PIDSESSREM"]) or $_COOKIE["PIDSESSREM"] == 'delete') and isset($_POST['_remember_me']) and $_POST['_remember_me'] == true)
					{
						// Stock les infos du cookie
						$cookie_info = array(
							'name'  => 'PIDSESSREM',
							'value' => $service->encrypt($user->getUsername(),$this->params->get('saltcookies')),
							'time'  => time() + (3600 * 24 * 360)
						);
						// Cree le cookie
						setCookie($cookie_info['name'], $cookie_info['value'], $cookie_info['time'],'/');
						setCookie('PIDSESSDUR',$cookie_info['time'], $cookie_info['time'],'/');
					}

					$session = $this->get('session');
					$target_link = $session->get('_security.welcome.target_path');
					if($target_link != null and strlen($target_link) > 5)
					{
						return $this->redirect($target_link);
					}else{
						return $this->redirect($this->generateUrl('users_user_acces_plateforme'));
					}
				}else{
					$error_login = '<span style="color: red;">Echec: Mot de passe ou Email invalide.</span>';
					$last_username = $_POST['username'];
				}
			}else{
				$last_username = $_POST['username'];
			}
		}
	}
	return $this->render('Theme/Users/User/Security/login.html.twig',
	array('last_username' => $last_username,'error'=> $error_login));
}


public function accueilsite(GeneralServicetext $service)
{
	$em = $this->getDoctrine()->getManager();
	$user = $this->getUser();
	if($this->isGranted('IS_AUTHENTICATED_REMEMBERED')){ //dès qu'un utilisateur se connecte il est redirigé vers le path / qui exécute directement ce controlleur.
	$dureelastvisite = round((time() - $user->getDatebeg())/60);
		if($dureelastvisite >= 3) //s'il ya plus de 3 minutes que utilisateur n'a pas actualisé sa sesion 
		{
			$user->setDatebeg(time());
			$em->flush();
		}
	}
	
	$allslide = $em->getRepository(Imgslide::class)
	                      ->findSlideAccueil();
	$slide_bg = $em->getRepository(Imgslide::class)
	                      ->findSlideConnexion();
	
	
	$liste_formateur = $em->getRepository(User::class)
	                      ->findFormateurs();
	
	$liste_produit = $em->getRepository(Produit::class)
	                    ->findBy(array('valide'=>1,'avant'=>1), array('rang'=>'desc'));	
	
	$article_faq = $em->getRepository(Infoentreprise::class)
	                   ->findBy(array('type'=>'article-faq'), array('rang'=>'desc'));
					   
	$article_avantage = $em->getRepository(Infoentreprise::class)
	                   ->findBy(array('type'=>'nos-avantage'), array('rang'=>'desc'));
					   
	$slideaccueil = $service->selectEntities($allslide,5);
	$slidebg = $service->selectEntity($slide_bg);

	foreach($slideaccueil as $video)
	{
		if($video->getProduit() != null)
		{
			$video->getProduit()->setEm($em);
		}
	}
	$liste_formateur = $service->selectEntities($liste_formateur, 10);
	$liste_produit = $service->selectEntities($liste_produit, 3);
	$article_faq = $service->selectEntities($article_faq, 7);
	$article_avantage = $service->selectEntities($article_avantage, 6);

	$tabproduit = array();
	foreach($liste_produit as $produit)
	{
		if(!in_array($produit->getId(), $tabproduit))
		{
			array_push($tabproduit,$produit->getId());
		}
	}
	
	$produit_enregistrer = $em->getRepository(Animationproduit::class)
	                          ->findBy(array('user'=>$this->getUser(), 'enregistrer'=>1), array('date'=>'desc'),3);	
							  
	$produit_aime = $em->getRepository(Produit::class)
	                    ->listeProduitPlusLike(6,$tabproduit);
	foreach($produit_aime as $produit)
	{
		if(!in_array($produit->getId(), $tabproduit))
		{
			array_push($tabproduit,$produit->getId());
		}
	}
	$produit_visite = $em->getRepository(Produit::class)
	                     ->listeProduitPlusVisite(6,$tabproduit);	

	$liste_categorie = $em->getRepository(Categorie::class)
	                      ->myFindAll();
	$liste_formation = $em->getRepository(Service::class)
	                      ->listeformation(1,8);
	foreach($liste_formation as $formation)
	{
		$formation->setEm($em);
	}
	
	$liste_recommandation = $em->getRepository(Animationproduit::class)
					           ->findBy(array('recommander'=>1,'user'=>$this->getUser()));
						
	return $this->render('Theme\Users\User\Security\accueilsite.html.twig',
	array('slideaccueil'=>$slideaccueil,'article_faq'=>$article_faq,'liste_formation'=>$liste_formation,
	'liste_formateur'=>$liste_formateur,'liste_produit'=>$liste_produit,'slidebg'=>$slidebg,
	'produit_enregistrer'=>$produit_enregistrer,'produit_aime'=>$produit_aime,'produit_visite'=>$produit_visite,
	'article_avantage'=>$article_avantage,'liste_categorie'=>$liste_categorie,'liste_recommandation'=>$liste_recommandation));
}

public function resetpassword(GeneralServicetext $service, $etape)
{
	$em = $this->getDoctrine()->getManager();
	$session = $this->get('session');
	if($etape == 1)
	{
		if(isset($_POST['username']))
		{
			$repository = $em->getRepository(User::class);
			$user = $repository->findOneBy(array('username'=>$_POST['username']));
			
			if($user != null)
			{
				$code = $user->getDatebeg();
				if($service->email($user->getUsername()))
				{
					$siteweb = $this->params->get('siteweb');
					$sitename = $this->params->get('sitename');
					$emailadmin = $this->params->get('emailadmin');
					

					$response = $this->_servicemail->sendNotifEmail($user->name(30), $user->getUsername(), 'Inscription Réussit', 'Le code d\'initialisation du mot de passe de votre compte '.$sitename.' a été généré avec succès !', 'Le code est: <strong style="font-size: 25px;">'.$code.'</strong></br></br> Si vous n\'avez pas demandé cette action, Aucune action n\'est requise de votre part.', '');
			
					$type = 1;
				}else{
					$type = 2;
				}
				
				return $this->render('Theme/Users/User/Security/resetpassword.html.twig',
				array('type' =>$type,'etape'=> $etape,'user'=>$user));
			}else{
				echo 0;
				exit;
			}
		}else{
			echo 0;
			exit;
		}
	}else if($etape == 2)
	{
		if(isset($_POST['code']) and isset($_POST['id']))
		{
			$repository = $em->getRepository(User::class);
			$user = $repository->find($_POST['id']);
			
			if($user != null and $user->getDatebeg() == trim($_POST['code']))
			{
				$session->set('reset_password', 1);
				
				
				return $this->render('Theme/Users/User/Security/resetpassword.html.twig',
				array('etape'=> $etape,'user'=>$user));
			}else{
				echo 0;
				exit;
			}
		}else{
			echo 0;
			exit;
		}
	}else if($etape == 3)
	{
		if(isset($_POST['password']) and isset($_POST['id']))
		{

			$repository = $em->getRepository(User::class);
			$user = $repository->find($_POST['id']);
			$reset_password = $session->get('reset_password');
				
			if($user != null and $reset_password == 1)
			{
				//sécurisation du mot de passe utilisateur
				$passuser = $_POST['password'];
				
				$salt = substr(crypt($passuser,''), 0, 16);
				$user->setSalt($salt);
				$newpassword = $service->encrypt($passuser,$salt);
				
				
				$user->setPassword($newpassword);
				$em->flush();
				
				return $this->render('Theme/Users/User/Security/resetpassword.html.twig',
				array('etape'=> $etape,'user'=> $user));
			}else{
				echo 0;
				exit;
			}

		}else{
			echo 0;
			exit;
		}
	}
	echo 0;
	exit;
}
}
<?php
/*(c) Noel Kenfack <noel.kenfack@yahoo.fr> Février 2015
*/
namespace App\Controller\Produit\Produit;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Response;
use App\Entity\Produit\Produit\Questionnaire;
use App\Entity\Produit\Produit\Proposition;
use App\Entity\Produit\Produit\Chapitrecours;
use App\Entity\Produit\Produit\Partiecours;
use App\Entity\Produit\Produit\Produitpanier;
use App\Entity\Produit\Produit\Animationproduit;
use App\Entity\Produit\Produit\Composquestionnaire;
use App\Form\Produit\Produit\QuestionnaireType;
use App\Service\Servicetext\GeneralServicetext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class QuestionnaireController extends AbstractController
{
private $params;

public function __construct(ParameterBagInterface $params)
{
	$this->params = $params;
}

public function nouvellequestion(Chapitrecours $chapitre, GeneralServicetext $service, Request $request)
{
	$em = $this->getDoctrine()->getManager();
	if(!isset($_POST['ident']))
	{
		$question = new Questionnaire();
		$formquestion = $this->createForm(QuestionnaireType::class, $question); 

		if ($request->getMethod() == 'POST'){
			$formquestion->handleRequest($request);
			if($formquestion->isValid()){
				$question->setChapitrecours($chapitre);
				$em->persist($question);
				$em->flush();

				$this->get('session')->getFlashBag()->add('information','Votre questionnaire a été mis à jour avec succès.');

			}else{
				$this->get('session')->getFlashBag()->add('information','Une erreur a été rencontrée !!! Vérifier la taille du titre [3,250]');
			}
		}
		return $this->redirect($this->generateUrl('produit_produit_user_modif_chapter', array('id'=>$chapitre->getId())));
	}else{
		$questionnaire = $em->getRepository(Questionnaire::class)
	                          ->findOneBy(array('id'=>$_POST['ident']));
		if($questionnaire != null and isset($_POST['titre']))
		{
			$questionnaire->setTitre($_POST['titre']);
			$em->flush();
			return $this->redirect($this->generateUrl('produit_produit_user_liste_propos_questionnaire', array('id'=>$chapitre->getId())));
		}
	}
	echo 'Une erreur a été rencontrée ! Données invalides.';
	exit;
}

public function questionnairenotion(Chapitrecours $chapitre, $src)
{
	$em = $this->getDoctrine()->getManager();
	$liste_questionnaire = $em->getRepository(Questionnaire::class)
	                          ->findBy(array('chapitrecours'=>$chapitre), array('date'=>'asc'));
	return $this->render('Theme/Produit/Produit/Questionnaire/questionnairenotion.html.twig', 
	array('chapitre'=>$chapitre,'liste_questionnaire'=>$liste_questionnaire, 'src'=>$src));
}

public function composquestionnaire(Chapitrecours $chapitre, $idpropan, $src, GeneralServicetext $service)
{
	$em = $this->getDoctrine()->getManager();
	$liste_questionnaire = $em->getRepository(Questionnaire::class)
	                          ->findBy(array('chapitrecours'=>$chapitre,'valide'=>true), array('date'=>'asc'));
	$produit = $chapitre->getPartiecours()->getProduit();
	
	$prodpan = $em->getRepository(Produitpanier::class)
				  ->find($idpropan);
	$etat = 'inconnu';
	$recenteDateValidation = 0;
	$repeat = $this->params->get('repeattime');
	$indexj = 0;
	if($prodpan !=  null)
	{
		foreach($liste_questionnaire as $question)
		{
			$oldcompos = $em->getRepository(Composquestionnaire::class)
							->findOneBy(array('produitpanier'=>$prodpan,'questionnaire'=>$question));
			if($oldcompos != null and $oldcompos->getProposition() != null)
			{
				$question->setElement($oldcompos->getProposition()->getId());
				
				if($recenteDateValidation < $oldcompos->getLastvalidation())
				{
					$recenteDateValidation = $oldcompos->getLastvalidation();
				}
				if($indexj == 0)
				{
					if($oldcompos->getFermer() == true)
					{
						$etat = 'fermer';
					}else{
						$etat = 'ouvert';
					}
				}
				$indexj++;
			}
		}
	}
	$waitTimePerMin = $repeat*60 - ceil((time() - $recenteDateValidation)/60);
	$composeAllQuestion = false;
	$notemin = $this->params->get('notemin');
	if($indexj == count($liste_questionnaire))
	{
		$composeAllQuestion = true;
	}

	$liste_part = $em->getRepository(Partiecours::class)
					 ->findBy(array('produit'=>$chapitre->getPartiecours()->getProduit()), array('rang'=>'asc'));

	$trouver = false;
	$prechapter = null;
	foreach($liste_part as $part)
	{
		$part->setEm($em);
		$all_chapter = $part->getAllChapitre();
		foreach($all_chapter as $chapter)
		{
			if($trouver == false)
			{
				if($chapter == $chapitre)
				{
					$trouver = true;
					break;
				}else{
					$prechapter = $chapter;
					$chapter->setEm($em);
				}
			}
		}
		if($trouver == true and $prechapter != null)
		{
			break;
		}
	}
	
	
	return $this->render('Theme/Produit/Produit/Questionnaire/composquestionnaire.html.twig', 
	array('chapitre'=>$chapitre,'prechapter'=>$prechapter,'notemin'=>$notemin,'liste_questionnaire'=>$liste_questionnaire,
	'prodpan'=>$prodpan, 'src'=>$src, 'etat'=> $etat, 'waitTimePerMin'=>$waitTimePerMin, 'composeAllQuestion'=>$composeAllQuestion));
}

/**
 * Cette fonction doit Déterminer la note de l'élève et cloturé le questionnaire de cet élève pour ce chapitre.
 */
public function validationquestionnaire(Chapitrecours $chapitre, Produitpanier $prodpan, GeneralServicetext $service, Request $request)
{
	$em = $this->getDoctrine()->getManager();
	$liste_questionnaire = $em->getRepository(Questionnaire::class)
	                          ->findBy(array('chapitrecours'=>$chapitre,'valide'=>true), array('date'=>'asc'));
	$produit = $chapitre->getPartiecours()->getProduit();
	$update = true;
	$repeat = $this->params->get('repeattime');
	$notemin = $this->params->get('notemin');
	$chapitre->setEm($em);

	$oldanimation = $em->getRepository(Animationproduit::class)
					   ->findOneBy(array('user'=>$this->getUser(),'chapitrecours'=>$chapitre,'produitpanier'=>$prodpan,'voir'=>1));
	
	if($oldanimation != null){ //S'il a la possibilité de composer à nouveau, on annulle l'ancienne note.
		$oldanimation->setNotechapter(null);
		$oldanimation->setBaremechapter(null);
		$em->flush();
	}	

	$noteuser = $chapitre->getNoteQuestionnaire($prodpan);
	if($noteuser >= $notemin)
	{
		$fermer = true;
	}else{
		$fermer = false;
	}

	

	if($request->getMethod() == 'POST' and $this->getUser() == $prodpan->getPanier()->getUser() and $oldanimation != null)
	{
		foreach($liste_questionnaire as $question)
		{
			$oldcompos = $em->getRepository(Composquestionnaire::class)
							->findOneBy(array('produitpanier'=>$prodpan,'questionnaire'=>$question));
			if($oldcompos != null and $oldcompos->getProposition() != null)
			{
				$oldcompos->setFermer($fermer);
				$oldcompos->setLastvalidation(time());
			}else{
				$update = false;
				break;
			}
		}

		if($update == true)
		{
			if($fermer == true)
			{
				$oldanimation->setNotechapter($noteuser);
				$oldanimation->setBaremechapter($service->getBareme());
				$this->get('session')->getFlashBag()->add('information','Votre feuille de composition a été soumise avec succès. Vous avez eu une note de '.$noteuser.'/'.$service->getBareme());
			}else{
				$this->get('session')->getFlashBag()->add('information','Echec !!! Vous avez eu une note éliminatoire de '.$noteuser.'/'.$service->getBareme().' Rééssayez plutard.');
			}
			$em->flush();
		}else{
			$this->get('session')->getFlashBag()->add('information','Echec ! Il ya des questions que vous n\'avez pas répondu.');
		}
	}else{
		if($oldanimation == null){
			$this->get('session')->getFlashBag()->add('information','Vous n\'avez pas encore regardé la vidéo assossiée à cette formation.');
		}else{
			$this->get('session')->getFlashBag()->add('information','Echec ! Vous n\'avez pas le droit de valider ce questionnaire.');
		}
	}
	return $this->redirect($this->generateUrl('produit_produit_presentation_chapter', array('id'=>$chapitre->getId())));
}

/**
 * Cette fonction doit mettre à jour le choix de l'élève si questionnaire non fermer.
 */
public function updatecomposition(Produitpanier $propan)
{
	$em = $this->getDoctrine()->getManager();
	
	if(isset($_POST['id']) and isset($_POST['question']))
	{
		$proposition = $em->getRepository(Proposition::class)
	                      ->find($_POST['id']);
		$questionnaire = $em->getRepository(Questionnaire::class)
	                        ->find($_POST['question']);
		$repeat = $this->params->get('repeattime');
		if($proposition != null and $questionnaire != null)
		{
			$oldcompos = $em->getRepository(Composquestionnaire::class)
	                        ->findOneBy(array('produitpanier'=>$propan,'questionnaire'=>$questionnaire));
			if($oldcompos != null)
			{
				if(((time() - $oldcompos->getLastvalidation())/3600) >= $repeat and $oldcompos->getFermer() == false)
				{
					$oldcompos->setProposition($proposition);
				}else{
					if($oldcompos->getFermer() == true)
					{
						echo -1;
						exit;
					}else{
						echo ($repeat - ceil((time() - $oldcompos->getLastvalidation())/3600));
						exit;
					}
				}
			}else{
				$compos = new Composquestionnaire();
				$compos->setQuestionnaire($questionnaire);
				$compos->setProduitpanier($propan);
				$compos->setProposition($proposition);
				$em->persist($compos);
			}
			$em->flush();
			return $this->redirect($this->generateUrl('produit_produit_liste_questionnaire_composition_chapter', array('id'=>$questionnaire->getChapitrecours()->getId(), 'idpropan'=>$propan->getId())));
		}
	}
	echo 'Une erreur a été rencontrée ! Données n\'ont reçus.';
	exit;
}

public function addnewproposition(Chapitrecours $chapitre)
{
	$em = $this->getDoctrine()->getManager();
	if(isset($_POST['id']) and isset($_POST['titre']))
	{
		$questionnaire = $em->getRepository(Questionnaire::class)
	                          ->find($_POST['id']);
		if($questionnaire != null)
		{
			$question = new Proposition();
			$question->setTitre(htmlspecialchars($_POST['titre']));
			$question->setQuestionnaire($questionnaire);
			$em->persist($question);
			$em->flush();
			return $this->redirect($this->generateUrl('produit_produit_user_liste_propos_questionnaire', array('id'=>$chapitre->getId())));
		}
	}
	echo 'Une erreur a été rencontrée ! Données n\'ont reçus.';
	exit;
}

public function updatereponse(Chapitrecours $chapitre)
{
	$em = $this->getDoctrine()->getManager();
	if(isset($_POST['id']))
	{
		$proposition = $em->getRepository(Proposition::class)
	                          ->find($_POST['id']);
		if($proposition != null)
		{
			$question = $proposition->getQuestionnaire();
			$liste_propos = $question->getPropositions();
			
			foreach($liste_propos as $propos)
			{
				$propos->setReponse(false);
			}
			$em->flush();
			
			$proposition->setReponse(true);
			$em->flush();
			return $this->redirect($this->generateUrl('produit_produit_user_liste_propos_questionnaire', array('id'=>$chapitre->getId())));
		}
	}
	echo 'Une erreur a été rencontrée ! Données n\'ont reçus.';
	exit;
}

public function supprimerproposition(Chapitrecours $chapitre)
{
	$em = $this->getDoctrine()->getManager();
	if(isset($_POST['id']))
	{
		$proposition = $em->getRepository(Proposition::class)
	                      ->find($_POST['id']);
		if($proposition != null and !isset($_POST['titre']))
		{
			$em->remove($proposition);
			$em->flush();
			return $this->redirect($this->generateUrl('produit_produit_user_liste_propos_questionnaire', array('id'=>$chapitre->getId())));
		}else{
			if($proposition != null and isset($_POST['titre']))
			{
				$proposition->setTitre($_POST['titre']);
				$em->flush();
				return $this->redirect($this->generateUrl('produit_produit_user_liste_propos_questionnaire', array('id'=>$chapitre->getId())));
			}
		}
	}
	echo 'Une erreur a été rencontrée ! Données n\'ont reçus.';
	exit;
}

public function validatequestion(Chapitrecours $chapitre)
{
	$em = $this->getDoctrine()->getManager();
	if(isset($_POST['id']))
	{
		$question = $em->getRepository(Questionnaire::class)
	                          ->find($_POST['id']);
		if($question != null)
		{
			if($question->getValide() == true)
			{
				$question->setValide(false);
			}else{
				$question->setValide(true);
			}
			$em->flush();
			return $this->redirect($this->generateUrl('produit_produit_user_liste_propos_questionnaire', array('id'=>$chapitre->getId())));
		}
	}
	echo 'Une erreur a été rencontrée ! Données n\'ont reçus.';
	exit;
}

public function deletequestionnaire(Chapitrecours $chapitre)
{
	$em = $this->getDoctrine()->getManager();
	if(isset($_POST['id']))
	{
		$questionnaire = $em->getRepository(Questionnaire::class)
	                        ->find($_POST['id']);
		if($questionnaire != null)
		{
			$em->remove($questionnaire);
			$em->flush();
			return $this->redirect($this->generateUrl('produit_produit_user_liste_propos_questionnaire', 
			array('id'=>$chapitre->getId())));
		}
	}
	echo 'Une erreur a été rencontrée ! Données n\'ont reçus.';
	exit;
}
}
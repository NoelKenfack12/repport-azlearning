<?php
/*
	(c) Noel Kenfack <noel.kenfack@yahoo.fr>
*/
namespace App\Controller\Produit\Service;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Produit\Service\Infoentreprise;
use App\Form\Produit\Service\InfoentrepriseType;
use App\Entity\Produit\Service\Detailinfoentreprise;
use App\Form\Produit\Service\DetailinfoentrepriseType;

use App\Service\Servicetext\GeneralServicetext;
use Symfony\Component\HttpFoundation\Request;

class InfoentrepriseController extends AbstractController
{
public function infoentreprise($page, GeneralServicetext $service, Request $request)
{
	$em = $this->getDoctrine()->getManager();
	$info = new Infoentreprise($service);
	$form = $this->createForm(InfoentrepriseType::class, $info);
	$formsupp = $this->createFormBuilder()->getForm();

	if($request->getMethod() == 'POST')
	{
		$form->handleRequest($request);
		$info->setUser($this->getUser());
		
		if(isset($_POST['type']))
		{
			$info->setType($_POST['type']);
		}
		
		if(isset($_POST['detailarticle']))
		{
			$info->setDetail($_POST['detailarticle']);
		}
		
		if($info->getImginfoentreprise() !== null)
		{
			$info->getImginfoentreprise()->setServicetext($service);
		}
		
		$aucuneinfo = false;
		if($info->getTitre() == null and $info->getDescription() == null)
		{
			$aucuneinfo = true;
		}
		if($form->isValid() and $aucuneinfo == false){
			$em->persist($info);
			$em->flush();
			$this->get('session')->getFlashBag()->add('information','Publication enregistrée avec succès.');
		}else{
			if($aucuneinfo == true)
			{
				$this->get('session')->getFlashBag()->add('information','Vous ne pouvez laissez le titre et le contenu vide.');
			}else{
			$this->get('session')->getFlashBag()->add('information','Une erreur a été rencontrée.');
			}
		}
	}
	$liste_article = $em->getRepository(Infoentreprise::class)
	                      ->listeAllArticleEntreprise($page, 5);
    return $this->render('Theme/Users/Adminuser/Infoentreprise/infoentreprise.html.twig',
	array('form'=>$form->createView(),'allinfo'=>$liste_article,'page'=>$page,'nombrepage' => ceil(count($liste_article)/5),
	'formsupp'=>$formsupp->createView()));
}

public function supprimerinfoentreprise(Infoentreprise $info, Request $request)
{
	$em = $this->getDoctrine()->getManager();
	$formsupp = $this->createFormBuilder()->getForm(); 
	if ($request->getMethod() == 'POST') {
		$formsupp->handleRequest($request);
		if ($formsupp->isValid()){
			$details = $info->getDetailinfoentreprises();
			foreach($details as $detail)
			{
				$em->remove($detail);
			}
			$em->remove($info);
			$em->flush();
			$this->get('session')->getFlashBag()->add('information','Suppression effectuée avec succès');
			return $this->redirect($this->generateUrl('users_adminuser_gestion_info_entreprise'));
		}
	}
    $this->get('session')->getFlashBag()->add('supprime_apropos',$info->getId());
	$this->get('session')->getFlashBag()->add('supprime_apropos',$info->getTitre());
	return $this->redirect($this->generateUrl('users_adminuser_gestion_info_entreprise'));
}

public function modifierinfoentreprise(Infoentreprise $info, $page, GeneralServicetext $service, Request $request)
{
	$em = $this->getDoctrine()->getManager();
	$form = $this->createForm(InfoentrepriseType::class, $info);
	$formsupp = $this->createFormBuilder()->getForm();

	if($request->getMethod() == 'POST')
	{
		$form->handleRequest($request);
		$info->setUser($this->getUser());
		
		if(isset($_POST['type']))
		{
			$info->setType($_POST['type']);
		}
		
		if(isset($_POST['detailarticle']))
		{
			$info->setDetail($_POST['detailarticle']);
		}
		
		if($info->getImginfoentreprise() !== null)
		{
			$info->getImginfoentreprise()->setServicetext($service);
		}
		$aucuneinfo = false;
		if($info->getTitre() == null and $info->getDescription() == null)
		{
			$aucuneinfo = true;
		}
		if($form->isValid() and $aucuneinfo == false){
			$em->persist($info);
			$em->flush();
			$this->get('session')->getFlashBag()->add('information','Modification effectuée succès.');
			return $this->redirect($this->generateUrl('users_adminuser_gestion_info_entreprise'));
		}else{
			if($aucuneinfo == true)
			{
				$this->get('session')->getFlashBag()->add('information','Vous ne pouvez laissez le titre et le contenu vide.');
			}else{
			$this->get('session')->getFlashBag()->add('information','Une erreur a été rencontrée.');
			}
		}
	}
	$detailinfo = new Detailinfoentreprise();
	$formdetail = $this->createForm(DetailinfoentrepriseType::class, $detailinfo);
	
	$liste_article = $em->getRepository(Infoentreprise::class)
	                      ->listeAllArticleEntreprise($page, 5);
						 			  
    return $this->render('Theme/Users/Adminuser/Infoentreprise/modifierinfoentreprise.html.twig',
	array('form'=>$form->createView(),'allinfo'=>$liste_article,'formsupp'=>$formsupp->createView(),'info'=>$info,
	'formdetail'=>$formdetail->createView(), 'page'=>$page,'nombrepage' => ceil(count($liste_article)/5),));
}

public function ajouterinfoentreprise(Infoentreprise $info)
{
	$em = $this->getDoctrine()->getManager();
	$detailinfo = new Detailinfoentreprise();
	$formdetail = $this->createForm(DetailinfoentrepriseType::class, $detailinfo);

	if($request->getMethod() == 'POST')
	{
		$formdetail->handleRequest($request);
		$detailinfo->setUser($this->getUser());
		$detailinfo->setInfoentreprise($info);
		$aucuneinfo = false;
		if($detailinfo->getTitre() == null and $detailinfo->getDescription() == null)
		{
			$aucuneinfo = true;
		}
		if($formdetail->isValid() and $aucuneinfo == false){
			$em->persist($detailinfo);
			$em->flush();
			$this->get('session')->getFlashBag()->add('information','Publication enregistrée avec succès.');
		}else{
			if($aucuneinfo == true)
			{
				$this->get('session')->getFlashBag()->add('information','Vous ne pouvez laissez le titre et le contenu vide.');
			}else{
			$this->get('session')->getFlashBag()->add('information','Une erreur a été rencontrée.');
			}
		}
	}
	return $this->redirect($this->generateUrl('users_adminuser_modification_information_entreprise_courant',array('id'=>$info->getId())));
}

public function supprimerdetailinfoentreprise(Detailinfoentreprise $detailinfo)
{
	$em = $this->getDoctrine()->getManager();
	$formsupp = $this->createFormBuilder()->getForm(); 

	$info = $detailinfo->getInfoentreprise();
	if ($request->getMethod() == 'POST') {
		$formsupp->handleRequest($request);
		if ($formsupp->isValid()){
			$em->remove($detailinfo);
			$em->flush();
			$this->get('session')->getFlashBag()->add('information','Suppression effectuée avec succès');
			return $this->redirect($this->generateUrl('users_adminuser_modification_information_entreprise_courant',array('id'=>$info->getId())));
		}
	}
    $this->get('session')->getFlashBag()->add('supprime_apropos',$detailinfo->getId());
	$this->get('session')->getFlashBag()->add('supprime_apropos',$detailinfo->getTitre());
	return $this->redirect($this->generateUrl('users_adminuser_modification_information_entreprise_courant',array('id'=>$info->getId())));
}

public function gallerystruct(GeneralServicetext $service)
{
	$em = $this->getDoctrine()->getManager();		  
	$article_galerie = $em->getRepository(Infoentreprise::class)
	                   ->findBy(array('type'=>'article-galerie'), array('rang'=>'desc'));
	
	$article_slide = $em->getRepository(Infoentreprise::class)
	                    ->findBy(array('type'=>'slide-galerie'), array('rang'=>'desc'));	
	$article_slide = $service->selectEntities($article_slide,2);
	
	return $this->render('Theme/Produit/Service/Infoentreprise/gallerystruct.html.twig', 
	array('article_galerie'=>$article_galerie, 'article_slide'=>$article_slide));
}

public function videosstruct(GeneralServicetext $service)
{
	$em = $this->getDoctrine()->getManager();		  

	$article_galerie = $em->getRepository(Infoentreprise::class)
	                      ->findBy(array('type'=>'lien-video'), array('rang'=>'desc'));
	
	$article_slide = $em->getRepository(Infoentreprise::class)
	                    ->findBy(array('type'=>'slide-video'), array('rang'=>'desc'));	
	$article_slide = $service->selectEntities($article_slide,2);
	
	return $this->render('Theme/Produit/Service/Infoentreprise/videosstruct.html.twig',
	array('article_galerie'=>$article_galerie, 'article_slide'=>$article_slide));
}

public function azelearningfaq(GeneralServicetext $service)
{
	$em = $this->getDoctrine()->getManager();		  
	$article_faq = $em->getRepository(Infoentreprise::class)
	                   ->findBy(array('type'=>'article-faq'), array('rang'=>'desc'));
	
	$article_slide = $em->getRepository(Infoentreprise::class)
	                    ->findBy(array('type'=>'slide-faq'), array('rang'=>'desc'));	
	$article_slide = $service->selectEntities($article_slide,2);
	
	return $this->render('Theme/Produit/Service/Infoentreprise/azelearningfaq.html.twig',
	array('article_faq'=>$article_faq,'article_slide'=>$article_slide));
}
}
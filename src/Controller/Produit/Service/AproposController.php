<?php
/*
	(c) Noel Kenfack <noel.kenfack@yahoo.fr>
*/
namespace App\Controller\Produit\Service;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Produit\Service\Apropos;
use App\Form\Produit\Service\AproposType;
use App\Service\Servicetext\GeneralServicetext;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Produit\Service\Infoentreprise;

class AproposController extends AbstractController
{
public function direapropos(GeneralServicetext $service, Request $request)
{
	$em = $this->getDoctrine()->getManager();
	$apropos = new Apropos($service);
	$form = $this->createForm(AproposType::class, $apropos);
	$formsupp = $this->createFormBuilder()->getForm();

	if($request->getMethod() == 'POST')
	{
		$form->handleRequest($request);
		$apropos->setUser($this->getUser());
		if($form->isValid()){
			if (isset($_POST['nature']) and $_POST['nature'] == 'entreprise'){
				$apropos->setEntreprise(true);
			}
			if (isset($_POST['nature']) and $_POST['nature'] == 'particulier'){
				$apropos->setParticulier(true);
			}
			if (isset($_POST['nature']) and $_POST['nature'] == 'team'){
				$apropos->setTeam(true);
			}
			if (isset($_POST['nature']) and $_POST['nature'] == 'slider'){
				$apropos->setSlider(true);
			}
			$em->persist($apropos);
			$em->flush();
			$this->get('session')->getFlashBag()->add('information','Publication enregistrée avec succès.');
		}
	}
	$alldire = $em->getRepository(Apropos::class)
	                      ->FindAll();
    return $this->render('Theme/Users/Adminuser/Apropos/direapropos.html.twig',
	array('form'=>$form->createView(),'alldire'=>$alldire,'formsupp'=>$formsupp->createView()));
}

public function modifierapropos(Apropos $propos, GeneralServicetext $service, Request $request)
{
	$em = $this->getDoctrine()->getManager();
	$form = $this->createForm(AproposType::class, $propos);
	$formsupp = $this->createFormBuilder()->getForm();
	$propos->setServicetext($service);

	if($request->getMethod() == 'POST')
	{
		$form->handleRequest($request);
		$propos->setUser($this->getUser());
		if($form->isValid()){
			if (isset($_POST['nature']) and $_POST['nature'] == 'entreprise'){
				$propos->setEntreprise(true);
			}
			if (isset($_POST['nature']) and $_POST['nature'] == 'particulier'){
				$propos->setParticulier(true);
			}
			if (isset($_POST['nature']) and $_POST['nature'] == 'team'){
				$propos->setTeam(true);
			}
			if (isset($_POST['nature']) and $_POST['nature'] == 'slider'){
				$propos->setSlider(true);
			}
			$em->persist($propos);
			$em->flush();
			$this->get('session')->getFlashBag()->add('information','Modification enregistrée avec succès.');
		}
	}
	$alldire = $em->getRepository(Apropos::class)
	              ->FindAll();
    return $this->render('Theme/Users/Adminuser/Apropos/modifierapropos.html.twig',
	array('form'=>$form->createView(),'apropos'=>$propos,'alldire'=>$alldire,'formsupp'=>$formsupp->createView()));
}

public function deleteapropos(Apropos $apropos, Request $request)
{
	$em = $this->getDoctrine()->getManager();
	$formsupp = $this->createFormBuilder()->getForm(); 

	if ($request->getMethod() == 'POST'){
    	$formsupp->handleRequest($request);
		if ($formsupp->isValid()){
			$em->remove($apropos);
			$em->flush();
			$this->get('session')->getFlashBag()->add('information','Suppression effectuée avec succès');
			return $this->redirect($this->generateUrl('users_adminuser_apropos_de_nous'));
		}
	}
	
    $this->get('session')->getFlashBag()->add('supprime_apropos',$apropos->getId());
	$this->get('session')->getFlashBag()->add('supprime_apropos',$apropos->getNom());
	return $this->redirect($this->generateUrl('users_adminuser_apropos_de_nous'));
}

public function aproposdenous(GeneralServicetext $service)
{
	$em = $this->getDoctrine()->getManager();		  
	$article_slide = $em->getRepository(Infoentreprise::class)
	                    ->findBy(array('type'=>'slide-about'), array('rang'=>'desc'));					
	$article_about = $em->getRepository(Infoentreprise::class)
	                    ->findBy(array('type'=>'article-about'), array('rang'=>'desc'));
	$article_slide = $service->selectEntities($article_slide,2);

	
	return $this->render('Theme/Produit/Service/Apropos/aproposdenous.html.twig',
	array('article_slide'=>$article_slide, 'article_about'=>$article_about));
}

}
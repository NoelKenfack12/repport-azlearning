<?php
/*(c) Noel Kenfack <noel.kenfack@yahoo.fr> Février 2015
*/
namespace App\Controller\Users\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Users\User\Imgslide;
use App\Form\Users\User\ImgslideType;
use App\Service\Servicetext\GeneralServicetext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use App\Entity\Produit\Produit\Produit;

class ImgslideController extends AbstractController
{

private $params;
public function __construct(ParameterBagInterface $params)
{
	$this->params = $params;
}

public function addnewslide(GeneralServicetext $service, Request $request)
{
	$em = $this->getDoctrine()->getManager();
	$slide = new Imgslide($service);
	$formslide = $this->createForm(ImgslideType::class, $slide);

	if($request->getMethod() == 'POST')
	{
		$formslide->handleRequest($request);
		$slide->setUser($this->getUser());
		$slide->setServicetext($service);
		$allslide = $em->getRepository(Imgslide::class)
	                      ->FindAll();
		$nbslide = $this->params->get('nbslide');
		if ($formslide->isValid() and count($allslide) <= $nbslide){
			if (isset($_POST['nature']) and $_POST['nature'] == 'accueil'){
				$slide->setAcceuil(true);
			}
			if(isset($_POST['nature']) and $_POST['nature'] == 'forum'){
				$slide->setAcceuil(false);
			}
			if(isset($_POST['nature']) and $_POST['nature'] == 'inscription'){
				$slide->setAnnonce(true);
			}
			
			if($_POST['idproduit'])
			{
				$produit = $em->getRepository(Produit::class)
	                          ->find($_POST['idproduit']);
				$slide->setProduit($produit);
			}
			$em->persist($slide);
			$em->flush();
		}else{
			if(count($allslide) > $nbslide)
			{
				$this->get('session')->getFlashBag()->add('imgslide','Trop de slide.');
			}else{
				$this->get('session')->getFlashBag()->add('imgslide','Données invalides.');
			}
		}
	}
	return $this->redirect($this->generateUrl('users_adminuser_accueil_administration'));
}

public function deleteslide(Imgslide $slide, Request $request)
{
	$em = $this->getDoctrine()->getManager();
	$formsupp = $this->createFormBuilder()->getForm(); 
    
	if ($request->getMethod() == 'POST') {
		$formsupp->handleRequest($request);
		if ($formsupp->isValid()){
			$em->remove($slide);
			$em->flush();
			$this->get('session')->getFlashBag()->add('information','Suppression effectuée avec succès');
			return $this->redirect($this->generateUrl('users_adminuser_accueil_administration'));
		}
	}
    $this->get('session')->getFlashBag()->add('supprime_slide',$slide->getId());
	$this->get('session')->getFlashBag()->add('supprime_slide',$slide->getTitre());
	return $this->redirect($this->generateUrl('users_adminuser_accueil_administration'));
}

public function listeslide()
{
	$em = $this->getDoctrine()->getManager();
	$allslide = $em->getRepository(Imgslide::class)
	                      ->FindAll();
	return $this->render('Theme/Users/Adminuser/Accueil/listeslide.html.twig', 
	array('allslide'=>$allslide));
}

public function modifierslide(Imgslide $slide, GeneralServicetext $service, Request $request)
{
	$em = $this->getDoctrine()->getManager();
	$formslide = $this->createForm(ImgslideType::class, $slide);
	
	if($request->getMethod() == 'POST')
	{
		$formslide->handleRequest($request);
		$slide->setServicetext($service);
		if ($formslide->isValid()){
			if (isset($_POST['nature']) and $_POST['nature'] == 'accueil'){
				$slide->setAcceuil(true);
				$slide->setAnnonce(false);
			}
			if(isset($_POST['nature']) and $_POST['nature'] == 'forum'){
				$slide->setAcceuil(false);
				$slide->setAnnonce(false);
			}
			if(isset($_POST['nature']) and $_POST['nature'] == 'inscription'){
				$slide->setAcceuil(false);
				$slide->setAnnonce(true);
			}
			
			if($_POST['idproduit'])
			{
				$produit = $em->getRepository(Produit::class)
	                          ->find($_POST['idproduit']);
				$slide->setProduit($produit);
			}
			
			$em->flush();
			return $this->redirect($this->generateUrl('users_adminuser_accueil_administration'));
		}else{
			$this->get('session')->getFlashBag()->add('imgslide','Données invalides. Vérifier la taille de l\'image');
		}
	}
	return $this->render('Theme/Users/Adminuser/Accueil/modifierslide.html.twig',
	array('formslide'=>$formslide->createView(),'slide'=>$slide));
}
}
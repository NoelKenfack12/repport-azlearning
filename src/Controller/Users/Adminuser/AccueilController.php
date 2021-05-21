<?php
/*(c) Noel Kenfack <noel.kenfack@yahoo.fr> Février 2015
*/
namespace App\Controller\Users\Adminuser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Produit\Produit\Categorie;
use App\Form\Produit\Produit\CategorieType;
use App\Entity\Produit\Produit\Souscategorie;
use App\Form\Produit\Produit\SouscategorieType;
use App\Entity\Users\User\Imgslide;
use App\Form\Users\User\ImgslideType;
use App\Entity\Produit\Produit\Panier;
use App\Entity\Produit\Service\Recrutement;
use App\Service\Servicetext\GeneralServicetext;
use Symfony\Component\HttpFoundation\Request;

class AccueilController extends AbstractController
{
public function accueiladmin(GeneralServicetext $service)
{
	$em = $this->getDoctrine()->getManager();
	
	$nbcategorie = $em->getRepository(Categorie::class)
	                      ->FindAll();
	$categorie = new Categorie($service);
	$form = $this->createForm(CategorieType::class, $categorie);
	$souscategorie = new Souscategorie($service);
	$formcat = $this->createForm(SouscategorieType::class, $souscategorie);
	$slide = new Imgslide($service);
	$formslide = $this->createForm(ImgslideType::class, $slide);
	$formsupp = $this->createFormBuilder()->getForm();
    return $this->render('Theme/Users/Adminuser/Accueil/accueiladmin.html.twig',
	array('form'=>$form->createView(),'nbcategorie'=>$nbcategorie,'formcat'=>$formcat->createView(),
	'formslide'=>$formslide->createView(),
	'formsupp'=>$formsupp->createView()));
}

public function categorieproduit(GeneralServicetext $service)
{
	$em = $this->getDoctrine()->getManager();
	$categorie = new Categorie($service);
	$form = $this->createForm(CategorieType::class, $categorie);
	$souscategorie = new Souscategorie($service);
	$formcat = $this->createForm(SouscategorieType::class, $souscategorie);
	$formsupp = $this->createFormBuilder()->getForm();
	
	$liste_categorie = $em->getRepository(Categorie::class)
	                      ->findAll();
						  
    return $this->render('Theme/Users/Adminuser/Accueil/categorieproduit.html.twig',
	array('form'=>$form->createView(),'formcat'=>$formcat->createView(),
	'formsupp'=>$formsupp->createView(),'liste_categorie'=>$liste_categorie));
}

public function menuadmin()
{
	$em = $this->getDoctrine()->getManager();
	$liste_commande = $em->getRepository(Panier::class)
				         ->findBy(array('payer'=>1,'livrer'=>0));
	$liste_vente = $em->getRepository(Panier::class)
				      ->findBy(array('payer'=>1,'livrer'=>1));
	$nbrecrutement = $em->getRepository(Recrutement::class)
	                    ->findAll();
	return $this->render('Theme/Users/Adminuser/Accueil/menuadmin.html.twig',
	array('nbcommande'=>count($liste_commande),'nbvente'=>count($liste_vente),'nbrecrutement'=>count($nbrecrutement)));
}	
}
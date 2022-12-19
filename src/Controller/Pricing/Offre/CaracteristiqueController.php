<?php

namespace App\Controller\Pricing\Offre;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\Servicetext\GeneralServicetext;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Pricing\Offre\Caracteristique;
use App\Form\Pricing\Offre\CaracteristiqueType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Entity\Pricing\Offre\Offre;
use App\Entity\Pricing\Offre\Caracteristiqueproduit;

class CaracteristiqueController extends AbstractController
{
    private $_entityManager;
    public function __construct(EntityManagerInterface $em)
    {
        $this->_entityManager = $em;
    }

    public function ajoutercaracteristique(GeneralServicetext $service, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $caracteristique = new Caracteristique();
        $formcarac = $this->createForm(CaracteristiqueType::class, $caracteristique);
        if($request->getMethod() == 'POST')
        {
            $caracteristique->setUser($this->getUser());
            $formcarac->handleRequest($request);
            if($formcarac->isValid()){ 
                $em->persist($caracteristique);
                $em->flush();
                $this->get('session')->getFlashBag()->add('information','Enregistrement effectué avec succès');
            }else{
                $this->get('session')->getFlashBag()->add('information','Données invalides.');
            }
        }
        return $this->redirect($this->generateUrl('users_adminuser_liste_produit'));
    }

    public function updatecaracteristique(GeneralServicetext $service, Request $request, EntityManagerInterface $em, $id)
    {
        if(isset($_GET['id']))
        {
            $id = $_GET['id'];
        }else{
            $id = $id;
        }
        $caracteristique = $em->getRepository(Caracteristique::class)
                         ->find($id);
        if($caracteristique != null)
        {
            $formcarac = $this->createForm(CaracteristiqueType::class, $caracteristique);
            if ($request->getMethod() == 'POST'){
                $formcarac->handleRequest($request);
                if ($formcarac->isValid()){
                    $caracteristique->setDate(new \Datetime());
                    $em->flush();
                    $this->get('session')->getFlashBag()->add('information','Modification effectuée avec succès');
                }else{
                    $this->get('session')->getFlashBag()->add('information','Une ereur a été rencontrée!');
                }
                return $this->redirect($this->generateUrl('users_adminuser_liste_produit'));
            }
            return $this->render('Theme/Users/Adminuser/Offre/updatecaracteristique.html.twig',
            array('formcarac'=>$formcarac->createView(),'caracteristique'=>$caracteristique));
        }else{
            echo 'Echec ! Une erreur a été rencontrée.';
            exit;
        }
    }

    public function removecaracteristique(Caracteristique $caracteristique)
    {
        $this->_entityManager->remove($caracteristique);
        $this->_entityManager->flush();
        $this->get('session')->getFlashBag()->add('information','Suppression effectuée avec succès');
        return $this->redirect($this->generateUrl('users_adminuser_liste_produit'));
    }

    public function updatecaracteristiqueproduit(ValidatorInterface $validator, GeneralServicetext $service, Request $request, $idproduit, $idoffre)
    {
        $em = $this->getDoctrine()->getManager();
        if(isset($_GET['idproduit']) and isset($_GET['idoffre']))
        {
            $idproduit = $_GET['idproduit'];
            $idoffre = $_GET['idoffre'];
        }else{
            $idproduit = $idproduit;
            $idoffre = $idoffre;
        }
        
        $produit = $em->getRepository(Offre::class)
                      ->find($idproduit);
        $caracteristique = $em->getRepository(Caracteristique::class)
                              ->find($idoffre);
        if($produit != null and $caracteristique != null)
        {
        $caracteristiqueplan = $em->getRepository(Caracteristiqueproduit::class)
                                ->findOneBy(array('offre'=>$idproduit,'caracteristique'=>$idoffre));

        if($caracteristiqueplan == null)
        {
            $caracteristiqueplan = new Caracteristiqueproduit();
        }
        
        $caracteristiqueplan->setCaracteristique($caracteristique);
        $caracteristiqueplan->setOffre($produit);
        $caracteristiqueplan->setUser($this->getUser());
        
        if(isset($_POST['valeur']))
        {
            $caracteristiqueplan->setValeur($_POST['valeur']);
        }
        if(isset($_POST['isChecked']))
        {
            $caracteristiqueplan->setIsChecked($_POST['isChecked']);
        }
        if ($request->getMethod() == 'POST'){

            $liste_erreurs = $validator->validate($caracteristiqueplan);
            if(count($liste_erreurs) <= 0){
                $em->persist($caracteristiqueplan);
                $em->flush();
                $this->get('session')->getFlashBag()->add('information','Modification effectuée avec succès');
            }else{
            $this->get('session')->getFlashBag()->add('information','Une ereur a été rencontrée!');
            }
            return $this->redirect($this->generateUrl('users_adminuser_liste_produit'));
        }
        return $this->render('Theme/Users/Adminuser/Offre/updatecaracteristiqueproduit.html.twig',
        array('produit'=>$produit,'caracteristique'=>$caracteristique,'caracteristiqueplan'=>$caracteristiqueplan));
        
        }else{
            echo 'Echec ! Une erreur a été rencontrée.';
            exit;
        }
    }
}
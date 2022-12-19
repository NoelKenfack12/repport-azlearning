<?php

namespace App\Controller\Pricing\Offre;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\Servicetext\GeneralServicetext;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Pricing\Offre\Offre;
use App\Entity\Pricing\Offre\Caracteristique;
use App\Form\Pricing\Offre\OffreType;
use App\Form\Pricing\Offre\CaracteristiqueType;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Pricing\Offre\Abonnementuser;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use App\Service\Email\Singleemail;

class OffreController extends AbstractController
{
    private $params;
    private $_servicemail;
    private $_entityManager;

    public function __construct(ParameterBagInterface $params, Singleemail $servicemail, EntityManagerInterface $em)
    {
        $this->params = $params;
        $this->_servicemail = $servicemail;
        $this->_entityManager = $em;
    }

    public function listeproduitadmin(GeneralServicetext $service, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $produit = new Offre($service);
        $formpro = $this->createForm(OffreType::class, $produit);
        if($request->getMethod() == 'POST')
        {
            $produit->setUser($this->getUser());
            $formpro->handleRequest($request);
            if($formpro->isValid()){ 
                $em->persist($produit);
                $em->flush();
            }else{
                $this->get('session')->getFlashBag()->add('information','Données invalides.');
            }
        }

        $caracteristique = new Caracteristique();
        $formcarac = $this->createForm(CaracteristiqueType::class, $caracteristique);

        $liste_caract = $em->getRepository(Caracteristique::class)
                           ->findAll();

        $liste_produit = $em->getRepository(Offre::class)
                           ->findAll();
        
        foreach($liste_produit as $produitCurrent)
        {
            $produitCurrent->setEm($this->_entityManager);
        }
        $formsupp = $this->createFormBuilder()->getForm();
        return $this->render('Theme/Users/Adminuser/Offre/listeproduitadmin.html.twig', 
        array('formpro'=>$formpro->createView(),'formsupp'=>$formsupp->createView(),'formcarac'=>$formcarac->createView(),
        'liste_caract'=>$liste_caract, 'liste_produit'=>$liste_produit));
    }

    public function modificationproduit(GeneralServicetext $service, Request $request, EntityManagerInterface $em, $id)
    {
        if(isset($_GET['id']))
        {
            $id = $_GET['id'];
        }else{
            $id = $id;
        }
        $produit = $em->getRepository(Offre::class)
                         ->find($id);
        if($produit != null)
        {
            $formpro = $this->createForm(OffreType::class, $produit);
            if ($request->getMethod() == 'POST'){
                $formpro->handleRequest($request);
                if ($formpro->isValid()){
                    $produit->setDate(new \Datetime());
                    $em->flush();
                    $this->get('session')->getFlashBag()->add('information','Modification effectuée avec succès');
                }else{
                    $this->get('session')->getFlashBag()->add('information','Une ereur a été rencontrée!');
                }
                return $this->redirect($this->generateUrl('users_adminuser_liste_produit'));
            }
            return $this->render('Theme/Users/Adminuser/Offre/modificationproduit.html.twig',
            array('formpro'=>$formpro->createView(),'produit'=>$produit));
        }else{
            echo 'Echec ! Une erreur a été rencontrée.';
            exit;
        }
    }

    public function deleteproduit(Offre $produit)
    {
        $this->_entityManager->remove($produit);
        $this->_entityManager->flush();
        $this->get('session')->getFlashBag()->add('information','Suppression effectuée avec succès');
        return $this->redirect($this->generateUrl('users_adminuser_liste_produit'));
    }

    public function offrespremium()
    {
        $liste_produit = $this->_entityManager->getRepository(Offre::class)
                              ->findAll();
        
        foreach($liste_produit as $produitCurrent)
        {
            $produitCurrent->setEm($this->_entityManager);
        }

        $pack = '';
        if(isset($_GET['pack']))
        {
            $pack = $_GET['pack'];
        }

        return $this->render('Theme/Pricing/Offre/Offre/offrespremium.html.twig',
            array('liste_produit'=>$liste_produit, 'pack'=>$pack));
    }

    public function abonnementUser(EntityManagerInterface $em)
    {
        if($this->getUser() != null)
        {
            if(isset($_GET['pack']))
            {
                $pack = $_GET['pack'];
                if($pack == 'promo')
                {
                    $repository = $em->getRepository(Offre::class);
                    $offre = $repository->findOneBy(array('newprise'=>0));

                    if($offre != null)
                    {
                        $repository = $em->getRepository(Abonnementuser::class);
                        $olfAbonnementuser = $repository->findOneBy(array('user'=>$this->getUser(), 'active'=>1));

                        if($olfAbonnementuser == null)
                        {
                            $abonnementuser = new Abonnementuser();
                            $abonnementuser->setUser($this->getUser());
                            $abonnementuser->setOffre($offre);
                            $abonnementuser->setMontant(0);
                            $abonnementuser->setDureeJour(30);
                            $em->persist($abonnementuser);
                            $em->flush();
                        }
                    }

                    echo "promo";
                    exit;
                }else{
                    $repository = $em->getRepository(Offre::class);
                    $offre = $repository->findLastOffer();
 
                    if($offre != null)
                    {
                        return $this->render('Theme/Pricing/Offre/Offre/abonnementuser.html.twig', array('offre'=>$offre));
                    }else{
                        echo "echec";
                        exit;
                    }
                }
            }
            echo "echec";
            exit;
        }else{
            echo "echec";
            exit;
        }
    }

    public function validationsouscription(Offre $offre, GeneralServicetext $service)
    {
        if(isset($_POST['_password']))
        {
            $em = $this->getDoctrine()->getManager();
            if($this->getUser()->getSoldeprincipal() >= $offre->getNewprise())
            {
                if($_POST['_password'] == $service->decrypt($this->getUser()->getPassword(), $this->getUser()->getSalt()))
                {
                    $repository = $em->getRepository(Abonnementuser::class);
                    $oldAbonnementuser = $repository->findOneBy(array('user'=>$this->getUser(), 'active'=>1));

                    if($oldAbonnementuser == null or $oldAbonnementuser->getMontant() == 0)
                    {
                        $this->getUser()->setSoldeprincipal($this->getUser()->getSoldeprincipal() - $offre->getNewprise());

                        if($oldAbonnementuser == null)
                        {
                            $abonnementuser = new Abonnementuser();
                            $abonnementuser->setUser($this->getUser());
                            $abonnementuser->setOffre($offre);
                            $abonnementuser->setMontant($offre->getNewprise());
                            $abonnementuser->setDureeJour(30);
                            $em->persist($abonnementuser);
                        }else{
                            $oldAbonnementuser->setOffre($offre);
                            $oldAbonnementuser->setMontant($offre->getNewprise());
                            $oldAbonnementuser->setDureeJour(30);
                            $oldAbonnementuser->setCreatedAt(new \Datetime());
                        }
                        $em->flush();

                        //envoie d'email
                        $siteweb = $this->params->get('siteweb');
                        $sitename = $this->params->get('sitename');
                        $emailadmin = $this->params->get('emailadmin');
                        if($service->email($emailadmin))
                        {
                            $response = $this->_servicemail->sendNotifEmail(
                                'Team '.$sitename, //Nom du destinataire
                                $emailadmin, //Email Destinataire
                                $this->getUser()->name(30).' vient de s\'inscrire au pack '.$offre->getNom().' sur '.$sitename, //Objet de l'email
                                $this->getUser()->name(30).' vient de s\'inscrire au pack '.$offre->getNom().' sur '.$sitename, //Grand Titre de l'email
                                'Rendez-vous sur le site pour avoir les détails de cette offre.</br><a href="'.$siteweb.'/offres/premium'.'">Détail de l\'offre commandée par '.$this->getUser()->name(35).'.</a>',  //Contenu de l'email
                                ''  //Lien à suivre
                            );
                        }
                        
                        if($service->email($this->getUser()->getUsername()))
                        {
                            $response = $this->_servicemail->sendNotifEmail(
                                $this->getUser()->name(50), //Nom du destinataire
                                $this->getUser()->getUsername(), //Email Destinataire
                                'Votre inscription au pack '.$offre->getNom().' a été effectuée avec succès sur '.$sitename.' !', //Objet de l'email
                                'Votre inscription au pack '.$offre->getNom().' a été effectuée avec succès sur '.$sitename.' !', //Grand Titre de l'email
                                'Consultez notre catalogue de formation pour accéder aux formations désirées .</br><a href="'.$siteweb.'/formations">Consultez les formations</a>',  //Contenu de l'email
                                ''  //Lien à suivre
                            );
                        }

                        $this->get('session')->getFlashBag()->add('information','Souscription effectuée avec succès.');
                    }else{
                        $this->get('session')->getFlashBag()->add('information','Vous avez une souscription en cours.');
                    }
                    
                    return $this->redirect($this->generateUrl('produit_service_visiter_notre_blog'));
                }
            }
            $this->get('session')->getFlashBag()->add('information','Echec d\'enregistrement !! Votre compte ne peut supporter cette facture actuellement');
        }else{
            $this->get('session')->getFlashBag()->add('information','Echec d\'enregistrement !! Toutes les données n\'ont pas été reçu.');
        }
        
        return $this->redirect($this->generateUrl('pricing_offre_offres_premium'));
    }
}
<?php

namespace App\Entity\Produit\Produit;

use Doctrine\ORM\Mapping as ORM;
use App\Validator\Validatortext\Taillemin;
use App\Validator\Validatortext\Taillemax;
use App\Service\Servicetext\GeneralServicetext;
use Symfony\Component\Validator\Constraints as Assert;

use App\Validator\Validatortext\Email;
use App\Validator\Validatortext\Telephone;

use App\Repository\Produit\Produit\ProduitRepository;
use App\Entity\Users\User\User;
use App\Entity\Produit\Produit\Imgproduit;
use App\Entity\Produit\Produit\Videoproduit;
use App\Entity\Produit\Produit\Coutlivraison;
use App\Entity\Produit\Produit\Souscategorie;
use App\Entity\Produit\Service\Service;
use App\Entity\Produit\Service\Produitformation;
use Doctrine\Common\Collections\Collection;
use App\Entity\Produit\Produit\Chapitrecours;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Produit
 *
 * @ORM\Table("produit")
 * @ORM\Entity(repositoryClass=ProduitRepository::class)
 * @ApiResource(
 *    normalizationContext={"groups"={"produit:read"}},
 *    denormalizationContext={"groups"={"produit:write"}}
 * )
 **@ORM\HasLifecycleCallbacks
 */
class Produit
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"produit:read"})
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var integer
     *
     * @ORM\Column(name="nblike", type="integer")
     */
    private $nblike;
	
	/**
     * @var integer
     *
     * @ORM\Column(name="nbvue", type="integer")
     */
    private $nbvue; 
	
	/**
     * @var integer
     *
     * @ORM\Column(name="dureeminute", type="integer", nullable=true)
     */
    private $dureeminute; 
	
	/**
     * @var integer
     *
     * @ORM\Column(name="dureeseconde", type="integer", nullable=true)
     */
    private $dureeseconde; 
	
	/**
     * @var integer
     *
     * @ORM\Column(name="nbpartage", type="integer")
     */
    private $nbpartage;
	
	/**
     * @var integer
     *
     * @ORM\Column(name="nbenregistrer", type="integer")
     */
    private $nbenregistrer;
	
	/**
     * @var integer
     *
     * @ORM\Column(name="recommander", type="integer")
     */
    private $recommander;
	
	/**
     * @var integer
     *
     * @ORM\Column(name="nbcertificat", type="integer")
     */
    private $nbcertificat;
	
	/**
     * @var string
     *
     * @ORM\Column(name="titre", type="string")
     *@Taillemax(valeur=50000, message="Au plus 50000 caractès")
    */
    private $titre;
	
	
	/**
     * @var text
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     *@Taillemax(valeur=100000, message="Au plus 100000 caractès")
     */
    private $description;
	
	/**
     * @var text
     *
     * @ORM\Column(name="objectif", type="text", nullable=true)
     *@Taillemax(valeur=100000, message="Au plus 100000 caractès")
     */
    private $objectif;
	
	/**
     * @var text
     *
     * @ORM\Column(name="prerequis", type="text", nullable=true)
      *@Taillemax(valeur=100000, message="Au plus 100000 caractès")
     */
    private $prerequis;

    /**
     * @var integer
     *
     * @ORM\Column(name="nbvente", type="integer")
     */
    private $nbvente;

    /**
     * @var integer
     *
     * @ORM\Column(name="rang", type="integer")
     */
    private $rang;
	
	/**
     * @var integer
     *
     * @ORM\Column(name="timestamp", type="integer")
     */
    private $timestamp;

    /**
     * @var integer
     *
     * @ORM\Column(name="lastprise", type="integer")
     */
    private $lastprise;
	
	
	/**
     * @var boolean
     *
     * @ORM\Column(name="avant", type="boolean")
     */
    private $avant;
	
	/**
     * @var boolean
     *
     * @ORM\Column(name="valide", type="boolean")
     */
    private $valide;
	
	/**
     * @var boolean
     *
     * @ORM\Column(name="guide", type="boolean")
     */
    private $guide;

    /**
     * @var integer
     *
     * @ORM\Column(name="difference", type="integer")
     */
    private $difference;
	
	/**
     * @var integer
     *
     * @ORM\Column(name="newprise", type="integer")
     */
    private $newprise;
	
	/**
     * @var integer
     *
     * @ORM\Column(name="dureeacces", type="integer")
     */
    private $dureeacces;
	
	/**
       * @ORM\ManyToOne(targetEntity=User::class)
       * @ORM\JoinColumn(nullable=true)
     */
	private $user;
	
	/**
      * @ORM\ManyToMany(targetEntity=User::class)
	  * @ORM\JoinColumn(nullable=true)
    */
    private $userlikes;
	
	/**
     * @var integer
     *
     * @ORM\Column(name="prixlivraison", type="integer")
     */
    private $prixlivraison;
	
	/**
      * @ORM\OneToOne(targetEntity=Imgproduit::class, cascade={"persist", "remove"})
      * @Assert\Valid()
      */
   private $imgproduit;
   
   /**
      * @ORM\OneToOne(targetEntity=Videoproduit::class, cascade={"persist", "remove"})
      * @Assert\Valid()
      */
   private $videoproduit;
	
	/**
      * @ORM\OneToMany(targetEntity=Coutlivraison::class, mappedBy="produit")
      */
    private $coutlivraisons;
	
	 /**
       * @ORM\ManyToOne(targetEntity=Souscategorie::class, inversedBy="produits")
       * @ORM\JoinColumn(nullable=false)
       */
	private $souscategorie;
	
	/**
       * @ORM\ManyToOne(targetEntity=Service::class)
       * @ORM\JoinColumn(nullable=true)
       */
	private $equipedom;
	
	/**
       * @ORM\ManyToOne(targetEntity=Service::class)
       * @ORM\JoinColumn(nullable=true)
       */
	private $equipeext;
	
	/**
       * @ORM\ManyToOne(targetEntity=Service::class)
       * @ORM\JoinColumn(nullable=true)
       */
	private $competition;
	
	/**
       * @ORM\OneToMany(targetEntity=Produitformation::class, mappedBy="produit")
    */
    private $produitformations;
	

	private $servicetext;
	
	private $em;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $typecours;
	
	public function __construct(GeneralServicetext $service, EntityManagerInterface $em = null)
    {
        $this->servicetext = $service;
        $this->timestamp = time();
        $this->avant = false;
        $this->valide = false;
        $this->guide = false;
        $this->recommander = false;
        
        $this->nblike = 0;
        $this->nbvue = 0;
        $this->nbpartage = 0;
        $this->nbenregistrer = 0;
        $this->nbrecommander = 0;
            
        $this->nbvente = 0;
        $this->nbcertificat = 0;
        $this->newprise = 1000;
        $this->difference = 0;
        $this->prixlivraison = 0;
        $this->lastprise = 1000;
        $this->rang = 0; 
        $this->dureeacces = 360; 
        $this->date = new \Datetime();
        $this->coutlivraisons = new \Doctrine\Common\Collections\ArrayCollection();
        $this->userlikes = new \Doctrine\Common\Collections\ArrayCollection();
        $this->produitformations = new \Doctrine\Common\Collections\ArrayCollection();
        $this->em = $em;
    }

	public function setEm($em)
    {
        $this->em = $em;
    }
	
	public function getEm()
    {
        return $this->em;
    }

    public function postLoad(LifecycleEventArgs $args)
     {
         $entity = $args->getEntity();
         if(method_exists($entity, 'setEm')) {
             $entity->setEm($this->em);
         }
     }
	
	public function priseLivraison($ville)
    {
        $coutlivraison = $this->em->getRepository(Coutlivraison::class)
                            ->findOneBy(array('ville'=>$ville,'produit'=>$this));
        if($coutlivraison != null)
        {
            return $coutlivraison->getMontant();
        }else{
            return $this->prixlivraison;
        }
    }
	
	public function getServicetext()
    {
        return $this->servicetext;
    }
	
	public function setServicetext(GeneralServicetext $service)
    {
        $this->servicetext = $service;
        return $this;
    }
	
	public function ancienPrixProduit()
    {
        $aprix = $this->newprise + $this->difference;
        return $aprix;
    }
	
	/**
      * @ORM\PrePersist()
      * @ORM\PreUpdate()
     */
    public function preSave()
    {
		if($this->newprise != $this->lastprise)
		{
			$this->difference = ($this->lastprise - $this->newprise);
		}
		$this->lastprise = $this->newprise;
	}


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Produit
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set nblike
     *
     * @param integer $nblike
     * @return Produit
     */
    public function setNblike($nblike)
    {
        $this->nblike = $nblike;

        return $this;
    }

    /**
     * Get nblike
     *
     * @return integer 
     */
    public function getNblike()
    {
        return $this->nblike;
    }

    /**
     * Set nbvente
     *
     * @param integer $nbvente
     * @return Produit
     */
    public function setNbvente($nbvente)
    {
        $this->nbvente = $nbvente;

        return $this;
    }

    /**
     * Get nbvente
     *
     * @return integer 
     */
    public function getNbvente()
    {
        return $this->nbvente;
    }

    /**
     * Set newprise
     *
     * @param integer $newprise
     * @return Produit
     */
    public function setNewprise($newprise)
    {
        $this->newprise = $newprise;

        return $this;
    }

    /**
     * Get newprise
     *
     * @return integer 
     */
    public function getNewprise()
    {
        return $this->newprise;
    }

    /**
     * Set lastprise
     *
     * @param integer $lastprise
     * @return Produit
     */
    public function setLastprise($lastprise)
    {
        $this->lastprise = $lastprise;

        return $this;
    }

    /**
     * Get lastprise
     *
     * @return integer 
     */
    public function getLastprise()
    {
        return $this->lastprise;
    }

    /**
     * Set difference
     *
     * @param integer $difference
     * @return Produit
     */
    public function setDifference($difference)
    {
        $this->difference = $difference;

        return $this;
    }

    /**
     * Get difference
     *
     * @return integer 
     */
    public function getDifference()
    {
        return $this->difference;
    }

    public function setUser(User $user = null): self
    {
        $this->user = $user;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setSouscategorie(Souscategorie $souscategorie): self
    {
        $this->souscategorie = $souscategorie;
		$souscategorie->addProduit($this);
        return $this;
    }

    public function getSouscategorie(): ?Souscategorie
    {
        return $this->souscategorie;
    }

    /**
     * Set prixlivraison
     *
     * @param integer $prixlivraison
     * @return Produit
     */
    public function setPrixlivraison($prixlivraison)
    {
        $this->prixlivraison = $prixlivraison;

        return $this;
    }

    /**
     * Get prixlivraison
     *
     * @return integer 
     */
    public function getPrixlivraison()
    {
        return $this->prixlivraison;
    }

    public function addUserlike(User $userlikes): self
    {
        $this->userlikes[] = $userlikes;
		$this->nblike = $this->nblike + 1;

        return $this;
    }

    public function removeUserlike(User $userlikes)
    {
        $this->userlikes->removeElement($userlikes);
    }


    public function getUserlikes(): ?Collection
    {
        return $this->userlikes;
    }

    public function addCoutlivraison(Coutlivraison $coutlivraisons): self
    {
        $this->coutlivraisons[] = $coutlivraisons;

        return $this;
    }

    public function removeCoutlivraison(Coutlivraison $coutlivraisons)
    {
        $this->coutlivraisons->removeElement($coutlivraisons);
    }


    public function getCoutlivraisons(): ?Collection
    {
        return $this->coutlivraisons;
    }


    /**
     * Set timestamp
     *
     * @param integer $timestamp
     * @return Produit
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    /**
     * Get timestamp
     *
     * @return integer 
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }
	
	public function getImagealeatoire()
         	{
         		$max = count($this->getImgproduits());
         		if($max <= 1)
         		{
         			$tail1 = 1;
         		}else{
         			$tail1 = mt_rand(1,$max);
         		}
         		$compt = 1;
         		$imgchoise = null;
         		foreach($this->getImgproduits() as $image)
         		{
         			if($compt == $tail1)
         			{
         				$imgchoise = $image;
         			}
         			$compt++;
         		}
         		return $imgchoise;
         	}
	
	public function name($tail)
         	{
         		if(strlen($this->titre) <= $tail)
         		{
         		return $this->titre;
         		}else{
         		$text = wordwrap($this->titre,$tail,'~',true);
         		$tab = explode('~',$text);
         		$text = $tab[0];
         		return $text.'..';
         		}
         	}
	
	/**
      * @ORM\PrePersist()
     */
    public function prePersist()
    {
		$this->souscategorie->setNbproduit($this->souscategorie->getNbproduit() + 1);
		$this->souscategorie->setServicetext($this->getServicetext());
	}
	
	/**
     *@ORM\PreRemove()
     */
    public function preRemove()
    {
		$this->souscategorie->setNbproduit($this->souscategorie->getNbproduit() - 1);
		$this->souscategorie->setServicetext($this->getServicetext());
	}

    /**
     * Set avant
     *
     * @param boolean $avant
     * @return Produit
     */
    public function setAvant($avant)
    {
        $this->avant = $avant;

        return $this;
    }

    /**
     * Get avant
     *
     * @return boolean 
     */
    public function getAvant()
    {
        return $this->avant;
    }

    /**
     * Set rang
     *
     * @param integer $rang
     * @return Produit
     */
    public function setRang($rang)
    {
        $this->rang = $rang;

        return $this;
    }

    /**
     * Get rang
     *
     * @return integer 
     */
    public function getRang()
    {
        return $this->rang;
    }


    public function setEquipedom(Service $equipedom): self
    {
        $this->equipedom = $equipedom;

        return $this;
    }

    public function getEquipedom(): ?Service
    {
        return $this->equipedom;
    }

    public function setEquipeext(Service $equipeext): self
    {
        $this->equipeext = $equipeext;

        return $this;
    }

    public function getEquipeext(): ?Service
    {
        return $this->equipeext;
    }

    public function setCompetition(Service $competition): self
    {
        $this->competition = $competition;

        return $this;
    }

    public function getCompetition(): ?Service
    {
        return $this->competition;
    }

    public function setImgproduit(Imgproduit $imgproduit = null): self
    {
        $this->imgproduit = $imgproduit;

        return $this;
    }

    public function getImgproduit(): ?Imgproduit
    {
        return $this->imgproduit;
    }

    public function setVideoproduit(Videoproduit $videoproduit = null): self
    {
        $this->videoproduit = $videoproduit;

        return $this;
    }

    public function getVideoproduit(): ?Videoproduit
    {
        return $this->videoproduit;
    }

    /**
     * Set titre
     *
     * @param string $titre
     * @return Produit
     */
    public function setTitre($titre)
    {
        $this->titre = $titre;

        return $this;
    }

    /**
     * Get titre
     *
     * @return string 
     */
    public function getTitre()
    {
        return $this->titre;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Produit
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set objectif
     *
     * @param string $objectif
     * @return Produit
     */
    public function setObjectif($objectif)
    {
        $this->objectif = $objectif;

        return $this;
    }

    /**
     * Get objectif
     *
     * @return string 
     */
    public function getObjectif()
    {
        return $this->objectif;
    }

    /**
     * Set prerequis
     *
     * @param string $prerequis
     * @return Produit
     */
    public function setPrerequis($prerequis)
    {
        $this->prerequis = $prerequis;

        return $this;
    }

    /**
     * Get prerequis
     *
     * @return string 
     */
    public function getPrerequis()
    {
        return $this->prerequis;
    }

    /**
     * Set valide
     *
     * @param boolean $valide
     * @return Produit
     */
    public function setValide($valide)
    {
        $this->valide = $valide;

        return $this;
    }

    /**
     * Get valide
     *
     * @return boolean 
     */
    public function getValide()
    {
        return $this->valide;
    }

    /**
     * Set dureeacces
     *
     * @param integer $dureeacces
     * @return Produit
     */
    public function setDureeacces($dureeacces)
    {
        $this->dureeacces = $dureeacces;

        return $this;
    }

    /**
     * Get dureeacces
     *
     * @return integer 
     */
    public function getDureeacces()
    {
        return $this->dureeacces;
    }

    /**
     * Set nbcertificat
     *
     * @param integer $nbcertificat
     * @return Produit
     */
    public function setNbcertificat($nbcertificat)
    {
        $this->nbcertificat = $nbcertificat;

        return $this;
    }

    /**
     * Get nbcertificat
     *
     * @return integer 
     */
    public function getNbcertificat()
    {
        return $this->nbcertificat;
    }
	
	public function getNoteGeneralQCM($prodpan)
         	{
         		$liste_chapitre = $this->em->getRepository(Chapitrecours::class)
         							       ->chapitreselectcours($this->getId());
         		$total = 0;
         		foreach($liste_chapitre as $chap)
         		{
         			$chap->setEm($this->em);
         			$total = $total + $chap->getNoteQuestionnaire($prodpan);
         		}
         		
         		if(count($liste_chapitre) > 0)
         		{
         			return ceil($total/count($liste_chapitre));
         		}else{
         			return 0;
         		}
         	}
	
	public function getChapitreCours()
         	{
         		$liste_chapitre = $this->em->getRepository(Chapitrecours::class)
         							   ->chapitreselectcours($this->getId());
         		return $liste_chapitre;
         	}

    /**
     * Set guide
     *
     * @param boolean $guide
     * @return Produit
     */
    public function setGuide($guide)
    {
        $this->guide = $guide;

        return $this;
    }

    /**
     * Get guide
     *
     * @return boolean 
     */
    public function getGuide()
    {
        return $this->guide;
    }


    /**
     * Set nbvue
     *
     * @param integer $nbvue
     * @return Produit
     */
    public function setNbvue($nbvue)
    {
        $this->nbvue = $nbvue;

        return $this;
    }

    /**
     * Get nbvue
     *
     * @return integer 
     */
    public function getNbvue()
    {
        return $this->nbvue;
    }

    /**
     * Set nbpartage
     *
     * @param integer $nbpartage
     * @return Produit
     */
    public function setNbpartage($nbpartage)
    {
        $this->nbpartage = $nbpartage;

        return $this;
    }

    /**
     * Get nbpartage
     *
     * @return integer 
     */
    public function getNbpartage()
    {
        return $this->nbpartage;
    }

    /**
     * Set nbenregistrer
     *
     * @param integer $nbenregistrer
     * @return Produit
     */
    public function setNbenregistrer($nbenregistrer)
    {
        $this->nbenregistrer = $nbenregistrer;

        return $this;
    }

    /**
     * Get nbenregistrer
     *
     * @return integer 
     */
    public function getNbenregistrer()
    {
        return $this->nbenregistrer;
    }

    /**
     * Set recommander
     *
     * @param integer $recommander
     * @return Produit
     */
    public function setRecommander($recommander)
    {
        $this->recommander = $recommander;

        return $this;
    }

    /**
     * Get recommander
     *
     * @return integer 
     */
    public function getRecommander()
    {
        return $this->recommander;
    }

    /**
     * Set dureeminute
     *
     * @param integer $dureeminute
     * @return Produit
     */
    public function setDureeminute($dureeminute)
    {
        $this->dureeminute = $dureeminute;

        return $this;
    }

    /**
     * Get dureeminute
     *
     * @return integer 
     */
    public function getDureeminute()
    {
        return $this->dureeminute;
    }

    /**
     * Set dureeseconde
     *
     * @param integer $dureeseconde
     * @return Produit
     */
    public function setDureeseconde($dureeseconde)
    {
        $this->dureeseconde = $dureeseconde;

        return $this;
    }

    /**
     * Get dureeseconde
     *
     * @return integer 
     */
    public function getDureeseconde()
    {
        return $this->dureeseconde;
    }

    public function addProduitformation(Produitformation $produitformations): self
    {
        $this->produitformations[] = $produitformations;

        return $this;
    }

    public function removeProduitformation(Produitformation $produitformations)
    {
        $this->produitformations->removeElement($produitformations);
    }


    public function getProduitformations()
    {
        return $this->produitformations;
    }
	
	public function getDureeCours($position=1)
    {
        $liste_chapitre = $this->em->getRepository(Chapitrecours::class)
                                ->listechapitrecours($this->getId());
        $durreemin = 0;
        $durreeseconde = 0;
        $totalseconde = 0;
        foreach($liste_chapitre as $chapitre)
        {
            $durreemin = $durreemin + $chapitre->getDureeminute();
            $durreeseconde = $durreeseconde + $chapitre->getDureeseconde();
        }
        
        $totalseconde = $durreeseconde + ($durreemin * 60);
        $minute = (int)($totalseconde/60);
        $seconde = $totalseconde % 60;

        if($position == 1){
            return $minute.' min '.$seconde.' s ';
        }else{
            return $totalseconde;
        }
    }
	
	public function getLastFormationCours()
    {
        $formation = $this->em->getRepository(Produitformation::class)
                                ->findOneBy(array('produit'=>$this), array('date'=>'desc'),1);
        return $formation;
    }

    public function getAllPartiesCours($nbre = 6)
	{
		$formation = $this->em->getRepository(Partiecours::class)
							  ->findBy(array('produit'=>$this), array('date'=>'desc'), $nbre);
		return $formation;
	}

    public function getTypecours(): ?string
    {
        return $this->typecours;
    }

    public function setTypecours(?string $typecours): self
    {
        $this->typecours = $typecours;

        return $this;
    }
}

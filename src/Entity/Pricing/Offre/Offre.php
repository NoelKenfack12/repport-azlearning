<?php

namespace App\Entity\Pricing\Offre;

use Doctrine\ORM\Mapping as ORM;
use App\Validator\Validatortext\Taillemin;
use App\Validator\Validatortext\Taillemax;
use App\Service\Servicetext\GeneralServicetext;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Validatortext\Siteweb;
use App\Repository\Pricing\Offre\OffreRepository;
use App\Entity\Users\User\User;
use App\Entity\Pricing\Offre\Caracteristiqueproduit;
use App\Entity\Pricing\Offre\Imgoffre;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Offre
 *
 * @ORM\Table("offre")
 * @ORM\Entity(repositoryClass=OffreRepository::class)
 ** @ORM\HasLifecycleCallbacks
*/
class Offre
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255)
     *@Taillemin(valeur=2, message="Au moins 2 caractères")
     *@Taillemax(valeur=100, message="Au plus 100 caractès")
     */
    private $nom;
	
	/**
     * @var string
     *
     * @ORM\Column(name="contenu", type="text",nullable=true)
     *@Taillemax(valeur=1200, message="Au plus 1200 caractès")
     */
    private $contenu;
	
	/**
     * @var string
     *
     * @ORM\Column(name="rapport", type="string",nullable=true)
     *@Taillemax(valeur=50, message="Au plus 50 caractès")
     */
    private $rapport;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;
	
	/**
     * @var integer
     *
     * @ORM\Column(name="rang", type="integer")
     */
    private $rang;

    /**
     * @var integer
     *
     * @ORM\Column(name="nblike", type="integer")
     */
    private $nblike;

    /**
     * @var integer
     *
     * @ORM\Column(name="nbvente", type="integer")
     */
    private $nbvente;
	
	/**
     * @var integer
     *
     * @ORM\Column(name="choixauteur", type="boolean")
     */
    private $choixauteur;

    /**
     * @var integer
     *
     * @ORM\Column(name="newprise", type="integer")
     */
    private $newprise;

    /**
     * @var integer
     *
     * @ORM\Column(name="lastprise", type="integer")
     */
    private $lastprise;

    /**
     * @var integer
     *
     * @ORM\Column(name="difference", type="integer")
     */
    private $difference;
	
	/**
       * @ORM\ManyToOne(targetEntity=User::class)
       * @ORM\JoinColumn(nullable=false)
    */
	private $user;
	
	/**
     * @ORM\OneToMany(targetEntity=Caracteristiqueproduit::class, mappedBy="offre")
     */
    private $caracteristiqueproduits;
	
	/**
      * @ORM\OneToOne(targetEntity=Imgoffre::class, cascade={"persist", "remove"})
      * @Assert\Valid()
     */
	private $imgoffre;
	
	private $servicetext;
	
	private $element;
	
	private $em;
	
	public function __construct(GeneralServicetext $service)
	{
		$this->servicetext = $service;
		$this->nblike = 0;
		$this->nbvente = 0;
		$this->rang = 0;
		$this->lastprise = 0;
		$this->choixauteur = false;
		$this->difference = 0;
		$this->rapport = 'an';
		$this->date = new \Datetime();
		$this->caracteristiqueproduits = new ArrayCollection();
	}
	
	public function setEm($em)
	{
	$this->em = $em;
	}
	
	public function getEm()
	{
	return $this->em;
	}
	
	public function setElement($el)
	{
	$this->element = $el;
	}
	
	public function getElement()
	{
	return $this->element;
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
		if($this->newprise != $this->lastprise and $this->lastprise != 0)
		{
			$this->difference = ($this->lastprise - $this->newprise);
		}else{
			$this->difference = 0;
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
     * Set nom
     *
     * @param string $nom
     * @return Produit
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string 
     */
    public function getNom()
    {
        return $this->nom;
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

    /**
     * Set user
     * @return Produit
     */
    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * Set contenu
     *
     * @param string $contenu
     * @return Produit
     */
    public function setContenu($contenu)
    {
        $this->contenu = $contenu;

        return $this;
    }

    /**
     * Get contenu
     *
     * @return string 
     */
    public function getContenu()
    {
        return $this->contenu;
    }

    /**
     * Set choixauteur
     *
     * @param boolean $choixauteur
     * @return Produit
     */
    public function setChoixauteur($choixauteur)
    {
        $this->choixauteur = $choixauteur;

        return $this;
    }

    /**
     * Get choixauteur
     *
     * @return boolean 
     */
    public function getChoixauteur()
    {
        return $this->choixauteur;
    }

    /**
     * Add caracteristiqueproduits
     * @return Produit
     */
    public function addCaracteristiqueproduit(Caracteristiqueproduit $caracteristiqueproduits)
    {
        $this->caracteristiqueproduits[] = $caracteristiqueproduits;

        return $this;
    }

    /**
     * Remove caracteristiqueproduits
     */
    public function removeCaracteristiqueproduit(Caracteristiqueproduit $caracteristiqueproduits)
    {
        $this->caracteristiqueproduits->removeElement($caracteristiqueproduits);
    }

    /**
     * Get caracteristiqueproduits
     */
    public function getCaracteristiqueproduits(): ?Collection
    {
        return $this->caracteristiqueproduits;
    }
    
    /**
     * Set imgoffre
     * @return Offre
     */
    public function setImgoffre(Imgoffre $imgoffre = null): self
    {
        $this->imgoffre = $imgoffre;

        return $this;
    }

    /**
     * Get imgoffre
     */
    public function getImgoffre(): ?Imgoffre
    {
        return $this->imgoffre;
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

    /**
     * Set prixrenew
     *
     * @param integer $prixrenew
     * @return Produit
     */
    public function setPrixrenew($prixrenew)
    {
        $this->prixrenew = $prixrenew;

        return $this;
    }

    /**
     * Get prixrenew
     *
     * @return integer 
     */
    public function getPrixrenew()
    {
        return $this->prixrenew;
    }

    /**
     * Set rapport
     *
     * @param string $rapport
     * @return Produit
     */
    public function setRapport($rapport)
    {
        $this->rapport = $rapport;

        return $this;
    }

    /**
     * Get rapport
     *
     * @return string 
     */
    public function getRapport()
    {
        return $this->rapport;
    }
	
	public function subcontenu($tail)
	{
		$allname = $this->contenu;
		if(strlen($allname) <= $tail)
		{
			return $allname;
		}else{
			$text = wordwrap($allname,$tail,'~',true);
			$tab = explode('~',$text);
			$text = $tab[0];
			return $text.'...';
		}
	}

    public function getCaracteristiqueProduit(Caracteristique $caracteristique)
    {
        $caracteristiqueproduit = $this->em->getRepository(Caracteristiqueproduit::class)
                                ->findOneBy(array('offre'=>$this,'caracteristique'=>$caracteristique));
        return $caracteristiqueproduit;
    }

    public function getListeCaracteristiqueProduit()
    {
        $liste_caracteristiqueproduit = $this->em->getRepository(Caracteristiqueproduit::class)
                                             ->findBy(array('offre'=>$this));
        return $liste_caracteristiqueproduit;
    }
}

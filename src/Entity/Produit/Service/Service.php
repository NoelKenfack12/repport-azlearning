<?php

namespace App\Entity\Produit\Service;

use Doctrine\ORM\Mapping as ORM;
use App\Validator\Validatortext\Taillemin;
use App\Validator\Validatortext\Taillemax;
use App\Service\Servicetext\GeneralServicetext;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\Produit\Service\ServiceRepository;
use App\Entity\Users\User\User;
use App\Entity\Produit\Produit\Souscategorie;
use App\Entity\Produit\Service\Imgservice;
use App\Entity\Produit\Service\Imgservicesecond;
use App\Entity\Produit\Service\Evenement;
use App\Entity\Produit\Service\Commentaireblog;
use App\Entity\Produit\Produit\Produit;
use App\Entity\Produit\Service\Produitformation;
use Doctrine\Common\Collections\Collection;
use App\Entity\Produit\Produit\Chapitrecours;

use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Service
 *
 * @ORM\Table("service")
 * @ORM\Entity(repositoryClass=ServiceRepository::class)
 * @ApiResource(
 *    normalizationContext={"groups"={"service:read"}},
 *    denormalizationContext={"groups"={"service:write"}}
 * )
 ** @ORM\HasLifecycleCallbacks
 */
class Service
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"service:read"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255)
     * @Taillemax(valeur=60000, message="Au plus 60000 caractès")
     * @Groups({"service:read", "service:write"})
     */
    private $nom;
	
	/**
     * @var string
     *
     * @ORM\Column(name="detail", type="text", nullable=true)
     *@Taillemax(valeur=60000, message="Au plus 60000 caractès")
     */
    private $detail;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     *@Taillemax(valeur=60000, message="Au plus 60000 caractès")
     */
    private $description;
	
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
     * @var string
     *
     * @ORM\Column(name="keyword", type="text", nullable=true)
     *@Taillemax(valeur=400, message="Au plus 400 caractès")
     */
    private $keyword;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="rang", type="integer")
     */
    private $rang;
	
	/**
     * @var integer
     *
     * @ORM\Column(name="nbcertificat", type="integer")
     */
    private $nbcertificat;
	
	/**
     * @var string
     *
     * @ORM\Column(name="dureeacces", type="integer")
     */
    private $dureeacces;
	
	/**
     * @var boolean
     *
     * @ORM\Column(name="principal", type="boolean")
     */
    private $principal;
	
	/**
     * @var boolean
     *
     * @ORM\Column(name="themeforum", type="boolean")
     */
    private $themeforum;
	
	/**
     * @var boolean
     *
     * @ORM\Column(name="recrutement", type="boolean")
     */
    private $recrutement;
	
	/**
     * @var string
     *
     * @ORM\Column(name="datebegin", type="string", length=255,nullable=true)
     */
    private $datebegin;
	
	/**
     * @var string
     *
     * @ORM\Column(name="datefin", type="string", length=255,nullable=true)
     */
    private $datefin;
	
	/**
     * @var string
     *
     * @ORM\Column(name="pubinline", type="text", nullable=true)
     *@Taillemax(valeur=60000, message="Au plus 60000 caractès")
     */
    private $pubinline;
	
	/**
     * @var string
     *
     * @ORM\Column(name="puboffline", type="text", nullable=true)
     *@Taillemax(valeur=60000, message="Au plus 60000 caractès")
     */
    private $puboffline;
	
	/**
     * @var integer
     *
     * @ORM\Column(name="aprix", type="integer")
     */
    private $aprix;

    /**
     * @var integer
     *
     * @ORM\Column(name="nprix", type="integer")
     */
    private $nprix; //prix formation inLine

    /**
     * @var integer
     *
     * @ORM\Column(name="difference", type="integer")
     */
    private $difference;
	
	/**
     * @var integer
     *
     * @ORM\Column(name="aprixoff", type="integer")
     */
    private $aprixoff;  

    /**
     * @var integer
     *
     * @ORM\Column(name="nprixoff", type="integer")
     */
    private $nprixoff; //prix formation offLine

    /**
     * @var integer
     *
     * @ORM\Column(name="differenceoff", type="integer")
     */
    private $differenceoff;

	/**
       * @ORM\ManyToOne(targetEntity=User::class)
       * @ORM\JoinColumn(nullable=false)
    */
	private $user;
	
	/**
       * @ORM\ManyToOne(targetEntity=Souscategorie::class, inversedBy="services")
       * @ORM\JoinColumn(nullable=true)
        */
	private $souscategorie;
	
	/**
        * @ORM\OneToOne(targetEntity=Imgservice::class,  cascade={"persist","remove"})
        * @ORM\JoinColumn(nullable=true)
	    *@Assert\Valid()
    */
	private $imgservice;
	
	/**
      * @ORM\OneToOne(targetEntity=Imgservicesecond::class,  cascade={"persist","remove"})
      * @ORM\JoinColumn(nullable=true)
	  *@Assert\Valid()
    */
	private $imgservicesecond;
	
	/**
         * @ORM\OneToMany(targetEntity=Evenement::class, mappedBy="service")
         */
    private $evenements;
	
	/**
       * @ORM\OneToMany(targetEntity=Commentaireblog::class, mappedBy="service")
    */
    private $commentaireblogs;
	
	/**
     * @ORM\ManyToMany(targetEntity=Produit::class)
	 * @ORM\JoinColumn(nullable=true)
    */
    private $produits;
	
	/**
       * @ORM\OneToMany(targetEntity=Produitformation::class, mappedBy="service")
    */
    private $produitformations;
	
	// variable du service de normalisation des noms des pays.
	private $servicetext;
	
	private $datepicket;
	
	private $element;
	private $em;
	
	public function __construct(GeneralServicetext $service)
	{
		$this->servicetext = $service;
		$this->date = new \Datetime();
		$this->principal = false;
		$this->themeforum = false;
		$this->rang = 1;
		$this->aprix = 0;
		$this->nbcertificat = 0;
		$this->nprix = 0;
		$this->dureeacces = 0;
		$this->difference = 0;
		
		$this->aprixoff = 0;
		$this->nprixoff = 0;
		$this->differenceoff = 0;
		
		$this->recrutement = false;
		$this->evenements = new \Doctrine\Common\Collections\ArrayCollection();
		$this->commentaireblogs = new \Doctrine\Common\Collections\ArrayCollection();
		$this->produitformations = new \Doctrine\Common\Collections\ArrayCollection();
		$this->produits = new \Doctrine\Common\Collections\ArrayCollection();
	}

	public function setServicetext( GeneralServicetext $service)
    {
    $this->servicetext = $service;
    }
	
    public function getServicetext()
    {
    return $this->servicetext;
    }
	
	public function setElement($element)
    {
    $this->element = $element;
    }
	
    public function getElement()
    {
    return $this->element;
    }
	
	public function setEm($em)
    {
    $this->em = $em;
    }
	
    public function getEm()
    {
    return $this->em;
    }
	
	public function getDatepicket()
	{
		return $this->datepicket;
	}
	
	public function setDatepicket($datepicket)
	{
		$this->datepicket = $datepicket;
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
     * @return Service
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
     * Set description
     *
     * @param string $description
     * @return Service
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
     * Set date
     *
     * @param \DateTime $date
     * @return Service
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

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setImgservice(Imgservice $imgservice): self
    {
        $this->imgservice = $imgservice;

        return $this;
    }

    public function getImgservice(): ?Imgservice
    {
        return $this->imgservice;
    }

    public function addEvenement(Evenement $evenements): self
    {
        $this->evenements[] = $evenements;

        return $this;
    }

    public function removeEvenement(Evenement $evenements)
    {
        $this->evenements->removeElement($evenements);
    }

    public function getEvenements(): ?Collection
    {
        return $this->evenements;
    }

    /**
     * Set principal
     *
     * @param boolean $principal
     * @return Service
     */
    public function setPrincipal($principal)
    {
        $this->principal = $principal;

        return $this;
    }

    /**
     * Get principal
     *
     * @return boolean 
     */
    public function getPrincipal()
    {
        return $this->principal;
    }

    /**
     * Set recrutement
     *
     * @param boolean $recrutement
     * @return Service
     */
    public function setRecrutement($recrutement)
    {
        $this->recrutement = $recrutement;

        return $this;
    }

    /**
     * Get recrutement
     *
     * @return boolean 
     */
    public function getRecrutement()
    {
        return $this->recrutement;
    }

    public function setImgservicesecond(Imgservicesecond $imgservicesecond = null): self
    {
        $this->imgservicesecond = $imgservicesecond;

        return $this;
    }

    public function getImgservicesecond(): ?Imgservicesecond
    {
        return $this->imgservicesecond;
    }

    public function addCommentaireblog(Commentaireblog $commentaireblogs): self
    {
        $this->commentaireblogs[] = $commentaireblogs;

        return $this;
    }

    public function removeCommentaireblog(Commentaireblog $commentaireblogs)
    {
        $this->commentaireblogs->removeElement($commentaireblogs);
    }

    public function getCommentaireblogs(): ?Collection
    {
        return $this->commentaireblogs;
    }

    /**
     * Set keyword
     *
     * @param string $keyword
     * @return Service
     */
    public function setKeyword($keyword)
    {
        $this->keyword = $keyword;

        return $this;
    }

    /**
     * Get keyword
     *
     * @return string 
     */
    public function getKeyword()
    {
        return $this->keyword;
    }

    /**
     * Set rang
     *
     * @param integer $rang
     * @return Service
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
     * Set themeforum
     *
     * @param boolean $themeforum
     * @return Service
     */
    public function setThemeforum($themeforum)
    {
        $this->themeforum = $themeforum;

        return $this;
    }

    /**
     * Get themeforum
     *
     * @return boolean 
     */
    public function getThemeforum()
    {
        return $this->themeforum;
    }

    /**
     * Set datebegin
     *
     * @param string $datebegin
     * @return Service
     */
    public function setDatebegin($datebegin)
    {
        $this->datebegin = $datebegin;

        return $this;
    }

    /**
     * Get datebegin
     *
     * @return string 
     */
    public function getDatebegin()
    {
        return $this->datebegin;
    }

    /**
     * Set datefin
     *
     * @param string $datefin
     * @return Service
     */
    public function setDatefin($datefin)
    {
        $this->datefin = $datefin;

        return $this;
    }

    /**
     * Get datefin
     *
     * @return string 
     */
    public function getDatefin()
    {
        return $this->datefin;
    }

    /**
     * Set pubinline
     *
     * @param string $pubinline
     * @return Service
     */
    public function setPubinline($pubinline)
    {
        $this->pubinline = $pubinline;

        return $this;
    }

    /**
     * Get pubinline
     *
     * @return string 
     */
    public function getPubinline()
    {
        return $this->pubinline;
    }

    /**
     * Set puboffline
     *
     * @param string $puboffline
     * @return Service
     */
    public function setPuboffline($puboffline)
    {
        $this->puboffline = $puboffline;

        return $this;
    }

    /**
     * Get puboffline
     *
     * @return string 
     */
    public function getPuboffline()
    {
        return $this->puboffline;
    }

    public function addProduit(Produit $produits): self
    {
        $this->produits[] = $produits;

        return $this;
    }


    public function removeProduit(Produit $produits)
    {
        $this->produits->removeElement($produits);
    }

    public function getProduits(): ?Collection
    {
        return $this->produits;
    }
	
	/**
      * @ORM\PrePersist()
      * @ORM\PreUpdate()
    */
    public function preUpload()
    {
		$servicetext = new GeneralServicetext();
		if($this->aprix != $this->nprix)
		{
			$this->difference = ($this->aprix - $this->nprix);
		}
		$this->aprix = $this->nprix;
		
		if($this->aprixoff != $this->nprixoff)
		{
			$this->differenceoff = ($this->aprixoff - $this->nprixoff);
		}
		$this->aprixoff = $this->nprixoff;
		
		$date = $servicetext->normaliseDate($this->datepicket);
		$this->date = new \Datetime($date);
	}

	public function ancienPrix()
	{
		$aprix = $this->aprix + $this->difference;
		return $aprix;
	}
	
	public function ancienPrixOff()
	{
		$aprixoff = $this->aprixoff + $this->differenceoff;
		return $aprixoff;
	}

    /**
     * Set aprix
     *
     * @param integer $aprix
     * @return Service
     */
    public function setAprix($aprix)
    {
        $this->aprix = $aprix;

        return $this;
    }

    /**
     * Get aprix
     *
     * @return integer 
     */
    public function getAprix()
    {
        return $this->aprix;
    }

    /**
     * Set nprix
     *
     * @param integer $nprix
     * @return Service
     */
    public function setNprix($nprix)
    {
        $this->nprix = $nprix;

        return $this;
    }

    /**
     * Get nprix
     *
     * @return integer 
     */
    public function getNprix()
    {
        return $this->nprix;
    }

    /**
     * Set difference
     *
     * @param integer $difference
     * @return Service
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
     * Set aprixoff
     *
     * @param integer $aprixoff
     * @return Service
     */
    public function setAprixoff($aprixoff)
    {
        $this->aprixoff = $aprixoff;

        return $this;
    }

    /**
     * Get aprixoff
     *
     * @return integer 
     */
    public function getAprixoff()
    {
        return $this->aprixoff;
    }

    /**
     * Set nprixoff
     *
     * @param integer $nprixoff
     * @return Service
     */
    public function setNprixoff($nprixoff)
    {
        $this->nprixoff = $nprixoff;

        return $this;
    }

    /**
     * Get nprixoff
     *
     * @return integer 
     */
    public function getNprixoff()
    {
        return $this->nprixoff;
    }

    /**
     * Set differenceoff
     *
     * @param integer $differenceoff
     * @return Service
     */
    public function setDifferenceoff($differenceoff)
    {
        $this->differenceoff = $differenceoff;

        return $this;
    }

    /**
     * Get differenceoff
     *
     * @return integer 
     */
    public function getDifferenceoff()
    {
        return $this->differenceoff;
    }

    /**
     * Set detail
     *
     * @param string $detail
     * @return Service
     */
    public function setDetail($detail)
    {
        $this->detail = $detail;

        return $this;
    }

    /**
     * Get detail
     *
     * @return string 
     */
    public function getDetail()
    {
        return $this->detail;
    }

    /**
     * Set dureeacces
     *
     * @param integer $dureeacces
     * @return Service
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

    public function setSouscategorie(Souscategorie $souscategorie = null): self
    {
        $this->souscategorie = $souscategorie;
		if($souscategorie != null)
		{
			$souscategorie->addService($this);
		}

        return $this;
    }

    public function getSouscategorie(): ?Souscategorie
    {
        return $this->souscategorie;
    }
	
	public function name($tail)
	{
		if(strlen($this->nom) <= $tail)
		{
		return $this->nom;
		}else{
		$text = wordwrap($this->nom,$tail,'~',true);
		$tab = explode('~',$text);
		$text = $tab[0];
		return $text.'..';
		}
	}

    /**
     * Set nbcertificat
     *
     * @param integer $nbcertificat
     * @return Service
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

    /**
     * Set dureeminute
     *
     * @param integer $dureeminute
     * @return Service
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
     * @return Service
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

    public function getProduitformations(): ?Collection
    {
        return $this->produitformations;
    }
	
	public function getNbvideos()
	{
		$nbvideo = 0;
		foreach($this->getProduits() as $cours)
		{
			$liste_video = $this->em->getRepository(Chapitrecours::class)
							  ->listechapitrecours($cours->getId());
			$nbvideo = $nbvideo + count($liste_video) + 1;
		}
		return $nbvideo;
	}
}

<?php
namespace App\Entity\Produit\Service;

use Doctrine\ORM\Mapping as ORM;
use App\Validator\Validatortext\Taillemin;
use App\Validator\Validatortext\Taillemax;
use App\Service\Servicetext\GeneralServicetext;
use App\Validator\Validatortext\Siteweb;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use App\Repository\Produit\Service\InfoentrepriseRepository;
use App\Entity\Users\User\User;
use App\Entity\Produit\Service\Imginfoentreprise;
use App\Entity\Produit\Service\Detailinfoentreprise;
use Doctrine\Common\Collections\Collection;

/**
 * Infoentreprise
 *
 * @ORM\Table("infoentreprise")
 * @ORM\Entity(repositoryClass=InfoentrepriseRepository::class)
 */
class Infoentreprise
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
     * @ORM\Column(name="titre", type="string", length=255,nullable=true)
     *@Taillemax(valeur=300, message="Au plus 300 caractès")
     */
    private $titre;
	
	/**
     * @var string
     *
     * @ORM\Column(name="linkvideo", type="string", length=255,nullable=true)
     *@Taillemax(valeur=1000, message="Au plus 300 caractès")
     */
    private $linkvideo;
	
	/**
     * @var string
     *
     * @ORM\Column(name="link", type="string", length=255,nullable=true)
     *@Siteweb(message="Lien invalide")
     */
    private $link;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
      *@Taillemax(valeur=500, message="Au plus 500 caractès")
     */
    private $description;
	
	/**
     * @var string
     *
     * @ORM\Column(name="detail", type="text", nullable=true)
    */
    private $detail;
	
	/**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255,nullable=true)
     */
    private $type;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var integer
     *
     * @ORM\Column(name="timestamp", type="bigint")
     */
    private $timestamp;
	
	/**
     * @var boolean
     *
     * @ORM\Column(name="principal", type="boolean")
     */
    private $principal;
	
	/**
     * @var boolean
     *
     * @ORM\Column(name="valeur", type="boolean")
     */
    private $valeur;
	
	/**
     * @var integer
     *
     * @ORM\Column(name="rang", type="integer")
     */
    private $rang;
	
	/**
       * @ORM\ManyToOne(targetEntity=User::class)
       * @ORM\JoinColumn(nullable=false)
    */
	private $user;
	
	/**
     * @ORM\OneToOne(targetEntity=Imginfoentreprise::class,  cascade={"persist","remove"})
     * @ORM\JoinColumn(nullable=true)
	 *@Assert\Valid()
    */
	private $imginfoentreprise;
	
	/**
     * @ORM\OneToMany(targetEntity=Detailinfoentreprise::class, mappedBy="infoentreprise")
     */
    private $detailinfoentreprises;
	
	// variable du service de normalisation des noms des pays.
	private $servicetext;
	
	public function __construct(GeneralServicetext $service)
	{
        $this->servicetext = $service;
        $this->date = new \Datetime();
        $this->timestamp = time();
        $this->rang = 1;
        $this->valeur = false;
        $this->principal = false;
        $this->detailinfoentreprises = new \Doctrine\Common\Collections\ArrayCollection();
	}
	
	public function setServicetext(GeneralServicetext $service)
    {
        $this->servicetext = $service;
    }

    public function getServicetext()
    {
        return $this->servicetext;
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
     * Set titre
     *
     * @param string $titre
     * @return Infoentreprise
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
     * @return Infoentreprise
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
     * @return Infoentreprise
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
     * Set timestamp
     *
     * @param integer $timestamp
     * @return Infoentreprise
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

    /**
     * Set rang
     *
     * @param integer $rang
     * @return Infoentreprise
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

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setImginfoentreprise(Imginfoentreprise $imginfoentreprise = null): self
    {
        $this->imginfoentreprise = $imginfoentreprise;

        return $this;
    }

    public function getImginfoentreprise(): ?Imginfoentreprise
    {
        return $this->imginfoentreprise;
    }

    public function addDetailinfoentreprise(Detailinfoentreprise $detailinfoentreprises)
    {
        $this->detailinfoentreprises[] = $detailinfoentreprises;

        return $this;
    }

    public function removeDetailinfoentreprise(Detailinfoentreprise $detailinfoentreprises)
    {
        $this->detailinfoentreprises->removeElement($detailinfoentreprises);
    }

    public function getDetailinfoentreprises(): ?Collection
    {
        return $this->detailinfoentreprises;
    }

    /**
     * Set principal
     *
     * @param boolean $principal
     * @return Infoentreprise
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
     * Set link
     *
     * @param string $link
     * @return Infoentreprise
     */
    public function setLink($link)
    {
        $this->link = $link;

        return $this;
    }

    /**
     * Get link
     *
     * @return string 
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Set valeur
     *
     * @param boolean $valeur
     * @return Infoentreprise
     */
    public function setValeur($valeur)
    {
        $this->valeur = $valeur;

        return $this;
    }

    /**
     * Get valeur
     *
     * @return boolean 
     */
    public function getValeur()
    {
        return $this->valeur;
    }

    /**
     * Set linkvideo
     *
     * @param string $linkvideo
     * @return Infoentreprise
     */
    public function setLinkvideo($linkvideo)
    {
        $this->linkvideo = $linkvideo;

        return $this;
    }

    /**
     * Get linkvideo
     *
     * @return string 
     */
    public function getLinkvideo()
    {
        return $this->linkvideo;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Infoentreprise
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set detail
     *
     * @param string $detail
     * @return Infoentreprise
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
}

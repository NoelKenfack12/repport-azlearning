<?php

namespace App\Entity\Produit\Produit;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\Produit\Produit\AnimationproduitRepository;
use App\Entity\Users\User\User;
use App\Entity\Produit\Produit\Produit;
use App\Entity\Produit\Produit\Chapitrecours;
use App\Entity\Produit\Produit\Produitpanier;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Animationproduit
 *
 * @ORM\Table("animationproduit")
 * @ORM\Entity(repositoryClass=AnimationproduitRepository::class)
 *  @ApiResource(
 *    normalizationContext={"groups"={"animationproduit:read"}},
 *    denormalizationContext={"groups"={"animationproduit:write"}}
 * )
 **@ORM\HasLifecycleCallbacks
 */
class Animationproduit
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"animationproduit:read"})
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var boolean
     *
     * @ORM\Column(name="aimer", type="boolean")
     */
    private $aimer;

    /**
     * @var boolean
     *
     * @ORM\Column(name="enregistrer", type="boolean")
     */
    private $enregistrer;

    /**
     * @var boolean
     *
     * @ORM\Column(name="partager", type="boolean")
     */
    private $partager;
	
	 /**
     * @var boolean
     *
     * @ORM\Column(name="voir", type="boolean")
    */
    private $voir;
	
	/**
     * @var boolean
     *
     * @ORM\Column(name="recommander", type="boolean")
     */
    private $recommander;
	
	/**
       * @ORM\ManyToOne(targetEntity=User::class)
       * @ORM\JoinColumn(nullable=false)
    */
	private $user;
	
	/**
       * @ORM\ManyToOne(targetEntity=Produit::class)
       * @ORM\JoinColumn(nullable=false)
    */
	private $produit;
	
	/**
       * @ORM\ManyToOne(targetEntity=Chapitrecours::class)
       * @ORM\JoinColumn(nullable=true)
    */
	private $chapitrecours;
	
	/**
       * @ORM\ManyToOne(targetEntity=Produitpanier::class)
       * @ORM\JoinColumn(nullable=true)
    */
	private $produitpanier;
	
	public function __construct()
	{
		$this->date = new \Datetime();
		$this->aimer = false;
		$this->enregistrer = false;
		$this->partager = false;
		$this->voir = false;
		$this->recommander = false;
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
     * @return Animationproduit
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
     * Set aimer
     *
     * @param boolean $aimer
     * @return Animationproduit
     */
    public function setAimer($aimer)
    {
        $this->aimer = $aimer;

        return $this;
    }

    /**
     * Get aimer
     *
     * @return boolean 
     */
    public function getAimer()
    {
        return $this->aimer;
    }

    /**
     * Set enregistrer
     *
     * @param boolean $enregistrer
     * @return Animationproduit
     */
    public function setEnregistrer($enregistrer)
    {
        $this->enregistrer = $enregistrer;

        return $this;
    }

    /**
     * Get enregistrer
     *
     * @return boolean 
     */
    public function getEnregistrer()
    {
        return $this->enregistrer;
    }

    /**
     * Set partager
     *
     * @param boolean $partager
     * @return Animationproduit
     */
    public function setPartager($partager)
    {
        $this->partager = $partager;

        return $this;
    }

    /**
     * Get partager
     *
     * @return boolean 
     */
    public function getPartager()
    {
        return $this->partager;
    }
	
	/**
      * @ORM\PrePersist()
     */
    public function prePersist()
    {
		if($this->aimer == true)
		{
			$this->produit->setNblike($this->produit->getNblike() + 1);
		}else if($this->enregistrer == true)
		{
			$this->produit->setNbenregistrer($this->produit->getNbenregistrer() + 1);
		}else if($this->partager == true){
			$this->produit->setNbpartage($this->produit->getNbpartage() + 1);
		}else if($this->voir == true){
			$this->produit->setNbvue($this->produit->getNbvue() + 1);
		}
	}
	
	/**
     *@ORM\PreRemove()
     */
    public function preRemove()
    {
		if($this->aimer == true)
		{
			$this->produit->setNblike($this->produit->getNblike() - 1);
		}else if($this->enregistrer == true)
		{
			$this->produit->setNbenregistrer($this->produit->getNbenregistrer() - 1);
		}else if($this->partager == true){
			$this->produit->setNbpartage($this->produit->getNbpartage() - 1);
		}else if($this->voir == true){
			$this->produit->setNbvue($this->produit->getNbvue() - 1);
		}
	}

    /**
     * Set voir
     *
     * @param boolean $voir
     * @return Animationproduit
     */
    public function setVoir($voir)
    {
        $this->voir = $voir;

        return $this;
    }

    /**
     * Get voir
     *
     * @return boolean 
     */
    public function getVoir()
    {
        return $this->voir;
    }

    /**
     * Set recommander
     *
     * @param boolean $recommander
     * @return Animationproduit
     */
    public function setRecommander($recommander)
    {
        $this->recommander = $recommander;

        return $this;
    }

    /**
     * Get recommander
     *
     * @return boolean 
     */
    public function getRecommander()
    {
        return $this->recommander;
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

    public function setProduit(Produit $produit): self
    {
        $this->produit = $produit;

        return $this;
    }

    public function getProduit(): ?Produit
    {
        return $this->produit;
    }

    public function setChapitrecours(Chapitrecours $chapitrecours = null)
    {
        $this->chapitrecours = $chapitrecours;

        return $this;
    }

    public function getChapitrecours(): ?Chapitrecours
    {
        return $this->chapitrecours;
    }

    public function setProduitpanier(Produitpanier $produitpanier = null): self
    {
        $this->produitpanier = $produitpanier;

        return $this;
    }

    public function getProduitpanier(): ?Produitpanier
    {
        return $this->produitpanier;
    }
}

<?php
namespace App\Entity\Produit\Produit;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToArrayTransformer;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\Produit\Produit\PanierRepository;
use App\Entity\Users\User\User;
use App\Entity\Produit\Service\Ville;
use App\Entity\Produit\Produit\Produitpanier;
use App\Entity\Produit\Service\Service;
use App\Entity\Produit\Produit\Chapitrecours;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Panier
 *
 * @ORM\Table("panier")
 * @ORM\Entity(repositoryClass=PanierRepository::class)
 * @ApiResource(
 *    normalizationContext={"groups"={"panier:read"}},
 *    denormalizationContext={"groups"={"panier:write"}}
 * )
 */
 
class Panier
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"panier:read"})
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
     * @ORM\Column(name="payer", type="boolean")
     */
    private $payer;
	
	/**
     * @var boolean
     *
     * @ORM\Column(name="valide", type="boolean")
     */
    private $valide;

    /**
     * @var boolean
     *
     * @ORM\Column(name="livrer", type="boolean")
     */
    private $livrer;
	
	/**
     * @var integer
     *
     * @ORM\Column(name="coastlivraison", type="integer")
     */
	private $coastlivraison;

	/**
       * @ORM\ManyToOne(targetEntity=User::class)
       * @ORM\JoinColumn(nullable=true)
        */
	private $user;
	
	/**
     * @ORM\OneToOne(targetEntity=Ville::class)
     * @ORM\JoinColumn(nullable=true)
    */
	private $ville;
	
	/**
     * @var boolean
     *
     * @ORM\Column(name="livraisonpayer", type="boolean")
     */
    private $livraisonpayer;
	
	/**
     * @var boolean
     *
     * @ORM\Column(name="messadmin", type="boolean")
     */
    private $messadmin;
	
	
	/**
     * @var string
     *
     * @ORM\Column(name="destination", type="string", length=255, nullable=true)
     */
    private $destination;
	
	/**
     * @ORM\OneToMany(targetEntity=Produitpanier::class, mappedBy="panier")
     */
    private $produitpaniers;
	
	/**
     * @ORM\ManyToOne(targetEntity=Service::class)
     * @ORM\JoinColumn(nullable=true)
    */
	private $service;
	
	/**
     * @ORM\ManyToOne(targetEntity=Chapitrecours::class)
     * @ORM\JoinColumn(nullable=true)
    */
	private $chapitrecours;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $montantttc;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $montantspecial;
	
	public function __construct()
    {
        $this->produitpaniers = new \Doctrine\Common\Collections\ArrayCollection();
        $this->date = new \Datetime();
        $this->payer = false;
        $this->livrer = false;
        $this->valide = false;
        $this->livraisonpayer = false;
        $this->coastlivraison = 0;
        $this->messadmin = false;
        $this->montantttc = 0;
        $this->montantspecial = 0;
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
     * @return Panier
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
     * Set payer
     *
     * @param boolean $payer
     * @return Panier
     */
    public function setPayer($payer)
    {
        $this->payer = $payer;

        return $this;
    }

    /**
     * Get payer
     *
     * @return boolean 
     */
    public function getPayer()
    {
        return $this->payer;
    }

    /**
     * Set livrer
     *
     * @param boolean $livrer
     * @return Panier
     */
    public function setLivrer($livrer)
    {
        $this->livrer = $livrer;

        return $this;
    }

    /**
     * Get livrer
     *
     * @return boolean 
     */
    public function getLivrer()
    {
        return $this->livrer;
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

    public function addProduitpanier(Produitpanier $produitpaniers): self
    {
        $this->produitpaniers[] = $produitpaniers;

        return $this;
    }


    public function removeProduitpanier(Produitpanier $produitpaniers)
    {
        $this->produitpaniers->removeElement($produitpaniers);
    }

    public function getProduitpaniers(): ?Collection
    {
        return $this->produitpaniers;
    }

    /**
     * Set livraisonpayer
     *
     * @param boolean $livraisonpayer
     * @return Panier
     */
    public function setLivraisonpayer($livraisonpayer)
    {
        $this->livraisonpayer = $livraisonpayer;

        return $this;
    }

    /**
     * Get livraisonpayer
     *
     * @return boolean 
     */
    public function getLivraisonpayer()
    {
        return $this->livraisonpayer;
    }


    /**
     * Set destination
     *
     * @param string $destination
     * @return Panier
     */
    public function setDestination($destination)
    {
        $this->destination = $destination;

        return $this;
    }

    /**
     * Get destination
     *
     * @return string 
     */
    public function getDestination()
    {
        return $this->destination;
    }

    /**
     * Set coastlivraison
     *
     * @param integer $coastlivraison
     * @return Panier
     */
    public function setCoastlivraison($coastlivraison)
    {
        $this->coastlivraison = $coastlivraison;

        return $this;
    }

    /**
     * Get coastlivraison
     *
     * @return integer 
     */
    public function getCoastlivraison()
    {
        return $this->coastlivraison;
    }

    public function setVille(Ville $ville = null)
    {
        $this->ville = $ville;

        return $this;
    }

    public function getVille(): ?Ville
    {
        return $this->ville;
    }
	
    public function numFacture()
    {
        $datetransform = new DateTimeToArrayTransformer();
        $dt = $datetransform->transform($this->getDate());
        return ''.$dt['day'].''.$this->getId().''.$dt['month'].''.$dt['year'];
    }

    public function dateFacture()
    {
        $datetransform = new DateTimeToArrayTransformer();
        $dt = $datetransform->transform($this->getDate());
        return $dt['day'].'-'.$dt['month'].'-'.$dt['year'];
    }

    public function getUploadDir()
    {
        // On retourne le chemin relatif vers l'image pour un navigateur
        return 'bundles/produit/produit/facture/panier';
    }

    public function getUploadRootDir()
    {
        // On retourne le chemin relatif vers l'image pour notre codePHP
        return  __DIR__.'/../../../../public/'.$this->getUploadDir();
    }

    public function getWebPath()
    {
        return $this->getUploadDir().'/'.$this->numFacture().'.pdf';
    }

    /**
     * Set messadmin
     *
     * @param boolean $messadmin
     * @return Panier
     */
    public function setMessadmin($messadmin)
    {
        $this->messadmin = $messadmin;

        return $this;
    }

    /**
     * Get messadmin
     *
     * @return boolean 
     */
    public function getMessadmin()
    {
        return $this->messadmin;
    }

    /**
     * Set valide
     *
     * @param boolean $valide
     * @return Panier
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


    public function setService(Service $service = null): self
    {
        $this->service = $service;

        return $this;
    }

    public function getService(): ?Service
    {
        return $this->service;
    }

    public function setChapitrecours(Chapitrecours $chapitrecours = null): self
    {
        $this->chapitrecours = $chapitrecours;

        return $this;
    }

    public function getChapitrecours(): ?Chapitrecours
    {
        return $this->chapitrecours;
    }

    public function getMontantttc(): ?int
    {
        return $this->montantttc;
    }

    public function setMontantttc(?int $montantttc): self
    {
        $this->montantttc = $montantttc;

        return $this;
    }

    public function getMontantspecial(): ?float
    {
        return $this->montantspecial;
    }

    public function setMontantspecial(?float $montantspecial): self
    {
        $this->montantspecial = $montantspecial;

        return $this;
    }
}

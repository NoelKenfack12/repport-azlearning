<?php

namespace App\Entity\Produit\Service;

use Doctrine\ORM\Mapping as ORM;
use App\Validator\Validatortext\Taillemin;
use App\Validator\Validatortext\Taillemax;
use App\Repository\Produit\Service\DetailinfoentrepriseRepository;
use App\Entity\Produit\Service\Infoentreprise;
use App\Entity\Users\User\User;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;


/**
 * Detailinfoentreprise
 *
 * @ORM\Table("detailinfoentreprise")
 * @ORM\Entity(repositoryClass=DetailinfoentrepriseRepository::class)
 * @ApiResource(
 *    normalizationContext={"groups"={"detailinfoentreprise:read"}},
 *    denormalizationContext={"groups"={"detailinfoentreprise:write"}}
 * )
 */
class Detailinfoentreprise
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"detailinfoentreprise:read"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="titre", type="string", length=255,nullable=true)
     * @Taillemax(valeur=300, message="Au plus 300 caractès")
     * @Groups({"detailinfoentreprise:read", "detailinfoentreprise:write"})
     */
    private $titre;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
      *@Taillemax(valeur=500, message="Au plus 500 caractès")
     */
    private $description;

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
     * @ORM\ManyToOne(targetEntity=Infoentreprise::class, inversedBy="detailinfoentreprises")
     * @ORM\JoinColumn(nullable=false)
    */
	private $infoentreprise;
	
	/**
       * @ORM\ManyToOne(targetEntity=User::class)
       * @ORM\JoinColumn(nullable=false)
        */
	private $user;
	
	public function __construct()
	{
        $this->date = new \Datetime();
        $this->timestamp = time();
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
     * @return Detailinfoentreprise
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
     * @return Detailinfoentreprise
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
     * @return Detailinfoentreprise
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
     * @return Detailinfoentreprise
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

    public function setInfoentreprise(Infoentreprise $infoentreprise): self
    {
        $this->infoentreprise = $infoentreprise;
		$infoentreprise->addDetailinfoentreprise($this);

        return $this;
    }

    public function getInfoentreprise(): ?Infoentreprise
    {
        return $this->infoentreprise;
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
}

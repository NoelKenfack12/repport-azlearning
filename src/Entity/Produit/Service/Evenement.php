<?php

namespace App\Entity\Produit\Service;

use Doctrine\ORM\Mapping as ORM;
use App\Validator\Validatortext\Taillemin;
use App\Validator\Validatortext\Taillemax;
use App\Service\Servicetext\GeneralServicetext;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\Produit\Service\EvenementRepository;
use App\Entity\Users\User\User;
use App\Entity\Produit\Service\Service;
use App\Entity\Produit\Service\Imgevenement;


/**
 * Evenement
 *
 * @ORM\Table("evenement")
 * @ORM\Entity(repositoryClass=EvenementRepository::class)
 */
class Evenement
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
     *@Taillemin(valeur=3, message="Au moins 3 caractères")
     *@Taillemax(valeur=200, message="Au plus 200 caractès")
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     *@Taillemin(valeur=3, message="Au moins 3 caractères")
     *@Taillemax(valeur=1000, message="Au plus 1000 caractès")
     */
    private $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;
	
	/**
       * @ORM\ManyToOne(targetEntity=User::class)
       * @ORM\JoinColumn(nullable=false)
        */
	private $user;
	
	/**
       * @ORM\ManyToOne(targetEntity=Service::class, inversedBy="evenements")
       * @ORM\JoinColumn(nullable=false)
        */
	private $service;
	
	/**
     * @ORM\OneToOne(targetEntity=Imgevenement::class,  cascade={"persist","remove"})
     * @ORM\JoinColumn(nullable=true)
	*@Assert\Valid()
    */
	private $imgevenement;
	
	/**
     * @var integer
     *
     * @ORM\Column(name="rang", type="integer")
     */
    private $rang;

	
	// variable du service de normalisation des noms des pays.
	private $servicetext;
	
	public function __construct(GeneralServicetext $service)
	{
        $this->servicetext = $service;
        $this->date = new \Datetime();
        $this->rang = 1;
	}

	public function setServicetext( GeneralServicetext $service)
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
     * Set nom
     *
     * @param string $nom
     * @return Evenement
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
     * @return Evenement
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
     * @return Evenement
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

    public function setUser(User $user): seft
    {
        $this->user = $user;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setService(Service $service): self
    {
        $this->service = $service;
		$service->addEvenement($this);

        return $this;
    }

    public function getService(): ?Service
    {
        return $this->service;
    }

    /**
     * Set rang
     *
     * @param integer $rang
     * @return Evenement
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

    public function setImgevenement(Imgevenement $imgevenement = null): self
    {
        $this->imgevenement = $imgevenement;

        return $this;
    }

    public function getImgevenement(): ?Imgevenement
    {
        return $this->imgevenement;
    }
}

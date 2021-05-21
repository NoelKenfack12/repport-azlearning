<?php
namespace App\Entity\Produit\Service;

use Doctrine\ORM\Mapping as ORM;
use App\Validator\Validatortext\Taillemin;
use App\Validator\Validatortext\Taillemax;
use App\Service\Servicetext\GeneralServicetext;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use App\Repository\Produit\Service\QuartierRepository;
use App\Entity\Users\User\User;
use App\Entity\Produit\Service\Ville;

/**
 * Quartier
 *
 * @ORM\Table("quartier")
 * @ORM\Entity(repositoryClass=QuartierRepository::class)
 */
 
class Quartier
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
     *@Taillemax(valeur=50, message="Au plus 50 caractès")
     */
    private $nom;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;
	
	/**
       * @ORM\ManyToOne(targetEntity=User:class)
       * @ORM\JoinColumn(nullable=true)
    */
	private $user;
	
	/**
       * @ORM\ManyToOne(targetEntity=Ville::class)
       * @ORM\JoinColumn(nullable=true)
    */
	private $ville;
	
	// variable du service de normalisation des noms des pays.
	private $servicetext;
	
	public function __construct(GeneralServicetext $service)
	{
		$this->servicetext = $service;
		$this->date = new \Datetime();
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
     * Set nom
     *
     * @param string $nom
     * @return Quartier
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
     * @return Quartier
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

    public function setUser(User $user = null): self
    {
        $this->user = $user;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setVille(Ville $ville = null): self
    {
        $this->ville = $ville;

        return $this;
    }

    public function getVille(): ?Ville
    {
        return $this->ville;
    }
}

<?php
namespace App\Entity\Produit\Service;

use App\Repository\Produit\Service\ProduitformationRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Produit\Service\Service;
use App\Entity\Produit\Produit\Produit;

/**
 * Produitformation
 *
 * @ORM\Table("produitformation")
 * @ORM\Entity(repositoryClass=ProduitformationRepository::class)
 */

class Produitformation
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
     * @var integer
     *
     * @ORM\Column(name="rang", type="integer")
     */
    private $rang;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;
	
	/**
       * @ORM\ManyToOne(targetEntity=Service::class,inversedBy="produitformations")
       * @ORM\JoinColumn(nullable=false)
    */
	private $service;
	
	/**
       * @ORM\ManyToOne(targetEntity=Produit::class,inversedBy="produitformations")
       * @ORM\JoinColumn(nullable=false)
    */
	private $produit;
	
	public function __construct()
	{
		$this->date = new \Datetime();
		$this->rang = 0;
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
     * Set rang
     *
     * @param integer $rang
     * @return Produitformation
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
     * Set date
     *
     * @param \DateTime $date
     * @return Produitformation
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

    public function setService(Service $service): self
    {
        $this->service = $service;
		$service->addProduitformation($this);

        return $this;
    }

    public function getService(): ?Service
    {
        return $this->service;
    }

    public function setProduit(Produit $produit): self
    {
        $this->produit = $produit;
		$produit->addProduitformation($this);

        return $this;
    }

    public function getProduit(): ?Produit
    {
        return $this->produit;
    }
}

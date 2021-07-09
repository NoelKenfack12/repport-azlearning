<?php
namespace App\Entity\Produit\Produit;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\Produit\Produit\RecommandationRepository;
use App\Entity\Produit\Produit\Produit;
use App\Entity\Produit\Produit\Cataloguechantier;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Recommandation
 *
 * @ORM\Table("recommandation")
 * @ORM\Entity(repositoryClass=RecommandationRepository::class)
 * @ApiResource(
 *    normalizationContext={"groups"={"recommandation:read"}},
 *    denormalizationContext={"groups"={"recommandation:write"}}
 * )
 */
class Recommandation
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"recommandation:read"})
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
     * @ORM\Column(name="note", type="integer")
     */
    private $note;
	
	/**
      * @ORM\ManyToOne(targetEntity=Produit::class)
      * @ORM\JoinColumn(nullable=false)
      */
    private $produit;
	
	/**
      * @ORM\ManyToOne(targetEntity=Cataloguechantier::class)
      * @ORM\JoinColumn(nullable=false)
      */
    private $cataloguechantier;
	
	public function __construct()
	{
		$this->date = new \Datetime();
		$this->note = 0;
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
     * @return Recommandation
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
     * Set note
     *
     * @param integer $note
     * @return Recommandation
     */
    public function setNote($note)
    {
        $this->note = $note;

        return $this;
    }

    /**
     * Get note
     *
     * @return integer 
     */
    public function getNote()
    {
        return $this->note;
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

    public function setCataloguechantier(Cataloguechantier $cataloguechantier): self
    {
        $this->cataloguechantier = $cataloguechantier;

        return $this;
    }

    public function getCataloguechantier(): ?Cataloguechantier
    {
        return $this->cataloguechantier;
    }
}

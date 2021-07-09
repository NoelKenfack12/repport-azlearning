<?php

namespace App\Entity\Produit\Service;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\Produit\Service\CurentwebsiteRepository;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Curentwebsite
 *
 * @ORM\Table("curentwebsite")
 * @ORM\Entity(repositoryClass=CurentwebsiteRepository::class)
 * @ApiResource(
 *    normalizationContext={"groups"={"curentwebsite:read"}},
 *    denormalizationContext={"groups"={"curentwebsite:write"}}
 * )
 */
class Curentwebsite
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"curentwebsite:read"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255)
     * @Groups({"curentwebsite:read", "curentwebsite:write"})
     */
    private $nom;


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
     * @return Curentwebsite
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
}

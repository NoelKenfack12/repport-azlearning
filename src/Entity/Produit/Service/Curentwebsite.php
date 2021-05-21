<?php

namespace App\Entity\Produit\Service;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\Produit\Service\CurentwebsiteRepository;

/**
 * Curentwebsite
 *
 * @ORM\Table("curentwebsite")
 * @ORM\Entity(repositoryClass=CurentwebsiteRepository::class)
 */
class Curentwebsite
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

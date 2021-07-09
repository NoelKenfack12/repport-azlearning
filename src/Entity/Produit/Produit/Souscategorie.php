<?php
namespace App\Entity\Produit\Produit;

use Doctrine\ORM\Mapping as ORM;
use App\Validator\Validatortext\Taillemin;
use App\Validator\Validatortext\Taillemax;
use App\Service\Servicetext\GeneralServicetext;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use App\Validator\Validatorfile\Image;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\EntityManager;
use App\Repository\Produit\Produit\SouscategorieRepository;
use App\Entity\Users\User\User;
use App\Entity\Produit\Produit\Categorie;
use App\Entity\Produit\Produit\Produit;
use App\Entity\Produit\Service\Service;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;


/**
 * Souscategorie
 *
 * @ORM\Table("souscategorie")
 * @ORM\Entity(repositoryClass=SouscategorieRepository::class)
 * @ApiResource(
 *    normalizationContext={"groups"={"souscategorie:read"}},
 *    denormalizationContext={"groups"={"souscategorie:write"}}
 * )
 **@ORM\HasLifecycleCallbacks
*/
 
class Souscategorie
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"souscategorie:read"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255)
     * @Taillemin(valeur=3, message="Au moins 3 caractères")
     * @Taillemax(valeur=100, message="Au plus 100 caractès")
     * @Groups({"souscategorie:read", "souscategorie:write"})
     */
    private $nom;
	
	/**
     * @var text
     *
     * @ORM\Column(name="contenu", type="text", nullable=true)
     * @Taillemax(valeur=600, message="Au plus 600 caractès")
     * @Groups({"souscategorie:read", "souscategorie:write"})
     */
    private $contenu;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;
	
	/**
     * @var string
     *
     * @ORM\Column(name="datetext", type="string", length=255, nullable=true)
     */
    private $datetext;
	
	/**
     * @var integer
     *
     * @ORM\Column(name="nbvente", type="integer")
     */
	private $nbvente;
	
	/**
     * @var integer
     *
     * @ORM\Column(name="nbproduit", type="integer")
     */
	private $nbproduit;
	
	/**
     * @var integer
     *
     * @ORM\Column(name="rang", type="integer")
     */
    private $rang;
	
	/**
     * @var integer
     *
     * @ORM\Column(name="cagnote", type="integer")
     */
    private $cagnote;
	
	/**
     * @var integer
     *
     * @ORM\Column(name="gain", type="integer")
     */
    private $gain;
	
	/**
     * @var integer
     *
     * @ORM\Column(name="nbgagnant", type="integer")
     */
    private $nbgagnant;
	
	/**
     * @var boolean
     *
     * @ORM\Column(name="fermer", type="boolean")
     */
    private $fermer;
	
	/**
     * @var string
     *
     * @ORM\Column(name="src", type="string", length=255, nullable=true)
     */
    private $src;

    /**
     * @var string
     *
     * @ORM\Column(name="alt", type="string", length=255, nullable=true)
     */
    private $alt;
	
	/**
	*@Image(taillemax=1500000, message="la taille de l'image  %string% est grande.")
	*/
	private $file;
	
	// permet le stocage temporaire du nom du fichier
	private $tempFilename;
	
	/**
      * @ORM\ManyToOne(targetEntity=User::class)
      * @ORM\JoinColumn(nullable=false)
      */
    private $user;
	
	 /**
     * @ORM\ManyToOne(targetEntity=Categorie::class, inversedBy="souscategories")
     * @ORM\JoinColumn(nullable=false)
    */
	private $categorie;
	
	/**
     * @ORM\OneToMany(targetEntity=Produit::class, mappedBy="souscategorie")
    */
    private $produits;
	
	/**
     * @ORM\OneToMany(targetEntity=Service::class, mappedBy="souscategorie")
     */
    private $services;
	
	private $servicetext;
	
	private $datepicket;
	
	private $nbprodinval;
	
	private $nbprodval;
	private $nbprodachive;
	
	private $active;
	
	private $em;
	
	public function __construct(GeneralServicetext $service)
	{
		$this->servicetext = $service;
		$this->date = new \Datetime();
		$this->produits = new \Doctrine\Common\Collections\ArrayCollection();
		$this->services = new \Doctrine\Common\Collections\ArrayCollection();
		$this->nbvente = 0;
		$this->rang = 0;
		$this->nbproduit = 0;
		$this->cagnote = 0;
		$this->gain = 0;
		$this->nbgagnant = 0;
		$this->fermer = false;
	}
	
	public function getServicetext()
	{
		return $this->servicetext;
	}
	
	public function setServicetext(GeneralServicetext $service)
	{
		$this->servicetext = $service;
		return $this;
	}
	
	public function getDatepicket()
	{
		return $this->datepicket;
	}
	
	public function setDatepicket($datepicket)
	{
		$this->datepicket = $datepicket;
	}
	
	public function getActive()
	{
		return $this->active;
	}
	
	public function setActive($active)
	{
		$this->active = $active;
	}
	
	public function getEm()
	{
		return $this->em;
	}

	public function setEm(EntityManager $em)
	{
		$this->em = $em;
		return $this;
	}
	
	public function getNbprodinval()
	{
		return $this->nbprodinval;
	}

	public function setNbprodinval($nbre)
	{
		$this->nbprodinval = $nbre;
		return $this;
	}
	
	public function getNbprodval()
	{
		return $this->nbprodval;
	}

	public function setNbprodval($nbre)
	{
		$this->nbprodval = $nbre;
		return $this;
	}
	
	public function getNbprodachive()
	{
		return $this->nbprodachive;
	}

	public function setNbprodachive($nbre)
	{
		$this->nbprodachive = $nbre;
		return $this;
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
     * @return Souscategorie
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
     * @return Souscategorie
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
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function premajuscule()
	{
	//$text1 = $this->servicetext->retireAccent($this->nom);
	$text1 = strtolower($this->nom);
	$this->nom = ucwords($text1);
	
	$date = $this->servicetext->normaliseDate($this->datepicket);
	$this->date = new \Datetime($date);
	}

    public function setCategorie(Categorie $categorie): self
    {
        $this->categorie = $categorie;
		$categorie->addSouscategory($this);

        return $this;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
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

    public function addProduit(Produit $produits): ?Produit
    {
        $this->produits[] = $produits;

        return $this;
    }

    public function removeProduit(Produit $produits)
    {
        $this->produits->removeElement($produits);
    }

    public function getProduits(): ?Collection
    {
        return $this->produits;
    }

    /**
     * Set nbvente
     *
     * @param integer $nbvente
     * @return Souscategorie
     */
    public function setNbvente($nbvente)
    {
        $this->nbvente = $nbvente;

        return $this;
    }

    /**
     * Get nbvente
     *
     * @return integer 
     */
    public function getNbvente()
    {
        return $this->nbvente;
    }
	
	//permet la récupération du nom du fichier temporaire
    public function getTempFilename()
    {
    return $this->tempFilename;
    }
	//permet de modifier le contenu de la variable tempFilename
    public function setTempFilename($temp)
	{
	$this->tempFilename=$temp;
	}
	// permet la récupération du nom du fiechier
	public function getFile()
	{
	return $this->file;
	}
	
	public function getUploadDir()
	{
	// On retourne le chemin relatif vers l'image pour un navigateur
	return 'bundles/produit/produit/images/souscategorie';
	}
	protected function getUploadRootDir()
	{
	// On retourne le chemin relatif vers l'image pour notre codePHP
	return  __DIR__.'/../../../../public/'.$this->getUploadDir();
	}
	public function setFile(UploadedFile $file)
	{
	$this->file = $file;
	// On vérifie si on avait déjà un fichier pour cette entité
	if (null !== $this->src) {
	// On sauvegarde l'extension du fichier pour le supprimer plus tard
	$this->tempFilename = $this->src;
	// On réinitialise les valeurs des attributs url et alt
	$this->src = null;
	$this->alt = null;
	}
	}
	/**
	* @ORM\PrePersist()
	* @ORM\PreUpdate()
	*/
	public function preUpload()
	{
	$text1 = $this->servicetext->retireAccent($this->nom);
	$text1 = strtolower($text1);
	$this->nom = ucwords($text1);
	if (null === $this->file) {
	return;
	}
	$text = $this->file->getClientOriginalName();
	$this->src = $this->servicetext->normaliseText($text);
	$this->alt = $this->src;
	}
	
	/**
	* @ORM\PostPersist()
	* @ORM\PostUpdate()
	*/
	public function upload()
	{
	// Si jamais il n'y a pas de fichier (champ facultatif)
	if (null === $this->file) {
	return;
	}
	if (null !== $this->tempFilename) {
	$oldFile = $this->getUploadRootDir().'/'.$this->id.'.'.$this->tempFilename;
	if (file_exists($oldFile)) {
	unlink($oldFile);
	}
	}
	$this->file->move( $this->getUploadRootDir(), $this->id.'.'.$this->src);
	}
	/**
	*@ORM\PreRemove()
	*/
	public function preRemoveUpload()
	{
	$this->tempFilename = $this->getUploadRootDir().'/'.$this->id.'.'.$this->src;
	}
	
	/**
	* @ORM\PostRemove()
	*/
	public function postRemoveUpload()
	{
	// En PostRemove, on n'a pas accès à l'id, on utilise notre nom sauvegardé
	if (file_exists($this->tempFilename)) {
	// On supprime le fichier
	unlink($this->tempFilename);
	}
	}
	
	public function getWebPath()
	{
	return $this->getUploadDir().'/'.$this->getId().'.'.$this->getSrc();
	}

    /**
     * Set rang
     *
     * @param integer $rang
     * @return Souscategorie
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
     * Set src
     *
     * @param string $src
     * @return Souscategorie
     */
    public function setSrc($src)
    {
        $this->src = $src;

        return $this;
    }

    /**
     * Get src
     *
     * @return string 
     */
    public function getSrc()
    {
        return $this->src;
    }

    /**
     * Set alt
     *
     * @param string $alt
     * @return Souscategorie
     */
    public function setAlt($alt)
    {
        $this->alt = $alt;

        return $this;
    }

    /**
     * Get alt
     *
     * @return string 
     */
    public function getAlt()
    {
        return $this->alt;
    }

    /**
     * Set nbproduit
     *
     * @param integer $nbproduit
     * @return Souscategorie
     */
    public function setNbproduit($nbproduit)
    {
        $this->nbproduit = $nbproduit;

        return $this;
    }

    /**
     * Get nbproduit
     *
     * @return integer 
     */
    public function getNbproduit()
    {
        return $this->nbproduit;
    }
	
	public function getProduitValide()
	{
		$liste_produit = new \Doctrine\Common\Collections\ArrayCollection();
		if($this->getEm() != null)
		{
			$liste_produit = $this->getEm()->getRepository(Produit::class)
			                               ->listeProduitVal($this->GetId());
		}
		return $liste_produit;
	}

    /**
     * Set prix
     *
     * @param integer $prix
     * @return Souscategorie
     */
    public function setPrix($prix)
    {
        $this->prix = $prix;

        return $this;
    }

    /**
     * Get prix
     *
     * @return integer 
     */
    public function getPrix()
    {
        return $this->prix;
    }

    /**
     * Set contenu
     *
     * @param string $contenu
     * @return Souscategorie
     */
    public function setContenu($contenu)
    {
        $this->contenu = $contenu;

        return $this;
    }

    /**
     * Get contenu
     *
     * @return string 
     */
    public function getContenu()
    {
        return $this->contenu;
    }

    /**
     * Set datetext
     *
     * @param string $datetext
     * @return Souscategorie
     */
    public function setDatetext($datetext)
    {
        $this->datetext = $datetext;

        return $this;
    }

    /**
     * Get datetext
     *
     * @return string 
     */
    public function getDatetext()
    {
        return $this->datetext;
    }

    /**
     * Set cagnote
     *
     * @param integer $cagnote
     * @return Souscategorie
     */
    public function setCagnote($cagnote)
    {
        $this->cagnote = $cagnote;

        return $this;
    }

    /**
     * Get cagnote
     *
     * @return integer 
     */
    public function getCagnote()
    {
        return $this->cagnote;
    }

    /**
     * Set gain
     *
     * @param integer $gain
     * @return Souscategorie
     */
    public function setGain($gain)
    {
        $this->gain = $gain;

        return $this;
    }

    /**
     * Get gain
     *
     * @return integer 
     */
    public function getGain()
    {
        return $this->gain;
    }

    /**
     * Set fermer
     *
     * @param boolean $fermer
     * @return Souscategorie
     */
    public function setFermer($fermer)
    {
        $this->fermer = $fermer;

        return $this;
    }

    /**
     * Get fermer
     *
     * @return boolean 
     */
    public function getFermer()
    {
        return $this->fermer;
    }

    /**
     * Set nbgagnant
     *
     * @param integer $nbgagnant
     * @return Souscategorie
     */
    public function setNbgagnant($nbgagnant)
    {
        $this->nbgagnant = $nbgagnant;

        return $this;
    }

    /**
     * Get nbgagnant
     *
     * @return integer 
     */
    public function getNbgagnant()
    {
        return $this->nbgagnant;
    }


    public function addService(Service $services): self
    {
        $this->services[] = $services;

        return $this;
    }

    public function removeService(Service $services)
    {
        $this->services->removeElement($services);
    }

    public function getServices(): ?Collection
    {
        return $this->services;
    }
	
	public function getServiceSouscategorie()
	{
		$liste_service = $this->em->getRepository(Service::class)
		                          ->findServiceScat($this->getId());
		return $liste_service;
	}
	
	public function getProduitSouscategorie()
	{
		$liste_produit = $this->em->getRepository(Produit::class)
	                                 ->myFindBy($this->getId());
		return $liste_produit;
	}
}

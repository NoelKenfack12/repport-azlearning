<?php
namespace App\Entity\Produit\Produit;

use Doctrine\ORM\Mapping as ORM;
use App\Service\Servicetext\GeneralServicetext;
use App\Validator\Validatorfile\Yourfile;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Repository\Produit\Produit\ComposexerciceRepository;
use App\Entity\Produit\Produit\Exercicepartie;
use App\Entity\Produit\Produit\Produitpanier;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Composexercice
 *
 * @ORM\Table("composexercice")
 * @ORM\Entity(repositoryClass=ComposexerciceRepository::class)
 *  @ApiResource(
 *    normalizationContext={"groups"={"composexercice:read"}},
 *    denormalizationContext={"groups"={"composexercice:write"}}
 * )
 ** @ORM\HasLifecycleCallbacks
*/
class Composexercice
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"composexercice:read"})
     */
    private $id;
	
	/**
     * @var string
     *
     * @ORM\Column(name="alt", type="string", length=255,nullable=true)
     */
    private $alt;

    /**
     * @var string
     *
     * @ORM\Column(name="src", type="string", length=255,nullable=true)
     */
    private $src;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var boolean
     *
     * @ORM\Column(name="fermer", type="boolean")
     */
    private $fermer;

    /**
     * @var integer
     *
     * @ORM\Column(name="lastvalidation", type="bigint")
     */
    private $lastvalidation;

    /**
     * @var float
     *
     * @ORM\Column(name="note", type="float")
     */
    private $note;
	
	/**
     * @ORM\ManyToOne(targetEntity=Exercicepartie::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $exercicepartie;
	
	/**
     * @ORM\ManyToOne(targetEntity=Produitpanier::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $produitpanier;
	
	/**
	*@Yourfile(taillemax=100000000, message="la taille de l'image  %string% est grande.")
	*/
	private $file;
	
	// permet le stocage temporaire du nom du fichier
	private $tempFilename;
	
	// variable du service de normalisation des noms de fichier
	private $servicetext;
	
	public function __construct(GeneralServicetext $service)
	{
		$this->src ="source";
		$this->alt ="alternatif";
		$this->date = new \Datetime();
		$this->fermer = false;
		$this->note = 0;
		$this->servicetext = $service;
		$this->lastvalidation = 0;
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
     * @return Composexercice
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
     * Set fermer
     *
     * @param boolean $fermer
     * @return Composexercice
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
     * Set lastvalidation
     *
     * @param integer $lastvalidation
     * @return Composexercice
     */
    public function setLastvalidation($lastvalidation)
    {
        $this->lastvalidation = $lastvalidation;

        return $this;
    }

    /**
     * Get lastvalidation
     *
     * @return integer 
     */
    public function getLastvalidation()
    {
        return $this->lastvalidation;
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
	
	public function setServicetext( GeneralServicetext $service)
	{
	$this->servicetext = $service;
	}
	public function getServicetext()
	{
	return $this->servicetext;
	}
	
	public function getUploadDir()
	{
	// On retourne le chemin relatif vers l'image pour un navigateur
	return 'bundles/produit/produit/compos/exercices';
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
	
	public function getWebpath()
	{
	return $this->getUploadDir().'/'.$this->getId().'.'.$this->getSrc();
	}

    /**
     * Set alt
     *
     * @param string $alt
     * @return Composexercice
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
     * Set src
     *
     * @param string $src
     * @return Composexercice
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

    public function setExercicepartie(Exercicepartie $exercicepartie): self
    {
        $this->exercicepartie = $exercicepartie;

        return $this;
    }

    public function getExercicepartie(): ?Exercicepartie
    {
        return $this->exercicepartie;
    }

    public function setProduitpanier(Produitpanier $produitpanier): self
    {
        $this->produitpanier = $produitpanier;

        return $this;
    }


    public function getProduitpanier(): ?Produitpanier
    {
        return $this->produitpanier;
    }

    /**
     * Set note
     *
     * @param float $note
     * @return Composexercice
     */
    public function setNote($note)
    {
        $this->note = $note;

        return $this;
    }

    /**
     * Get note
     *
     * @return float 
     */
    public function getNote()
    {
        return $this->note;
    }
}

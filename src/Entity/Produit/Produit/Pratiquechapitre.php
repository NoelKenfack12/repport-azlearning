<?php
namespace App\Entity\Produit\Produit;

use Doctrine\ORM\Mapping as ORM;
use App\Validator\Validatorfile\Yourfile;
use App\Service\Servicetext\GeneralServicetext;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Validator\Validatortext\Taillemin;
use App\Validator\Validatortext\Taillemax;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\Produit\Produit\PratiquechapitreRepository;
use App\Entity\Produit\Produit\Chapitrecours;
use App\Entity\Produit\Produit\Correctionpratique;

/**
 * Pratiquechapitre
 *
 * @ORM\Table("pratiquechapitre")
 * @ORM\Entity(repositoryClass=PratiquechapitreRepository::class)
 ** @ORM\HasLifecycleCallbacks
*/
class Pratiquechapitre
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
     * @var integer
     *
     * @ORM\Column(name="bareme", type="integer")
     */
    private $bareme;
	
	/**
     * @var integer
     *
     * @ORM\Column(name="num", type="integer")
     */
    private $num;
	
	/**
       * @ORM\ManyToOne(targetEntity=Chapitrecours::class, inversedBy="pratiquechapitres")
       * @ORM\JoinColumn(nullable=false)
        */
	private $chapitrecours;
	
	/**
       * @ORM\OneToOne(targetEntity=Correctionpratique::class, cascade={"persist", "remove"})
      * @Assert\Valid()
       */
   private $correctionpratique;
	
	/**
	*@Yourfile(taillemax=100000000, message="la taille de l'image  %string% est grande.")
	*/
	private $file;
	
	// permet le stocage temporaire du nom du fichier
	private $tempFilename;
	
	// variable du service de normalisation des noms de fichier
	private $servicetext;
	
	public function __construct()
	{
	$this->src ="source";
	$this->alt ="alternatif";
	$this->date = new \Datetime();
	$this->bareme = 20;
	$this->num = 0;
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
     * Set alt
     *
     * @param string $alt
     * @return Pratiquechapitre
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
     * @return Pratiquechapitre
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
     * Set date
     *
     * @param \DateTime $date
     * @return Pratiquechapitre
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
     * Set bareme
     *
     * @param integer $bareme
     * @return Pratiquechapitre
     */
    public function setBareme($bareme)
    {
        $this->bareme = $bareme;

        return $this;
    }

    /**
     * Get bareme
     *
     * @return integer 
     */
    public function getBareme()
    {
        return $this->bareme;
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
	return 'bundles/produit/produit/supports/pratiquechapitre';
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
	
	public function getNumRessource()
	{
	return ($this->getChapitrecours()->getPartiecours()->getId() + 25*12*85) * $this->id; //numéro du roman.
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
	$oldFile = $this->getUploadRootDir().'/'.$this->getNumRessource().'.'.$this->tempFilename;
	if (file_exists($oldFile)) {
	unlink($oldFile);
	}
	}
	$this->file->move( $this->getUploadRootDir(), $this->getNumRessource().'.'.$this->src);
	}
	
	/**
	*@ORM\PreRemove()
	*/
	public function preRemoveUpload()
	{
	$this->tempFilename = $this->getUploadRootDir().'/'.$this->getNumRessource().'.'.$this->src;
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
	return $this->getUploadDir().'/'.$this->getNumRessource().'.'.$this->getSrc();
	}

    public function setChapitrecours(Chapitrecours $chapitrecours): self
    {
        $this->chapitrecours = $chapitrecours;
		$chapitrecours->addPratiquechapitre($this);

        return $this;
    }

    public function getChapitrecours(): ?Chapitrecours
    {
        return $this->chapitrecours;
    }

    /**
     * Set num
     *
     * @param integer $num
     * @return Pratiquechapitre
     */
    public function setNum($num)
    {
        $this->num = $num;

        return $this;
    }

    /**
     * Get num
     *
     * @return integer 
     */
    public function getNum()
    {
        return $this->num;
    }

    public function setCorrectionpratique(Correctionpratique $correctionpratique = null): self
    {
        $this->correctionpratique = $correctionpratique;

        return $this;
    }

    public function getCorrectionpratique(): ?Correctionpratique
    {
        return $this->correctionpratique;
    }
}

<?php

namespace App\Entity\Produit\Service;

use Doctrine\ORM\Mapping as ORM;
use App\Validator\Validatortext\Email;
use App\Validator\Validatortext\Siteweb;
use App\Validator\Validatortext\Taillemin;
use App\Validator\Validatortext\Taillemax;
use App\Repository\Produit\Service\CommentaireblogRepository;
use App\Entity\Users\User\User;
use App\Entity\Produit\Produit\Produit;
use App\Entity\Produit\Produit\Chapitrecours;
use App\Entity\Produit\Service\Service;
use App\Entity\Produit\Service\Intervention;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Commentaireblog
 *
 * @ORM\Table("commentaireblog")
 * @ORM\Entity(repositoryClass=CommentaireblogRepository::class)
 * @ApiResource(
 *    normalizationContext={"groups"={"commentaireblog:read"}},
 *    denormalizationContext={"groups"={"commentaireblog:write"}}
 * )
 */
class Commentaireblog
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"commentaireblog:read"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="titre", type="string", length=255,nullable=true)
     *@Taillemax(valeur=150, message="Au plus 100 caractès")
     * @Groups({"commentaireblog:read", "commentaireblog:write"})
     */
    private $titre;

    /**
     * @var string
     *
     * @ORM\Column(name="contenu", type="text")
     *@Taillemax(valeur=1000, message="Au plus 500 caractès")
     */
    private $contenu;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;
	
	/**
     * @var boolean
     *
     * @ORM\Column(name="message", type="boolean")
     */
    private $message;
	
	/**
     * @var boolean
     *
     * @ORM\Column(name="lut", type="boolean")
     */
    private $lut;

    /**
     * @var integer
     *
     * @ORM\Column(name="timestamp", type="bigint")
     */
    private $timestamp;
	
	/**
     * @var integer
     *
     * @ORM\Column(name="nbmessage", type="integer")
     */
    private $nbmessage;
	
	/**
       * @ORM\ManyToOne(targetEntity=User::class)
       * @ORM\JoinColumn(nullable=true)
     */
	private $user;
	
	/**
       * @ORM\ManyToOne(targetEntity=User::class)
       * @ORM\JoinColumn(nullable=true)
    */
	private $dest;
	
	/**
       * @ORM\ManyToOne(targetEntity=Produit::class)
       * @ORM\JoinColumn(nullable=true)
     */
	private $produit;
	
	/**
       * @ORM\ManyToOne(targetEntity=Chapitrecours::class)
       * @ORM\JoinColumn(nullable=true)
     */
	private $chapitrecours;
	
	/**
       * @ORM\ManyToOne(targetEntity=Service::class,inversedBy="commentaireblogs")
       * @ORM\JoinColumn(nullable=true)
        */
	private $service;
	
	/**
         * @ORM\OneToMany(targetEntity=Intervention::class, mappedBy="commentaireblog")
         */
    private $interventions;
	
	
    private $em;
	
	public function __construct()
	{
		$this->date = new \Datetime();
		$this->timestamp = time();
		$this->nbmessage = 0;
		$this->message = false;
		$this->lut = false;
		$this->interventions = new \Doctrine\Common\Collections\ArrayCollection();
	}

	public function getEm()
	{
		return $this->em;
	}
	
	public function setEm($em)
	{
		return $this->em = $em;
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
     * Set contenu
     *
     * @param string $contenu
     * @return Commentaireblog
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
     * Set date
     *
     * @param \DateTime $date
     * @return Commentaireblog
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
     * @return Commentaireblog
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

    public function setUser(User $user = null): self
    {
        $this->user = $user;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setService(Service $service=null): self
    {
        $this->service = $service;
		$service->addCommentaireblog($this);

        return $this;
    }

    public function getService(): ?Service
    {
        return $this->service;
    }
	
	public function getDuree()
	{
		$seconde = time() - $this->timestamp;
		$minute = round($seconde/60);
		if($minute < 60)
		{
		   if($minute < 3)
		   {
			   $duree = 'Il ya un instant';
		   }else{
			   $duree = 'Il ya '.$minute.' min';
		   }
		}else{
		   if($minute < 60*24)
		   {
		   $duree = 'Il ya '.(floor(($minute/60))).'h'.($minute%60).'min';
		   }else{
			  if($minute < 60*24*30)
			  {
			  $reste = $minute - (floor($minute/(60*24)))*(60*24);
			  $duree = 'Il ya '.(floor($minute/(60*24))).'j'.(floor($reste/60)).'h'.($reste - (floor($reste/60)*60)).'min';
			  }else{
			  $nbjour = $this->date->diff(new \Datetime())->days;
			  $age = floor($nbjour/365);
			  if($age < 1)
			  {
				  $reste = $nbjour - (floor($nbjour/(30)))*(30);
				  if(floor($nbjour/(30)) != 0)
				  {
					  $duree = 'Il ya '.(floor($nbjour/(30))).'Mois'.($reste - (floor($reste/30)*30)).'J';
				  }else{
					  $duree = 'Il ya '.($reste - (floor($reste/30)*30)).'J';
				  }
				  
			  }else{
				  $courantyear = date('Y');
				  $duree = $courantyear - $age; 
			  }
			  }
		   }
		}
		return $duree;
	}

    /**
         * Set titre
         *
         * @param string $titre
         * @return Commentaireblog
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
     * Set nbmessage
     *
     * @param integer $nbmessage
     * @return Commentaireblog
     */
    public function setNbmessage($nbmessage)
    {
        $this->nbmessage = $nbmessage;

        return $this;
    }

    /**
     * Get nbmessage
     *
     * @return integer 
     */
    public function getNbmessage()
    {
        return $this->nbmessage;
    }

    public function addIntervention(Intervention $interventions)
    {
        $this->interventions[] = $interventions;

        return $this;
    }

    public function removeIntervention(Intervention $interventions)
    {
        $this->interventions->removeElement($interventions);
    }

    public function getInterventions(): ?Collection
    {
        return $this->interventions;
    }


    public function setDest(User $dest = null): self
    {
        $this->dest = $dest;

        return $this;
    }

    public function getDest(): ?User
    {
        return $this->dest;
    }

    public function setProduit(Produit $produit = null): self
    {
        $this->produit = $produit;

        return $this;
    }

    public function getProduit(): ?Produit
    {
        return $this->produit;
    }

    public function setChapitrecours(Chapitrecours $chapitrecours = null): self
    {
        $this->chapitrecours = $chapitrecours;

        return $this;
    }

    public function getChapitrecours(): ?Chapitrecours
    {
        return $this->chapitrecours;
    }

    /**
     * Set message
     *
     * @param boolean $message
     * @return Commentaireblog
    */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return boolean 
     */
    public function getMessage()
    {
        return $this->message;
    }
	
	public function getInterventionsMessage()
    {
        $liste_intervention = $this->em->getRepository(Intervention::class)
								       ->findBy(array('commentaireblog'=>$this,'message'=>1), array('date'=>'asc'));
		return $liste_intervention;
    }

    /**
     * Set lut
     *
     * @param boolean $lut
     * @return Commentaireblog
     */
    public function setLut($lut)
    {
        $this->lut = $lut;

        return $this;
    }

    /**
     * Get lut
     *
     * @return boolean 
     */
    public function getLut()
    {
        return $this->lut;
    }
}

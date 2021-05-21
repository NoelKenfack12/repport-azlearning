<?php
namespace App\Entity\Produit\Service;

use Doctrine\ORM\Mapping as ORM;
use App\Validator\Validatortext\Taillemin;
use App\Validator\Validatortext\Taillemax;
use App\Repository\Produit\Service\InterventionRepository;
use App\Entity\Produit\Service\Commentaireblog;
use App\Entity\Users\User\User;

/**
 * Intervention
 *
 * @ORM\Table("intervention")
 * @ORM\Entity(repositoryClass=InterventionRepository::class)
 ** @ORM\HasLifecycleCallbacks
*/
 
class Intervention
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
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var integer
     *
     * @ORM\Column(name="timestamp", type="bigint")
     */
    private $timestamp;
	
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
     * @var string
     *
     * @ORM\Column(name="contenu", type="text")
      *@Taillemax(valeur=2000, message="Au plus 2000 caractès")
     */
    private $contenu;
	
	/**
       * @ORM\ManyToOne(targetEntity=Commentaireblog::class,inversedBy="interventions")
       * @ORM\JoinColumn(nullable=false)
        */
	private $commentaireblog;
	
	/**
       * @ORM\ManyToOne(targetEntity=User::class)
       * @ORM\JoinColumn(nullable=true)
        */
	private $user;
	
	public function __construct()
	{
		$this->date = new \Datetime();
		$this->timestamp = time();
		$this->message = false;
		$this->lut = false;
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
     * @return Intervention
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
     * @return Intervention
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

    /**
     * Set contenu
     *
     * @param string $contenu
     * @return Intervention
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
     * @ORM\PrePersist()
     */
    public function updatecommentaire()
	{
		$this->getCommentaireblog()->setNbmessage($this->getCommentaireblog()->getNbmessage() + 1);
	}
	
	/**
     * @ORM\PreRemove()
     */
    public function removecommentaire()
	{
		$this->getCommentaireblog()->setNbmessage($this->getCommentaireblog()->getNbmessage() - 1);
	}

    public function setCommentaireblog(Commentaireblog $commentaireblog)
    {
        $this->commentaireblog = $commentaireblog;
		$commentaireblog->addIntervention($this);

        return $this;
    }

    public function getCommentaireblog(): ?Commentaireblog
    {
        return $this->commentaireblog;
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

    /**
     * Set message
     *
     * @param boolean $message
     * @return Intervention
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

    /**
     * Set lut
     *
     * @param boolean $lut
     * @return Intervention
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

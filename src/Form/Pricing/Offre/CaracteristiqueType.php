<?php

namespace App\Form\Pricing\Offre;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use App\Entity\Pricing\Offre\Caracteristique;

class CaracteristiqueType extends AbstractType
{
	private $idcat;
	public function __construct($cat = null)
	{
		if($cat != null)
		$idcat = $cat->getId();
	}
	public function getIdCat()
	{
		return $this->idcat;
	}
	public function setIdCat($id)
	{
		$this->idcat = $id;
	}
	
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
		$cat = $options['cat'];
        if($cat != null)
		$id = $cat->getId();
        $builder
            ->add('nom',TextType::class,array('attr'=>array('class'=>'form-control')))
			->add('rang',IntegerType::class,array('attr'=>array('class'=>'form-control')))
            ->add('description',TextareaType::class,array('attr'=>array('class'=>'form-control')))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Caracteristique::class,
            'cat'=> null
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'pricing_offrebundle_caracteristique';
    }
}

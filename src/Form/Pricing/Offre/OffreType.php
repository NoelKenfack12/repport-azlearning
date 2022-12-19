<?php

namespace App\Form\Pricing\Offre;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use App\Entity\Pricing\Offre\Offre;

class OffreType extends AbstractType
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
            ->add('rapport',TextType::class,array('attr'=>array('class'=>'form-control')))
            ->add('contenu',TextareaType::class,array('attr'=>array('class'=>'form-control'),'required'=>false))
            ->add('newprise',TextType::class,array('attr'=>array('class'=>'form-control')))
            ->add('choixauteur',CheckboxType::class, array('required'=>false))
			->add('rang',IntegerType::class,array('attr'=>array('class'=>'form-control')))
			->add('imgoffre',ImgoffreType::class,array('required'=>false))
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Offre::class,
            'cat' => null
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'pricing_offrebundle_offre';
    }
}

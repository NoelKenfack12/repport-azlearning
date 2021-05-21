<?php

namespace App\Form\Produit\Produit;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Produit\Produit\Souscategorie;
use Doctrine\ORM\EntityRepository;
use App\Entity\Produit\Produit\Produit;


class ProduitType extends AbstractType
{
private $idcat;
public function __construct($cat = null)
{
	if($cat != null)
	{
		$this->idcat = $cat->getId();
	}else{
		$this->idcat = 0;
	}
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
		$id = $this->getIdCat();
        $builder
		    ->add('titre',TextType::class,array('attr'=>array('class'=>'form-control','placeholder'=>'Titre du cours','style'=>'width: 100%; border-radius: 0px; font-size: 16px;')))
		    ->add('description',TextareaType::class,array('attr'=>array('class'=>'form-control tinymce-manager','placeholder'=>'Présentation  votre cours','style'=>'width: 100%'),'required'=>false))
		    ->add('objectif',TextareaType::class,array('attr'=>array('class'=>'form-control tinymce-manager','placeholder'=>'Quels sont les objectifs de votre cours  ?','style'=>'width: 100%'),'required'=>false))
		    ->add('prerequis',TextareaType::class,array('attr'=>array('class'=>'form-control tinymce-manager','placeholder'=>'Quel sont les prérequis de votre cours ?','style'=>'width: 100%'),'required'=>false))
			->add('imgproduit',ImgproduitType::class)
			->add('videoproduit',VideoproduitType::class)
			->add('souscategorie',EntityType::class, array(
			'class'=>Souscategorie::class,
			'choice_label'=>'nom',
			'attr'=>array('class'=>'form-control', 'style'=>'width: 100%; border-radius: 0px; font-size: 16px;'),
			'query_builder' => function(EntityRepository $d){
                return $d->getSelectList();
            }
            ))
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
           'data_class' => Produit::class
        ));
    }

    /**
     * @return string
    */
    public function getName()
    {
        return 'produit_produitbundle_produit';
    }
}

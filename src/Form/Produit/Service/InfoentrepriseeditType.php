<?php

namespace App\Form\Produit\Service;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;

class InfoentrepriseeditType extends InfoentrepriseType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
		parent::buildForm($builder, $options);
		$builder->add('titre',TextType::class,array('attr'=>array('placeholder'=>'Titre de la publication','class'=>'form-control','style'=>'width: 100%;')));
		$builder->add('link',UrlType::class,array('attr'=>array('placeholder'=>'Ajouter un lien','class'=>'form-control','style'=>'width: 100%;'),'required'=>false));
        $builder->add('description',TextareaType::class,array('attr'=>array('placeholder'=>'Contenu de la publication','class'=>'form-control','style'=>'width: 100%;height: 60px;')));
        $builder->add('rang',IntegerType::class,array('attr'=>array('placeholder'=>'Rang','class'=>'form-control','style'=>'width: 100%;')));
		$builder->add('imginfoentreprise', ImginfoentrepriseType::class);
	}


    /**
     * @return string
     */
    public function getName()
    {
        return 'produit_servicebundle_infoentrepriseedit';
    }
}

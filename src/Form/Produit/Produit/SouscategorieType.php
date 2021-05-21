<?php

namespace App\Form\Produit\Produit;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Produit\Produit\Categorie;
use App\Entity\Produit\Produit\Souscategorie;

class SouscategorieType extends AbstractType
{
     /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom',TextType::class,array('attr'=>array('placeholder'=>'Nom du module','style'=>'width: 100%;')))
			->add('contenu',TextareaType::class,array('attr'=>array('placeholder'=>'Contenu','style'=>'width: 100%;'),'required'=>false))
			->add('rang',IntegerType::class,array('attr'=>array('placeholder'=>'rang','style'=>'width: 100%;'),'required'=>false))
			->add('file',FileType::class,array('attr'=>array('style'=>'opacity: 0.5;width: 100%;'),'required'=>false))
            ->add('categorie',EntityType::class, array(
			'class'=>Categorie::class,
			'choice_label'=>'nom',
			'multiple'=>false, 
			'attr'=>array('style'=>'width: 100%; padding-left: 30px;')))
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
           'data_class' => Souscategorie::class
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'produit_produitbundle_souscategorie';
    }
}

<?php

namespace App\Form\Produit\Produit;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use App\Entity\Produit\Produit\Categorie;

class CategorieType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom',TextType::class,array('attr'=>array('placeholder'=>'Nom du module de formation','style'=>'width: 100%;')))
            ->add('description',TextareaType::class,array('attr'=>array('placeholder'=>'Description de la catégorie','style'=>'width: 100%;')))
            ->add('rang',IntegerType::class,array('attr'=>array('placeholder'=>'Rang','style'=>'width: 100%;')))
			->add('file',FileType::class,array('attr'=>array('style'=>'opacity: 0.5;width: 100%;'),'required'=>false))
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver)
  	{
  		$resolver->setDefaults(array(
  		   'data_class' => Categorie::class
  		));
  	}

    /**
     * @return string
     */
    public function getName()
    {
        return 'produit_produitbundle_categorie';
    }
}

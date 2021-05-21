<?php


namespace App\Form\Produit\Produit;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use App\Entity\Produit\Produit\Chapitrecours;

class ChapitrecoursType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre',TextType::class,array('attr'=>array('class'=>'form-control','placeholder'=>'Titre du chapitre','style'=>'width: 100%; border-radius: 0px; font-size: 16px;')))
            ->add('contenu',TextareaType::class,array('attr'=>array('class'=>'form-control tinymce-manager','placeholder'=>'Présentation  votre cours','style'=>'width: 100%'),'required'=>false))
            ->add('rang',IntegerType::class,array('attr'=>array('class'=>'form-control','placeholder'=>'Chapitre n°:','style'=>'width: 100%; border-radius: 0px;')))
			->add('imgchapitre',ImgchapitreType::class)
			->add('videochapitre',VideochapitreType::class)
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver)
  	{
  		$resolver->setDefaults(array(
  		   'data_class' => Chapitrecours::class
  		));
  	}


    /**
     * @return string
     */
    public function getName()
    {
        return 'produit_produitbundle_chapitrecours';
    }
}

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
use App\Entity\Produit\Produit\Partiecours;


class PartiecoursType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre',TextType::class,array('attr'=>array('class'=>'form-control','placeholder'=>'Titre de la partie','style'=>'width: 100%;')))
            ->add('rang',IntegerType::class,array('attr'=>array('class'=>'form-control','placeholder'=>'Numéro de classement','style'=>'width: 100%;')))
        ;
    }


    public function configureOptions(OptionsResolver $resolver)
  	{
  		$resolver->setDefaults(array(
  		   'data_class' => Partiecours::class
  		));
  	}

    /**
     * @return string
     */
    public function getName()
    {
        return 'produit_produitbundle_partiecours';
    }
}

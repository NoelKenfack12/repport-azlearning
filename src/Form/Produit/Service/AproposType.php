<?php

namespace App\Form\Produit\Service;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Produit\Service\Apropos;

class AproposType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom',TextType::class,array('attr'=>array('placeholder'=>'Nom de l\'auteur','class'=>'form-control','style'=>'width: 100%;')))
            ->add('poste',TextType::class,array('attr'=>array('placeholder'=>'Poste de l\'auteur','class'=>'form-control','style'=>'width: 100%;'),'required'=>false))
            ->add('contenu',TextareaType::class,array('attr'=>array('placeholder'=>'Contenu','class'=>'form-control','style'=>'width: 100%;')))
            ->add('rang',IntegerType::class,array('attr'=>array('placeholder'=>'Rang','class'=>'form-control','style'=>'width: 100%;')))
            ->add('facebook',TextType::class,array('attr'=>array('placeholder'=>'Adresse Facebook','class'=>'form-control','style'=>'width: 100%;'),'required'=>false))
            ->add('twitter',TextType::class,array('attr'=>array('placeholder'=>'Adresse twitter','class'=>'form-control','style'=>'width: 100%;'),'required'=>false))
            ->add('google',TextType::class,array('attr'=>array('placeholder'=>'Adresse google','class'=>'form-control','style'=>'width: 100%;'),'required'=>false))
            ->add('file',FileType::class)
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
           'data_class' => Apropos::class
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'produit_servicebundle_apropos';
    }
}

<?php

namespace App\Form\Produit\Produit;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use App\Entity\Produit\Produit\Supportchapitre;

class SupportchapitreType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file',FileType::class)
            ->add('num',IntegerType::class,array('attr'=>array('class'=>'form-control','placeholder'=>'Numéro du support','style'=>'width: 100%; border-radius: 0px; font-size: 16px;')))
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
           'data_class' => Supportchapitre::class
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'produit_produitbundle_supportchapitre';
    }
}

<?php

namespace App\Form\Produit\Produit;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use App\Entity\Produit\Produit\Pratiquechapitre;

class PratiquechapitreType extends AbstractType
{
     /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file',FileType::class)
            ->add('correctionpratique',CorrectionpratiqueType::class, array('required'=>'false'))
            ->add('num',IntegerType::class,array('attr'=>array('class'=>'form-control','placeholder'=>'Numéro du TP','style'=>'width: 100%; border-radius: 0px; font-size: 16px;')))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
           'data_class' => Pratiquechapitre::class
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'produit_produitbundle_pratiquechapitre';
    }
}

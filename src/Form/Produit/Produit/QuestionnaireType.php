<?php

namespace App\Form\Produit\Produit;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Entity\Produit\Produit\Questionnaire;

class QuestionnaireType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre',TextType::class,array('attr'=>array('class'=>'form-control','placeholder'=>'Rentrez une nouvelle question','style'=>'width: 100%; border-radius: 0px; font-size: 16px;')))
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
           'data_class' => Questionnaire::class
        ));
    }


    /**
     * @return string
     */
    public function getName()
    {
        return 'produit_produitbundle_questionnaire';
    }
}

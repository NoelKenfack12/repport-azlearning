<?php

namespace App\Form\Produit\Service;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Entity\Produit\Service\Recrutement;

class RecrutementType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username',TextType::class,array('attr'=>array('class'=>'mon-imput','placeholder'=>'Rentrez votre nom','style'=>'width: 100%; border-radius: 0px;')))
            ->add('villeactuel',TextType::class,array('attr'=>array('class'=>'mon-imput','placeholder'=>'Ville actuelle','style'=>'width: 100%; border-radius: 0px;')))
            ->add('mail',TextType::class,array('attr'=>array('class'=>'mon-imput','placeholder'=>'Votre adresse E-mail','style'=>'width: 100%; border-radius: 0px;')))
            ->add('tel',NumberType::class,array('attr'=>array('class'=>'mon-imput','placeholder'=>'Numéro de Téléphone','style'=>'width: 100%; border-radius: 0px;')))
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
           'data_class' => Recrutement::class
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'produit_servicebundle_recrutement';
    }
}

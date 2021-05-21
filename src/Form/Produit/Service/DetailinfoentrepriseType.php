<?php

namespace App\Form\Produit\Service;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use App\Entity\Produit\Service\Detailinfoentreprise;

class DetailinfoentrepriseType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre',TextType::class,array('attr'=>array('placeholder'=>'Titre de la publication','style'=>'width: 100%; border: none;'),'required'=>false))
            ->add('description',TextareaType::class,array('attr'=>array('placeholder'=>'Contenu de la publication','style'=>'width: 100%; border: none; height: 60px;'),'required'=>false))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
           'data_class' => Detailinfoentreprise::class
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'produit_servicebundle_detailinfoentreprise';
    }
}

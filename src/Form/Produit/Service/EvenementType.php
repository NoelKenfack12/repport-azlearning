<?php

namespace App\Form\Produit\Service;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use App\Entity\Produit\Service\Evenement;

class EvenementType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom',TextType::class,array('attr'=>array('class'=>'form-control','placeholder'=>'Titre de la partie','style'=>'width: 100%; border-radius: 0px;')))
            ->add('description',TextareaType::class,array('attr'=>array('class'=>'form-control tinymce-manager','placeholder'=>'Description de la partie','style'=>'width: 100%; border-radius: 0px;'),'required'=>false))
			->add('rang',IntegerType::class,array('attr'=>array('class'=>'form-control', 'placeholder'=>'Rang','style'=>'width: 100%; border-radius: 0px;')))
			->add('imgevenement',ImgevenementType::class,array('required'=>false))
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
           'data_class' => Evenement::class
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'produit_servicebundle_evenement';
    }
}

<?php

namespace App\Form\Produit\Service;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use App\Entity\Produit\Service\Intervention;

class InterventionType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('contenu',TextareaType::class,array('attr'=>array('placeholder'=>'Contenu du message','style'=>'width: 100%; height: 120px;','class'=>'form-control tinymce-manager'), 'required'=>false))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
           'data_class' => Intervention::class
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'produit_servicebundle_intervention';
    }
}

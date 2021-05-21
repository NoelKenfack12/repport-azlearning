<?php

namespace App\Form\Produit\Service;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Entity\Produit\Service\Messemail;

class MessemailType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre',TextType::class,array('attr'=>array('placeholder'=>'Titre','style'=>'width: 100%;','class'=>'form-control')))
            ->add('contenu',TextareaType::class,array('attr'=>array('placeholder'=>'Contenu','style'=>'width: 100%; height: 150px;','class'=>'form-control')))
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
           'data_class' => Messemail::class
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'produit_servicebundle_messemail';
    }
}

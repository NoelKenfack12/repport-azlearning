<?php

namespace App\Form\Produit\Service;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use App\Entity\Produit\Service\Message;

class MessageType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre',TextType::class,array('attr'=>array('class'=>'mon-imput','placeholder'=>'Titre du message','style'=>'width: 100%; border-radius: 0px;')))
            ->add('nom',TextType::class,array('attr'=>array('class'=>'mon-imput','placeholder'=>'Votre nom','style'=>'width: 100%; border-radius: 0px;')))
            ->add('contenu',TextareaType::class,array('attr'=>array('class'=>'mon-imput','placeholder'=>'Contenu du message','style'=>'width: 100%;border-radius: 0px; height: 100px;')))
            ->add('email',EmailType::class,array('attr'=>array('class'=>'mon-imput','placeholder'=>'Adresse E-mail','style'=>'width: 100%; border-radius: 0px;')))
            ->add('tel',NumberType::class,array('attr'=>array('class'=>'mon-imput','placeholder'=>'Numéro de Téléphone','style'=>'width: 100%; border-radius: 0px;'),'required'=>false))
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
           'data_class' => Message::class
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'produit_servicebundle_message';
    }
}

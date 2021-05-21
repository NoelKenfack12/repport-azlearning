<?php

namespace App\Form\Produit\Service;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use App\Entity\Produit\Service\Commentaireblog;

class CommentaireblogType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre',TextType::class,array('attr'=>array('placeholder'=>'Titre du sujet','style'=>'width: 100%; border-radius: 0px;','class'=>'form-control')))
            ->add('contenu',TextareaType::class,array('attr'=>array('placeholder'=>'Contenu du message','style'=>'width: 100%; height: 120px;','class'=>'form-control tinymce-manager'), 'required'=>false))
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
           'data_class' => Commentaireblog::class
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'produit_servicebundle_commentaireblog';
    }
}

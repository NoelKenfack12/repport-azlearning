<?php

namespace App\Form\Users\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use App\Entity\Users\User\Imgslide;

class ImgslideType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file',FileType::class,array('attr'=>array('style'=>'opacity: 0.5; width: 100%;')))
            ->add('titre',TextType::class,array('attr'=>array('placeholder'=>'Titre du slide','style'=>'width: 100%;'),'required'=>false))
            ->add('rang',IntegerType::class,array('attr'=>array('placeholder'=>'Rang','style'=>'width: 100%;')))
            ->add('description',TextType::class,array('attr'=>array('placeholder'=>'Contenu du slide','style'=>'width: 100%;'),'required'=>false))
            ->add('link',UrlType::class,array('attr'=>array('placeholder'=>'Lien vers une page web','style'=>'width: 100%;'),'required'=>false))
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
           'data_class' => Imgslide::class
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'users_userbundle_imgslide';
    }
}

<?php

namespace App\Form\Users\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use App\Entity\Users\User\User;

class UserType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom',TextType::class,array('attr'=>array('placeholder'=>'Entrez votre nom')))
            ->add('prenom',TextType::class,array('attr'=>array('placeholder'=>'Entrez votre prenom')))
            ->add('username',TextType::class,array('attr'=>array('placeholder'=>'Votre E-mail')))
            ->add('password',PasswordType::class,array('attr'=>array('placeholder'=>'Entrez votre mot de passe')))
            ->add('tel',TextType::class,array('attr'=>array('class'=>'number','placeholder'=>'Votre Téléphone'),'required'=>false))
        ;
    }
 
    public function configureOptions(OptionsResolver $resolver)
  	{
  		$resolver->setDefaults(array(
  		   'data_class' => User::class
  		));
  	}

    /**
     * @return string
     */
    public function getName()
    {
        return 'users_userbundle_user';
    }
}

<?php

namespace App\Form\Users\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use App\Entity\Users\User\Imgcouverture;

class ImgcouvertureType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file',FileType::class,array('attr'=>array('style'=>'opacity: 0.5;width: 100%;')))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
           'data_class' => Imgcouverture::class
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'users_userbundle_imgcouverture';
    }
}

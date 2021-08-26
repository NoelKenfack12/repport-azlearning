<?php

namespace App\Form\Produit\Service;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use App\Entity\Produit\Service\Continent;

class ContinentType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom',texttype::class,array('attr'=>array('style'=>'width: 100%;')))
            ->add('siteweb',texttype::class,array('attr'=>array('style'=>'width: 100%;'),'required'=>false))
            ->add('citoyen',texttype::class,array('attr'=>array('style'=>'width: 100%;')))
            ->add('citoyenne',texttype::class,array('attr'=>array('style'=>'width: 100%;')))
            ->add('file',FileType::class,array('required'=>false))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Continent::class
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'produit_servicebundle_continent';
    }
}
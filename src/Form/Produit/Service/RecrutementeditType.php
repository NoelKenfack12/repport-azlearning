<?php

namespace App\Form\Produit\Service;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class RecrutementeditType extends RecrutementType
{
     /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
		$builder->add('file',FileType::class);
        $builder->add('message',TextareaType::class,array('attr'=>array('placeholder'=>'Ajouter un message','style'=>'width: 100%;')));
        $builder->add('yourcv',YourcvType::class);
    }


    /**
     * @return string
     */
    public function getName()
    {
        return 'produit_servicebundle_recrutementedit';
    }
}

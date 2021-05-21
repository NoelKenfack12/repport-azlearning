<?php

namespace App\Form\Produit\Service;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormationeditType extends ServiceType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
		$builder->add('imgservice',ImgserviceType::class,array('required'=>false));
        $builder->add('imgservicesecond',ImgservicesecondType::class,array('required'=>false));
    }


    /**
     * @return string
     */
    public function getName()
    {
        return 'produit_servicebundle_formationedit';
    }
}

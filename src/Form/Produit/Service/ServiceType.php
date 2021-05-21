<?php

namespace App\Form\Produit\Service;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Produit\Service\Service;

class ServiceType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom',TextType::class,array('attr'=>array('class'=>'form-control','placeholder'=>'Intitulé de l\'offre de formation','style'=>'width: 100%; border-radius: 0px;'),'required'=>false))
			->add('description',TextareaType::class,array('attr'=>array('class'=>'form-control tinymce-manager','placeholder'=>'Description de l\'offre de formation','style'=>'width: 100%'),'required'=>false))
			->add('detail',TextareaType::class,array('attr'=>array('class'=>'form-control tinymce-manager','placeholder'=>'Détail sur cette offre de formation','style'=>'width: 100%'),'required'=>false))
            ->add('pubinline',TextareaType::class,array('attr'=>array('class'=>'form-control','placeholder'=>'Message pub formation en ligne','style'=>'width: 100%; border-radius: 0px;'),'required'=>false))
            ->add('puboffline',TextareaType::class,array('attr'=>array('class'=>'form-control','placeholder'=>'Message pub formation au centre','style'=>'width: 100%; border-radius: 0px;'),'required'=>false))
			->add('keyword',TextType::class,array('attr'=>array('class'=>'form-control','placeholder'=>'Les mots clés rapportant à la formation','style'=>'width: 100%; border-radius: 0px;'),'required'=>false))
            ->add('rang',IntegerType::class,array('attr'=>array('class'=>'form-control','placeholder'=>'Numéro de formation','style'=>'width: 100%; border-radius: 0px;')))
            ->add('nprix',IntegerType::class,array('attr'=>array('class'=>'form-control','placeholder'=>'Prix de la formation en ligne','style'=>'width: 100%; border-radius: 0px;')))
            ->add('nprixoff',IntegerType::class,array('attr'=>array('class'=>'form-control','placeholder'=>'Prix de la formation au centre','style'=>'width: 100%; border-radius: 0px;')))
            ->add('recrutement',CheckboxType::class,array('required'=>false))
            ->add('imgservice',ImgserviceType::class)
            ->add('imgservicesecond',ImgservicesecondType::class)
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
           'data_class' => Service::class
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'produit_servicebundle_service';
    }
}

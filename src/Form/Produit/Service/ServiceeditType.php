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
use App\Entity\Produit\Produit\Souscategorie;
use Doctrine\ORM\EntityRepository;

class ServiceeditType extends ServiceType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
		$builder->remove('detail');
		$builder->remove('pubinline');
		$builder->remove('puboffline');
		$builder->remove('nprix');
		$builder->remove('nprixoff');
		$builder->remove('recrutement');
		$builder->remove('imgservicesecond');
		$builder->add('nom',TextType::class,array('attr'=>array('class'=>'form-control','placeholder'=>'Titre du cours','style'=>'width: 100%; border-radius: 0px; font-size: 16px;'),'required'=>true));
		$builder->add('description',TextareaType::class,array('attr'=>array('class'=>'form-control tinymce-manager','placeholder'=>'Présentation  votre cours','style'=>'width: 100%'),'required'=>true));
		$builder->add('souscategorie',EntityType::class, array(
			'class'=>Souscategorie::class,
			'choice_label'=>'nom',
			'attr'=>array('class'=>'form-control', 'style'=>'width: 100%; border-radius: 0px; font-size: 16px;'),
			'query_builder' => function(EntityRepository $d){
                      return $d->getSelectList();
					  }
					  ));
    }


    /**
     * @return string
     */
    public function getName()
    {
        return 'produit_servicebundle_serviceedit';
    }
}

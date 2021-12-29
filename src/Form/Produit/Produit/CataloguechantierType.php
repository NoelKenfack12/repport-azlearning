<?php

namespace App\Form\Produit\Produit;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use App\Entity\Produit\Produit\Cataloguechantier;

class CataloguechantierType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom',TextType::class)
            ->add('description',TextareaType::class,array('attr'=>array('class'=>'materialize-textarea')))
            ->add('rang',IntegerType::class)
            ->add('file',FileType::class,array('attr'=>array('style'=>'opacity: 0;'),'required'=>false))
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver)
  	{
  		$resolver->setDefaults(array(
  		   'data_class' => Cataloguechantier::class
  		));
  	}

    /**
     * @return string
     */
    public function getName()
    {
        return 'produit_produitbundle_cataloguechantier';
    }
}

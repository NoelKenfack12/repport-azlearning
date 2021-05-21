<?php

namespace App\Form\Produit\Produit;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use App\Entity\Produit\Produit\Compospratique;

class CompospratiqueType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file',FileType::class)
        ;
    }
    

    public function configureOptions(OptionsResolver $resolver)
  	{
  		$resolver->setDefaults(array(
  		   'data_class' => Compospratique::class
  		));
  	}

    /**
     * @return string
     */
    public function getName()
    {
        return 'produit_produitbundle_compospratique';
    }
}

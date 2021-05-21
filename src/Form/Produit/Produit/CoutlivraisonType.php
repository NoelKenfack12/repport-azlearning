<?php

namespace App\Form\Produit\Produit;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\entity\Produit\Service\Ville;
use App\Entity\Produit\Produit\Coutlivraison;

class CoutlivraisonType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('montant',TextType::class,array('attr'=>array('placeholder'=>'Montant de la livraison','style'=>'width: 100%;')))
            ->add('ville',EntityType::class, array(
			'class'=>Ville::class,
			'choice_label'=>'nom',
			'attr'=>array('style'=>'width: 100%')
			))
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
           'data_class' => Coutlivraison::class
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'produit_produitbundle_coutlivraison';
    }
}

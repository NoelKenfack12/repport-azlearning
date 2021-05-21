<?php

namespace App\Form\Users\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use App\Entity\Users\User\Newsletter;

class NewsletterType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom',TextType::class,array('attr'=>array('class'=>'input-lg')))
            ->add('email',TextType::class,array('attr'=>array('class'=>'input-lg')))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
  	{
  		$resolver->setDefaults(array(
  		   'data_class' => Newsletter::class
  		));
  	}

    /**
     * @return string
     */
    public function getName()
    {
        return 'users_userbundle_newsletter';
    }

}

<?php

namespace App\Form;

use App\Entity\AdminEsprit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminEspritType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
           
            ->add('password')
            ->add('idEsprit')
            ->add('nom')
            ->add('prenom')
            ->add('cin')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AdminEsprit::class,
        ]);
    }
}

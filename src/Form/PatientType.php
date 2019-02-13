<?php

namespace App\Form;

use App\Entity\Patient;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PatientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('prenom')
            ->add('date')
            ->add('motif')
            ->add('etp')
            ->add('sexe')
            ->add('dentree')
            ->add('tel1')
            ->add('tel2')
            ->add('distance')
            ->add('etude')
            ->add('profession')
            ->add('activite')
            ->add('diagnostic')
            ->add('dedate')
            ->add('orientation')
            ->add('etpdecision')
            ->add('precisions')
            ->add('progetp')
            ->add('precisionsperso')
            ->add('observ')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Patient::class,
        ]);
    }
}

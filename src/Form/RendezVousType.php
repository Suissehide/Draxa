<?php

namespace App\Form;

use App\Entity\RendezVous;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RendezVousType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', ChoiceType::class, array(
                'label' => 'Nom',
                'choices' => array(
                    '' => '',
                    'BCVs' => 'BCVs',
                    'Stress/Alerte' => 'Stress/Alerte',
                    'M+3' => 'M+3',
                    'Equilibre' => 'Equilibre',
                    'Communication' => 'Communication',
                    'M+12' => 'M+12',
                    'Renf1' => 'Renf1',
                    'Renf2' => 'Renf2',
                ),
            ))
            ->add('date', DateType::class, array(
                'label' => 'Date prévue',
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'attr' => [
                    'class' => 'datepicker',
                ],
            ))
            ->add('type', ChoiceType::class, array(
                'label' => 'Type',
                'choices' => array(
                    '' => '',
                    'Ambu' => 'Ambu',
                    'Tel' => 'Tel',
                    'Hospi' => 'Hospi',
                ),
                'required'   => false,
            ))
            ->add('accompagnant', ChoiceType::class, array(
                'label' => 'Accompagnant',
                'choices' => array(
                    '' => '',
                    'Oui' => 'oui',
                    'Non' => 'non',
                ),
                'required'   => false,
            ))
            ->add('permission', ChoiceType::class, array(
                'label' => 'Permission demandée',
                'choices' => array(
                    '' => '',
                    'Oui' => 'oui',
                    'Non' => 'non',
                ),
                'required'   => false,
            ))
            ->add('choix', ChoiceType::class, array(
                'label' => 'Est-il/elle venu ?',
                'choices' => array(
                    '' => '',
                    'Oui' => 'oui',
                    'Non' => 'non',
                ),
                'required'   => false,
            ))
            ->add('motifRefus', TextType::class, array(
                'label' => 'Motif de refus',
                'required'   => false,
            ))
            ->add('date_repro', DateType::class, array(
                'label' => 'Date repro prévue',
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'attr' => [
                    'class' => 'datepicker',
                ],
                'required'   => false,
            ))
            ->add('type_repro', ChoiceType::class, array(
                'label' => 'Type',
                'choices' => array(
                    '' => '',
                    'Ambu' => 'Ambu',
                    'Tel' => 'Tel',
                    'Hospi' => 'Hospi',
                ),
                'required'   => false,
            ))
            ->add('accompagnant_repro', ChoiceType::class, array(
                'label' => 'Accompagnant',
                'choices' => array(
                    '' => '',
                    'Oui' => 'oui',
                    'Non' => 'non',
                ),
                'required'   => false,
            ))
            ->add('permission_repro', ChoiceType::class, array(
                'label' => 'Permission demandée',
                'choices' => array(
                    '' => '',
                    'Oui' => 'oui',
                    'Non' => 'non',
                ),
                'required'   => false,
            ))
            ->add('choix_repro', ChoiceType::class, array(
                'label' => 'Est-il/elle venu ?',
                'choices' => array(
                    '' => '',
                    'Oui' => 'oui',
                    'Non' => 'non',
                ),
                'required'   => false,
            ))
            ->add('motifRefus_repro', TextType::class, array(
                'label' => 'Motif de refus',
                'required'   => false,
            ))
            // ->add('patient',  EntityType::class, array(
            //     'class' => 'App\Entity\Patient',
            //     'choice_label' => 'id',
            //     'multiple' => false,
            //     ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => RendezVous::class,
        ]);
    }
}

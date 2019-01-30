<?php

namespace App\Form;

use App\Entity\Entretien;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EntretienType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
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
                    'Diet' => 'Diet',
                    'Renf1' => 'Renf1',
                    'Renf2' => 'Renf2',
                    'Reactu DE' => 'Reactu DE',
                    'Tabac' => 'Tabac',
                    'Psy' => 'Psy',
                    'Diabète' => 'Diabete',
                    'HTA' => 'HTA',
                    'Autre' => 'Autre',
                ),
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
            ->add('motifRefus', TextType::class, array(
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
            'data_class' => Entretien::class,
        ]);
    }
}

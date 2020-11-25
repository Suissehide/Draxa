<?php

namespace App\Form;

use App\Entity\RendezVous;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\CallbackTransformer;

class RendezVousType extends AbstractType
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
            ->add('thematique', ChoiceType::class, array(
                'label' => 'Thématique',
                'placeholder' => '',
                'choices' => array(
                    'Consultation psychologue' => 'Consultation psychologue',
                    'Consultation diététicienne' => 'Consultation diététicienne',
                    'Consultation pharmacienne' => 'Consultation pharmacienne',
                ),
            ))
            ->add('heure', ChoiceType::class, array(
                'label' => 'Heure',
                'placeholder' => '',
                'choices' => array(
                    '09:00' => '09:00',
                    '09:30' => '09:30',
                    '10:00' => '10:00',
                    '10:30' => '10:30',
                    '11:00' => '11:00',
                    '11:30' => '11:30',
                    '12:00' => '12:00',
                    '13:00' => '13:00',
                    '13:30' => '13:30',
                    '14:00' => '14:00',
                    '14:30' => '14:30',
                    '15:00' => '15:00',
                    '15:30' => '15:30',
                    '16:00' => '16:00',
                ),
                'required' => false,
            ))
            ->add('type', ChoiceType::class, array(
                'label' => 'Type',
                'placeholder' => '',
                'choices' => array(
                    'Ambu' => 'Ambu',
                    'Tel' => 'Tel',
                    'Hospi' => 'Hospi',
                ),
                'required' => false,
            ))
            ->add('accompagnant', ChoiceType::class, array(
                'label' => 'Accompagnant',
                'placeholder' => '',
                'choices' => array(
                    'Oui' => 'oui',
                    'Non' => 'non',
                ),
                'required' => false,
            ))
            ->add('etat', ChoiceType::class, array(
                'label' => 'A-t-il eu lieu ?',
                'choices' => array(
                    '' => '',
                    'Oui' => 'Oui',
                    'Non' => 'Non',
                ),
                'choice_attr' => [
                    '' => ['class' => 'white'],
                    'Oui' => ['class' => 'hotpink'],
                    'Non' => ['class' => 'gold'],
                ],
                'required' => false,
            ))
            ->add('motifRefus', TextType::class, array(
                'label' => 'Motif de refus',
                'required' => false,
            ))
            // ->add('patient',  EntityType::class, array(
            //     'class' => 'App\Entity\Patient',
            //     'choice_label' => 'id',
            //     'multiple' => false,
            //     ))
        ;

        $builder->get('heure')
            ->addModelTransformer(new CallbackTransformer(
                function ($tagsAsString) {
                    if ($tagsAsString != null)
                        return $tagsAsString->format('H:i');
                },
                function ($tagsAsDate) {
                    if ($tagsAsDate != null) {
                        $t = explode(':', $tagsAsDate);
                        $time = new \DateTime();
                        return $time->setTime($t[0], $t[1]);
                    }
                }
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => RendezVous::class,
        ]);
    }
}

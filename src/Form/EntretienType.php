<?php

namespace App\Form;

use App\Entity\Entretien;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\CallbackTransformer;

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
            ->add('thematique', ChoiceType::class, array(
                'label' => 'Thématique',
                'placeholder' => '',
                'choices' => array(
                    'Athérosclérose' => 'Athérosclérose',
                    'Diabète' => 'Diabète',
                    'Signes alerte et CAT' => 'Signes alerte et CAT',
                    'Réactu 1' => 'Réactu 1',
                    'Réactu 2' => 'Réactu 2',
                    'Réactu 3' => 'Réactu 3',
                    'Réactu 4' => 'Réactu 4',
                    'Réactu 5' => 'Réactu 5',
                    'Réactu 6' => 'Réactu 6',
                    'Tabac' => 'Tabac',
                    'Activité physique' => 'Activité physique',
                    'Navigation' => 'Navigation',
                    'HTA' => 'HTA',
                    'Motivation tabac' => 'Motivation tabac',
                    'Motivation AP' => 'Motivation AP',
                    'Motivation FDR' => 'Motivation FDR',
                    'Motivation navigation' => 'Motivation navigation',
                ),
            ))
            ->add('heure', ChoiceType::class, array(
                'label' => 'Heure',
                'placeholder' => '',
                'choices' => array(
                    '09:15' => '09:15',
                    '10:30' => '10:30',
                    '11:45' => '11:45',
                    '13:30' => '13:30',
                    '14:45' => '14:45',
                ),
                'required' => false,
            ))
            ->add('type', ChoiceType::class, array(
                'label' => 'Type',
                'placeholder' => '',
                'choices' => array(
                    'Ambu' => 'Ambu',
                    'Tel' => 'Tel',
                    'Hospit' => 'Hospit',
                ),
                'required' => false,
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
                'required'   => false,
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
            'data_class' => Entretien::class,
        ]);
    }
}

<?php

namespace App\Form;

use App\Entity\Atelier;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\CallbackTransformer;

class AtelierType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date', DateType::class, array(
                'label' => 'Date prévue',
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'input' => 'datetime',
                'attr' => [
                    'class' => 'datepicker',
                ],
            ))
            ->add('thematique', ChoiceType::class, array(
                'label' => 'Thématique',
                'placeholder' => '',
                'choices' => array(
                    'Signes d\'alerte' => 'Signes d\'alerte',
                    'Gestion du stress' => 'Gestion du stress',
                    'Cuisine Poisson' => 'Cuisine Poisson',
                    'Cuisine MG' => 'Cuisine MG',
                    'Cuisine F&L' => 'Cuisine F&L',
                    'Gestion des sucres' => 'Gestion des sucres',
                    'Gestion des quantités' => 'Gestion des quantités',
                    'Freins/solutions équilibre' => 'Freins/solutions équilibre',
                    'Stress aigu' => 'Stress aigu',
                    'Prévention stress' => 'Prévention stress',
                    'Communication' => 'Communication',
                    'Marche co 1' => 'Marche co 1',
                    'Marche co 2' => 'Marche co 2',
                ),
                'choice_attr' => [
                    'Signes d\'alerte' => ['data-hour' => '15:00'],
                    'Gestion du stress' => ['data-hour' => '13:30'],
                    'Cuisine Poisson' => ['data-hour' => '10:00'],
                    'Cuisine MG' => ['data-hour' => '10:00'],
                    'Cuisine F&L' => ['data-hour' => '10:00'],
                    'Gestion des sucres' => ['data-hour' => '14:00'],
                    'Gestion des quantités' => ['data-hour' => '12:00'],
                    'Freins/solutions équilibre' => ['data-hour' => '14:00'],
                    'Stress aigu' => ['data-hour' => '14:00'],
                    'Prévention stress' => ['data-hour' => '14:00'],
                    'Communication' => ['data-hour' => '14:00'],
                    'Marche co 1' => ['data-hour' => '14:00'],
                    'Marche co 2' => ['data-hour' => '14:00'],
                ],
                'attr' => [
                    'class' => 'get-hour',
                ],
                'required' => false,
            ))
            ->add('heure', ChoiceType::class, array(
                'label' => 'Heure',
                'placeholder' => '',
                'choices' => array(
                    '10:00' => '10:00',
                    '12:00' => '12:00',
                    '13:30' => '13:30',
                    '14:00' => '14:00',
                    '15:00' => '15:00',
                ),
                'attr' => [
                        'class' => 'set-hour',
                ],
                'required' => false,
            ))
            // ->add('heure', TimeType::class, array(
            //     'label' => 'Heure',
            //     'widget' => 'choice',
            //     'attr' => [
            //         'class' => 'set-hour',
            //     ],
            //     'minutes' => array(
            //         0,15,30,45
            //     ),
            //     'hours' => array(
            //         9,10,11,12,13,14,15,16
            //     ),
            // ))
            ->add('type', ChoiceType::class, array(
                'label' => 'Type',
                'placeholder' => '',
                'choices' => array(
                    'Ambu' => 'Ambu',
                    'Hospit' => 'Hospit',
                ),
                'required' => false,
            ))
            ->add('etat', ChoiceType::class, array(
                'label' => 'Est-il/elle venu(e) ?',
                'choices' => array(
                    '' => '',
                    'Oui' => 'Oui',
                    'Non' => 'Non',
                ),
                'choice_attr' => [
                    '' => ['class' => 'white'],
                    'Oui' => ['class' => 'hotpink'],
                    'Non' => ['class' => 'yellow'],
                ],
                'required' => false,
            ))
            ->add('accompagnant', ChoiceType::class, array(
                'label' => 'Accompagnant',
                'choices' => array(
                    '' => '',
                    'Oui' => 'oui',
                    'Non' => 'non',
                ),
                'required' => false,
            ))
            ->add('motifRefus', TextType::class, array(
                'label' => 'Motif de refus',
                'required' => false,
            ))
            // ->add('patient')
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
            'data_class' => Atelier::class,
        ]);
    }
}

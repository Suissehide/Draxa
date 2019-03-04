<?php

namespace App\Form;

use App\Entity\Telephonique;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TelephoniqueType extends AbstractType
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
                    'Motivationnel' => 'Motivationnel',
                    'Coaching tabac' => 'Coaching tabac',
                    'Coaching AOMI' => 'Coaching AOMI',
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
                    'Non' => ['class' => 'yellow'],
                ],
                'required' => false,
            ))
            ->add('motifRefus', TextType::class, array(
                'label' => 'Motif de non réponse',
                'required' => false,
            ))
            // ->add('patient')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Telephonique::class,
        ]);
    }
}

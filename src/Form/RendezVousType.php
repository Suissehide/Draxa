<?php

namespace App\Form;

use App\Entity\RendezVous;

use DateTime;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextAreaType;

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
                'input' => 'datetime',
                'html5' => false
            ))
            ->add('heure', ChoiceType::class, array(
                'label' => 'Heure',
                'placeholder' => '',
                'choices' => array(),
                'attr' => [
                    'class' => 'heure',
                ],
                'required' => false,
            ))
            ->add('accompagnant', ChoiceType::class, array(
                'label' => 'Accompagnant',
                'placeholder' => '',
                'choices' => array(
                    'Oui' => 'Oui',
                    'Non' => 'Non',
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
            ->add('notes', TextAreaType::class, array(
                'label' => 'Notes de transmission',
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
                        $time = new DateTime();
                        return $time->setTime($t[0], $t[1]);
                    }
                }
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RendezVous::class,
        ]);
    }
}

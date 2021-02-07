<?php

namespace App\Form;

use App\Form\SlotType;

use App\Entity\Semaine;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class SemaineType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dateDebut', DateType::class, array(
                'label' => 'Date début',
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'attr' => [
                    'autocomplete' => 'off',
                    'placeholder' => "dd/mm/yyyy"
                ],
                'html5' => false
            ))
            ->add('dateFin', DateType::class, array(
                'label' => 'Date fin',
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'attr' => [
                    'disabled' => true,
                    'placeholder' => "dd/mm/yyyy"
                ],
                'html5' => false
            ))
            ->add('slots', CollectionType::class, [
                'entry_type' => SlotType::class,
                'entry_options' => ['label' => false],
            ]);

            
            // ->add('slots', CollectionType::class, array(
            //     'entry_type' => SlotType::class,
            //     'entry_options' => array('label' => false),
            //     'allow_add' => true,
            //     'allow_delete' => true,
            //     'by_reference' => false,
            // ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Semaine::class,
        ]);
    }
}

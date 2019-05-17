<?php

namespace App\Form;

use App\Entity\Infos;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InfosType extends AbstractType
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
                'label' => 'Type de support',
                'placeholder' => '',
                'choices' => array(
                    'Tablette' => 'Tablette',
                    'Smartphone' => 'Smartphone',
                    'Ordinateur' => 'Ordinateur',
                    'Tablette + Ordinateur' => 'Tablette + Ordinateur',
                    'Tablette + Smartphone' => 'Tablette + Smartphone',
                    'Smartphone + Ordinateur' => 'Smartphone + Ordinateur',
                    'Tablette + Smartphone + Ordinateur' => 'Tablette + Smartphone + Ordinateur',
                ),
                'required' => false,
            ))
            ->add('moment', ChoiceType::class, array(
                'label' => 'Moment du parcours',
                'placeholder' => '',
                'choices' => array(
                    'Phase aïgue' => 'Phase aïgue',
                    'Consultation ambulatoire' => 'Consultation ambulatoire',
                    'Au cours d\'une hospitalisation' => 'Au cours d\'une hospitalisation',
                    'BCVi' => 'BCVi',
                    'BCVs' => 'BCVs',
                    'Atelier éducatif' => 'Atelier éducatif',
                    'M3' => 'M3',
                    'M12' => 'M12',
                    'Renf1' => 'Renf1',
                    'Renf2' => 'Renf2',
                ),
                'required' => false,
            ))
            ->add('parametrage', ChoiceType::class, array(
                'label' => 'Paramétrage',
                'placeholder' => '',
                'choices' => array(
                    'Profil santé' => 'Profil santé',
                    'Profil santé + Programme objectifs' => 'Profil santé + Programme objectifs',
                    'Profil santé + Programme objectifs + Télésurveillance' => 'Profil santé + Programme + Télésurveillance',
                ),
                'required' => false,
            ))
            ->add('code', TextType::class, array(
                'label' => 'Codes d\'accès dossier patient',
                'required' => false,
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Infos::class,
        ]);
    }
}

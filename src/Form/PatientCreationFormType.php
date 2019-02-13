<?php

namespace App\Form;

use App\Entity\Patient;
use App\Form\AnnexeType;
use App\Form\AtelierType;
use App\Form\EntretienType;
use App\Form\TelephoniqueType;
use App\Form\RendezVousType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Serializer\Mapping\ClassMetadata;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PatientCreationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('observ', TextareaType::class, array('label' => 'Observations diverses'))
            ->add('motif', TextType::class, array('label' => 'Motif d\'arrêt de programme'))
            ->add('etp', TextType::class, array('label' => 'Point final parcours ETP'))
            ->add('date', DateType::class, array(
                'label' => 'Date de naissance',
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'attr' => [
                    'class' => 'datepicker',
                ],
            ))
            ->add('nom', TextType::class, array('label' => 'Nom'))
            ->add('prenom', TextType::class, array('label' => 'Prénom'))
            ->add('dentree', DateType::class, array(
                'label' => 'Date de sortie  ',
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'attr' => [
                    'class' => 'datepicker',
                ],
            ))
            ->add('tel1', TextType::class, array('label' => 'Téléphone 1'))
            ->add('tel2', TextType::class, array('label' => 'Téléphone 2'))
            ->add('sexe', ChoiceType::class, array(
                'label' => 'Sexe',
                'choices' => array(
                    '' => '',
                    'Mr' => 'homme',
                    'Mme' => 'femme',
                ),
            ))
            ->add('distance', ChoiceType::class, array(
                'label' => 'Distance d\'habitation',
                'choices' => array(
                    '' => '',
                    'Hors Gironde' => 'Hors Gironde',
                    'CUB' => 'CUB',
                    'Gironde' => 'Gironde',
                ),
            ))
            ->add('etude', ChoiceType::class, array(
                'label' => 'Niveau d\'étude',
                'choices' => array(
                    '' => '',
                    'Primaire' => 'primaire',
                    'Secondaire' => 'secondaire',
                    'Universitaire' => 'universitaire',
                ),
            ))
            ->add('profession', ChoiceType::class, array(
                'label' => 'Profession',
                'choices' => array(
                    '' => '',
                    'Artisans, commerçants et chefs d\'entreprises' => 'Artisans, commerçants et chefs d\'entreprises',
                    'Cadres et professions intellectuelles supérieures' => 'Cadres et professions intellectuelles supérieures',
                    'Professions intermédiaires' => 'Professions intermédiaires',
                    'Employés' => 'Employés',
                    'Ouvriers' => 'Ouvriers',
                    'Agriculteur' => 'Agriculteur',
                    'Mère au foyer' => 'Mère au foyer',
                ),
            ))
            ->add('activite', ChoiceType::class, array(
                'label' => 'Activité actuelle',
                'choices' => array(
                    '' => '',
                    'Actif' => 'actif',
                    'Retraité' => 'retraité',
                    'RMI/RSA' => 'RMI/RSA',
                    'Sans emploi' => 'sans emploi',
                    'Chômage' => 'chômage',
                    'Arrêt maladie' => 'arrêt maladie',
                    'Invalidité' => 'invalidité',
                ),
            ))
            ->add('diagnostic', ChoiceType::class, array(
                'label' => 'Diagnostic médical',
                'choices' => array(
                    '' => '',
                    'AOMI' => 'AOMI',
                    'AVC' => 'AVC',
                    'CORONAROPATHIE' => 'CORONAROPATHIE',
                    'PREVENTION' => 'PREVENTION',
                ),
            ))
            ->add('dedate', DateType::class, array(
                'label' => 'Date d\'entrée',
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'attr' => [
                    'class' => 'datepicker',
                ],
            ))
            ->add('orientation', TextType::class, array('label' => 'Orientation'))
            ->add('etpdecision', ChoiceType::class, array(
                'label' => 'ETP Décision',
                'choices' => array(
                    '' => '',
                    'Oui' => 'oui',
                    'Non' => 'non',
                ),
            ))
            ->add('precisions', TextType::class, array('label' => 'Précision non inclusion'))
            ->add('progetp', TextType::class, array(
                'label' => 'Type de programme',
                // 'choices' => array(
                //     '' => '',
                //     'HRCV' => 'HRCV',
                //     'HRCV+AOMI' => 'HRCV+AOMI',
                //     'HRCV+AOD' => 'HRCV+AOD',
                //     'HRCV+AOMI+AOD' => 'HRCV+AOMI+AOD',
                //     'Perso+HRCV' => 'Perso+HRCV',
                //     'Perso+AOMI' => 'Perso+AOMI',
                //     'Personnalisé' => 'Personnalisé',
                // ),
            ))
            ->add('precisionsperso', TextareaType::class, array('label' => 'Précision contenu personnalisé'))

            ->add('rendezVous', CollectionType::class, array(
                'entry_type' => RendezVousType::class,
                'entry_options' => array('label' => false),
                'allow_add' => true,
                'by_reference' => false,
            ))
            ->add('entretiens', CollectionType::class, array(
                'entry_type' => EntretienType::class,
                'entry_options' => array('label' => false),
                'allow_add' => true,
                'by_reference' => false,
            ))
            ->add('ateliers', CollectionType::class, array(
                'entry_type' => AtelierType::class,
                'entry_options' => array('label' => false),
                'allow_add' => true,
                'by_reference' => false,
            ))
            ->add('telephoniques', CollectionType::class, array(
                'entry_type' => TelephoniqueType::class,
                'entry_options' => array('label' => false),
                'allow_add' => true,
                'by_reference' => false,
            ))
            ->add('annexes', CollectionType::class, array(
                'entry_type' => AnnexeType::class,
                'entry_options' => array('label' => false),
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ))
            ->add('validation', SubmitType::class, array('label' => 'Enregistrer'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Patient::class,
        ]);
    }
}

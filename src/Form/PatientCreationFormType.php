<?php

namespace App\Form;

use App\Entity\Patient;
use App\Form\AnnexeType;
use App\Form\AtelierType;
use App\Form\EntretienType;
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
            ->add('soignantReferent', TextareaType::class, array(
                'label' => 'Soignant référent',
                'required' => false
            ))
            ->add('observ', TextareaType::class, array(
                'label' => 'Suivi à régulariser',
                'required' => false
            ))
            ->add('divers', TextareaType::class, array(
                'label' => 'Theraflow',
                'required' => false
            ))
            ->add('notes', TextareaType::class, array(
                'label' => 'Notes',
                'required' => false
            ))
            ->add('date', DateType::class, array(
                'label' => 'Date de naissance',
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'attr' => [
                    'autocomplete' => 'off',
                    'class' => 'datepicker',
                ],
                'html5' => false
            ))
            ->add('nom', TextType::class, array('label' => 'Nom'))
            ->add('prenom', TextType::class, array('label' => 'Prénom'))

            ->add('tel1', TextType::class, array('label' => 'Téléphone 1'))
            ->add('tel2', TextType::class, array(
                'label' => 'Téléphone 2',
                'required' => false
            ))
            ->add('sexe', ChoiceType::class, array(
                'label' => 'Sexe',
                'choices' => array(
                    '' => '',
                    'Mr' => 'homme',
                    'Mme' => 'femme',
                )
            ))
            ->add('distance', ChoiceType::class, array(
                'label' => 'Distance d\'habitation',
                'choices' => array(
                    '' => '',
                    'Hors Gironde' => 'Hors Gironde',
                    'CUB' => 'CUB',
                    'Gironde' => 'Gironde',
                ),
                'required' => false
            ))
            ->add('etude', ChoiceType::class, array(
                'label' => 'Niveau d\'étude',
                'choices' => array(
                    '' => '',
                    'Primaire' => 'primaire',
                    'Secondaire' => 'secondaire',
                    'Universitaire' => 'universitaire',
                ),
                'required' => false
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
                    'Autre' => 'Autre'
                ),
                'required' => false
            ))
            ->add('activite', ChoiceType::class, array(
                'label' => 'Activité actuelle',
                'choices' => array(
                    '' => '',
                    'Actif' => 'Actif',
                    'Retraité' => 'Retraité',
                    'RMI/RSA' => 'RMI/RSA',
                    'Sans emploi' => 'Sans emploi',
                    'Chômage' => 'Chômage',
                    'Arrêt maladie' => 'Arrêt maladie',
                    'Invalidité' => 'Invalidité',
                    'Autre' => 'Autre',
                ),
                'required' => false
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
                'required' => false
            ))
            ->add('dedate', DateType::class, array(
                'label' => 'Date d\'entrée',
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'attr' => [
                    'autocomplete' => 'off',
                    'class' => 'datepicker',
                ],
                'required' => false,
                'html5' => false
            ))
            ->add('mode', ChoiceType::class, array(
                'label' => 'Mode de prise en charge',
                'choices' => array(
                    '' => '',
                    'Ambu' => 'Ambu',
                    'HDS' => 'HDS',
                    'HDJ' => 'HDJ',
                    'Hospit' => 'Hospit'
                ),
                'required' => false
            ))
            ->add('orientation', ChoiceType::class, array(
                'label' => 'Orientation',
                'choices' => array(
                    '' => '',
                    'Venue spontanée' => 'Venue spontanée',
                    'Orientation pro santé ext hôpital' => 'Orientation pro santé ext hôpital',
                    'Orientation pro santé au cours hospit' => 'Orientation pro santé au cours hospit',
                    'Orientation pro santé en Cs' => 'Orientation pro santé en Cs',
                    'NS' => 'NS',
                ),
                'required' => false
            ))
            ->add('etpdecision', ChoiceType::class, array(
                'label' => 'ETP Décision',
                'choices' => array(
                    '' => '',
                    'Oui' => 'oui',
                    'Non' => 'non',
                ),
                'required' => false
            ))

            ->add('progetp', ChoiceType::class, array(
                'label' => 'Type de programme',
                'choices' => array(
                    '' => '',
                    'ViVa' => 'ViVa',
                    'ViVa module AOMI' => 'ViVa module AOMI',
                ),
                'required' => false
            ))

            ->add('precisions', ChoiceType::class, array(
                'label' => 'Précision non inclusion',
                'choices' => array(
                    '' => '',
                    'Absence de besoins éducatifs' => 'Absence de besoins éducatifs',
                    'Refus' => 'Refus',
                    'Absence de critères médicaux d\'inclusion' => 'Absence de critères médicaux d\'inclusion',
                    'Prise en charge des besoins quotidiens' => 'Prise en charge des besoins quotidiens',
                    'Problèmes médicaux à régler' => 'Problèmes médicaux à régler',
                    'Problème de santé' => 'Problème de santé',
                    'Manque de motivation' => 'Manque de motivation',
                    'Indisponibilité' => 'Indisponibilité',
                    'Distance  d\'habitation' => 'Distance d\'habitation',
                    'Troubles cognitifs' => 'Troubles cognitifs',
                    'Troubles psychiatriques' => 'Troubles psychiatriques',
                    'Transport' => 'Transport',
                    'Barrière de la langue' => 'Barrière de la langue',
                    'NS' => 'NS',
                ),
                'required' => false
            ))

            ->add('precisionsperso', TextareaType::class, array(
                'label' => 'Précision contenu personnalisé',
                'required' => false
            ))

            ->add('objectif', TextareaType::class, array(
                'label' => 'Objectif',
                'required' => false
            ))

            ->add('motif', ChoiceType::class, array(
                'label' => 'Motif d\'arrêt de programme',
                'choices' => array(
                    '' => '',
                    'Absence besoins éducatifs' => 'Absence besoins éducatifs',
                    'Refus' => 'Refus',
                    'Plus de besoin/Fin de parcours' => 'Plus de besoin/Fin de parcours',
                    'Souhait arrêt' => 'Souhait arrêt',
                    'Perdu de vue' => 'Perdu de vue',
                    'Absence de critères médicaux d\'inclusion' => 'Absence de critères médicaux d\'inclusion',
                    'Prise en charge besoins quotidiens' => 'Prise en charge besoins quotidiens',
                    'Problèmes médicaux à régler' => 'Problèmes médicaux à régler',
                    'Problème de santé' => 'Problème de santé',
                    'Manque de motivation' => 'Manque de motivation',
                    'Indisponibilité' => 'Indisponibilité',
                    'Distance d\'habitation' => 'Distance d\'habitation',
                    'Troubles cognitifs' => 'Troubles cognitifs',
                    'Troubles psychiatriques' => 'Troubles psychiatriques',
                    'Transport' => 'Transport',
                    'Barrière de la langue' => 'Barrière de la langue',
                    'Déménagement' => 'Déménagement',
                    'Décès' => 'Décès',
                    'NS' => 'NS',
                ),
                'required' => false
            ))

            ->add('etp', ChoiceType::class, array(
                'label' => 'Point final parcours ETP',
                'choices' => array(
                    '' => '',
                    'Atelier information' => 'Atelier information',
                    'BCVs' => 'BCVs',
                    'DE' => 'DE',
                    'M3' => 'M3',
                    'M12' => 'M12',
                    'Renf1' => 'Renf1',
                    'Renf2' => 'Renf2',
                    'Atelier' => 'Atelier',
                    'Consultation' => 'Consultation',
                    'Entretien individuel' => 'Entretien individuel',
                    'Suivi téléphonique' => 'Suivi téléphonique',
                ),
                'required' => false
            ))

            ->add('dentree', DateType::class, array(
                'label' => 'Date de sortie  ',
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'attr' => [
                    'autocomplete' => 'off',
                    'class' => 'datepicker',
                ],
                'required' => false,
                'html5' => false
            ))

            ->add('offre', ChoiceType::class, array(
                'label' => 'Offres',
                'multiple' => false,
                'expanded' => true,
                'attr' => [
                    'class' => 'cntr',
                ],
                'placeholder' => 'Aucune offre en cours',
                'choices' => array(
                    'En cours d\'offre initiale' => 'initiale',
                    'En cours d\'offre de suivi' => 'suivi',
                    'En cours d\'offre de renforcement' => 'renforcement',
                    'En cours d\'offre de consolidation' => 'consolidation',
                    'En cours d\'offre supplémentaire' => 'supplementaire'
                ),
                'required' => false
            ))

            ->add('validation', SubmitType::class, array('label' => 'Enregistrer'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Patient::class,
            'allow_extra_fields' => true
        ]);
    }
}

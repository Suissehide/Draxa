<?php

namespace App\Form;

use App\Entity\DiagnosticEducatif;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class DiagnosticEducatifType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('facteursRisque', ChoiceType::class, array(
                'label' => 'Facteurs de risque',
                'multiple' => true,
                'expanded' => true,
                'attr' => [
                    'class' => 'radio-row',
                ],
                'choices' => array(
                    'Diabète' => 'Diabète',
                    'Hypertension' => 'Hypertension',
                    'Cholestérol' => 'Cholestérol',
                    'Tour de taille' => 'Tour de taille',
                    'Tabac' => 'Tabac',
                    'Activité physique' => 'Activité physique',
                    'Stress' => 'Stress',
                    'Adhésion thérapeutique' => 'Adhésion thérapeutique',
                    'Alimentation' => 'Alimentation'
                ),
                'required' => false
            ))

            ->add('qualiteVie', ChoiceType::class, array(
                'label' => 'Qualité de vie',
                'choices' => array(
                    'Bonne qualité de vie' => 'Bonne qualité de vie',
                    'Qualité de vie moyenne' => 'Qualité de vie moyenne',
                    'Mauvaise qualité de vie' => 'Mauvaise qualité de vie'
                ),
                'required' => false
            ))
            ->add('qualiteVieLibre', TextAreaType::class, array('label' => ' '))
            ->add('viePersonnelle', TextAreaType::class, array('label' => 'Vie personnelle'))
            ->add('vieProfessionnelle', TextAreaType::class, array('label' => 'Vie professionnelle'))
            ->add('occupations', TextAreaType::class, array('label' => 'Occupations'))
            ->add('loisirs', TextAreaType::class, array('label' => 'Loisirs'))

            ->add('implication', ChoiceType::class, array(
                'label' => 'Implication',
                'choices' => array(
                    'Forte implication dans sa PEC' => 'Forte implication dans sa PEC',
                    'Implication dans sa PEC à renforcer' => 'Implication dans sa PEC à renforcer'
                ),
                'required' => false
            ))
            ->add('implicationLibre', TextAreaType::class, array('label' => ' '))

            ->add('prioriteSante', ChoiceType::class, array(
                'label' => 'Priorité de santé',
                'choices' => array(
                    'Sa santé cardio-vasculaire' => 'Sa santé cardio-vasculaire',
                    'Autre problème de santé' => 'Autre problème de santé',
                    'Difficultés personnelles' => 'Difficultés personnelles'
                ),
                'required' => false
            ))
            ->add('prioriteSanteLibre', TextAreaType::class, array('label' => ' '))

            ->add('connaissancesMaladie', ChoiceType::class, array(
                'label' => 'Connaissances sur la maladie',
                'choices' => array(
                    'Acquises' => 'Acquises',
                    'En cours d\'acquisition' => 'En cours d\'acquisition',
                    'À renforcer' => 'À renforcer'
                ),
                'required' => false
            ))
            ->add('connaissancesMaladieLibre', TextAreaType::class, array('label' => ' '))
            ->add('mecanismes', TextAreaType::class, array('label' => 'Mécanismes'))
            ->add('localisations', TextAreaType::class, array('label' => 'Localisations'))
            ->add('symptomes', TextAreaType::class, array('label' => 'Symptômes'))
            ->add('chronicite', TextAreaType::class, array('label' => 'Chronicité'))
            ->add('reagirSignesAlerte', TextAreaType::class, array('label' => 'Réagir aux signes d\'alerte'))

            ->add('identificationFDR', ChoiceType::class, array(
                'label' => 'Identification de ses FDR',
                'choices' => array(
                    'Acquises' => 'Acquises',
                    'En cours d\'acquisition' => 'En cours d\'acquisition',
                    'À renforcer' => 'À renforcer'
                ),
                'required' => false
            ))
            ->add('identificationFDRLibre', TextAreaType::class, array('label' => ' '))
            ->add('ometFDR', ChoiceType::class, array(
                'label' => 'Omet de parler de',
                'multiple' => true,
                'expanded' => true,
                'attr' => [
                    'class' => 'radio-row',
                ],
                'choices' => array(
                    'Diabète' => 'Diabète',
                    'Hypertension' => 'Hypertension',
                    'Cholestérol' => 'Cholestérol',
                    'Tour de taille' => 'Tour de taille',
                    'Tabac' => 'Tabac',
                    'Activité physique' => 'Activité physique',
                    'Stress' => 'Stress',
                    'Adhésion thérapeutique' => 'Adhésion thérapeutique',
                    'Alimentation' => 'Alimentation'
                ),
                'required' => false
            ))

            ->add('gestionTensionArterielle', ChoiceType::class, array(
                'label' => 'Gestion de la tension artérielle',
                'choices' => array(
                    'Adaptée' => 'Adaptée',
                    'À renforcer' => 'À renforcer',
                    'Non concerné' => 'Non concerné'
                ),
                'required' => false
            ))
            ->add('gestionTensionArterielleLibre', TextAreaType::class, array('label' => ' '))
            ->add('gestionTensionArterielleSentimentAutoEfficacite', TextAreaType::class, array('label' => 'Sentiment d\'auto-efficacité'))
            ->add('gestionTensionArterielleEtapeChangement', TextAreaType::class, array('label' => 'Étape de changement'))

            ->add('gestionHba1c', ChoiceType::class, array(
                'label' => 'Gestion de l\'Hba1c',
                'choices' => array(
                    'Adaptée' => 'Adaptée',
                    'À renforcer' => 'À renforcer',
                    'Non concerné' => 'Non concerné'
                ),
                'required' => false
            ))
            ->add('gestionHba1cLibre', TextAreaType::class, array('label' => ' '))
            ->add('gestionHba1cSentimentAutoEfficacite', TextAreaType::class, array('label' => 'Sentiment d\'auto-efficacité'))
            ->add('gestionHba1cEtapeChangement', TextAreaType::class, array('label' => 'Étape de changement'))

            ->add('gestionLDL', ChoiceType::class, array(
                'label' => 'Gestion du LDL',
                'choices' => array(
                    'Adaptée' => 'Adaptée',
                    'À renforcer' => 'À renforcer',
                    'Non concerné' => 'Non concerné'
                ),
                'required' => false
            ))
            ->add('gestionLDLLibre', TextAreaType::class, array('label' => ' '))
            ->add('gestionLDLSentimentAutoEfficacite', TextAreaType::class, array('label' => 'Sentiment d\'auto-efficacité'))
            ->add('gestionLDLEtapeChangement', TextAreaType::class, array('label' => 'Étape de changement'))

            ->add('adhesionTraitement', ChoiceType::class, array(
                'label' => 'Adhésion au traitement',
                'choices' => array(
                    'Pas de difficultés' => 'Pas de difficultés',
                    'Légère difficulté' => 'Légère difficulté',
                    'Difficulté importante' => 'Difficulté importante'
                ),
                'required' => false
            ))
            ->add('adhesionTraitementLibre', TextAreaType::class, array('label' => ' '))
            ->add('adhesionTraitementSentimentAutoEfficacite', TextAreaType::class, array('label' => 'Sentiment d\'auto-efficacité'))
            ->add('adhesionTraitementEtapeChangement', TextAreaType::class, array('label' => 'Étape de changement'))

            ->add('alimentation', ChoiceType::class, array(
                'label' => 'Alimentation',
                'choices' => array(
                    'Adaptée' => 'Adaptée',
                    'En cours d\'adaptation' => 'En cours d\'adaptation',
                    'Peu adaptée' => 'Peu adaptée'
                ),
                'required' => false
            ))
            ->add('alimentationLibre', TextAreaType::class, array('label' => ' '))
            ->add('alimentationSentimentAutoEfficacite', TextAreaType::class, array('label' => 'Sentiment d\'auto-efficacité'))
            ->add('alimentationEtapeChangement', TextAreaType::class, array('label' => 'Étape de changement'))

            ->add('gestionStress', ChoiceType::class, array(
                'label' => 'Gestion du stress',
                'choices' => array(
                    'Adaptée' => 'Adaptée',
                    'En cours d\'acquisition' => 'En cours d\'acquisition',
                    'À renforcer' => 'À renforcer'
                ),
                'required' => false
            ))
            ->add('gestionStressLibre', TextAreaType::class, array('label' => ' '))
            ->add('gestionStressSentimentAutoEfficacite', TextAreaType::class, array('label' => 'Sentiment d\'auto-efficacité'))
            ->add('gestionStressEtapeChangement', TextAreaType::class, array('label' => 'Étape de changement'))

            ->add('consommationTabac', ChoiceType::class, array(
                'label' => 'Consommation de tabac',
                'choices' => array(
                    'Pas de tabac' => 'Pas de tabac',
                    'Sevrage en cours' => 'Sevrage en cours',
                    'Fumeur' => 'Fumeur'
                ),
                'required' => false
            ))
            ->add('consommationTabacNombreCigaretteJour', IntegerType::class, array('label' => 'Nombre de cigarette/jour',))
            ->add('consommationTabacLibre', TextAreaType::class, array('label' => ' '))
            
            ->add('consommationTabacSentimentAutoEfficacite', TextAreaType::class, array('label' => 'Sentiment d\'auto-efficacité'))
            ->add('consommationTabacEtapeChangement', TextAreaType::class, array('label' => 'Étape de changement'))

            ->add('gestionTourTaille', ChoiceType::class, array(
                'label' => 'Gestion du tour de taille',
                'choices' => array(
                    'Adaptée' => 'Adaptée',
                    'En cours d\'acquisition' => 'En cours d\'acquisition',
                    'À renforcer' => 'À renforcer'
                ),
                'required' => false
            ))
            ->add('gestionTourTailleLibre', TextAreaType::class, array('label' => ' '))
            ->add('gestionTourTailleSentimentAutoEfficacite', TextAreaType::class, array('label' => 'Sentiment d\'auto-efficacité'))
            ->add('gestionTourTailleEtapeChangement', TextAreaType::class, array('label' => 'Étape de changement'))

            ->add('activitePhysique', ChoiceType::class, array(
                'label' => 'Activité physique',
                'choices' => array(
                    'Très actif' => 'Très actif',
                    'Actif' => 'Actif',
                    'À renforcer' => 'À renforcer'
                ),
                'required' => false
            ))
            ->add('activitePhysiqueLibre', TextAreaType::class, array('label' => ' '))
            ->add('activitePhysiqueSentimentAutoEfficacite', TextAreaType::class, array('label' => 'Sentiment d\'auto-efficacité'))
            ->add('activitePhysiqueEtapeChangement', TextAreaType::class, array('label' => 'Étape de changement'))
            
            ->add('gestionFDR', ChoiceType::class, array(
                'label' => 'Gestion des FDR',
                'choices' => array(
                    'Acquise' => 'Acquise',
                    'En cours d\'acquisition' => 'En cours d\'acquisition',
                    'À renforcer' => 'À renforcer'
                ),
                'required' => false
            ))
            
            ->add('impactSurQualiteVie', ChoiceType::class, array(
                'label' => 'Impact sur sa qualité de vie',
                'choices' => array(
                    'Faible' => 'Faible',
                    'Moyen' => 'Moyen',
                    'Important' => 'Important'
                ),
                'required' => false
            ))
            ->add('stadeAcceptationMaladie', ChoiceType::class, array(
                'label' => 'Stade d\'acceptation de la maladie',
                'choices' => array(
                    'Semble en état de choc : Ne réalise pas bien ce qui lui arrive' => 'Semble en état de choc : Ne réalise pas bien ce qui lui arrive',
                    'Semble dans la dénégation de son état de santé : Minimise, Rationalise ce qui lui arrive' => 'Semble dans la dénégation de son état de santé : Minimise, Rationalise ce qui lui arrive',
                    'Semble être dans le marchandage quant à sa santé : est dans la négociation' => 'Semble être dans le marchandage quant à sa santé : est dans la négociation',
                    'Semble être dans une pseudo-acceptation de son état de santé : Refuse l\'idée de chronicité' => 'Semble être dans une pseudo-acceptation de son état de santé : Refuse l\'idée de chronicité',
                    'Semble dans le déni de son état de santé : Ne se sent pas malade' => 'Semble dans le déni de son état de santé : Ne se sent pas malade',
                    'Semble être dans la révolte quant à son état de santé ' => 'Semble être dans la révolte quant à son état de santé ',
                    'Semble être en état de résignation quant à son état de santé ' => 'Semble être en état de résignation quant à son état de santé ',
                    'Semble accepter son état de santé : Vit avec sa maladie, l\'a intégré à sa vie quotidienne' => 'Semble accepter son état de santé : Vit avec sa maladie, l\'a intégré à sa vie quotidienne'
                ),
                'required' => false
            ))
            ->add('soutienSocial', TextAreaType::class, array('label' => 'Soutien social'))
            ->add('projetDeVie', TextAreaType::class, array('label' => 'Projet de vie'))
            ->add('objectifsPatient', TextAreaType::class, array('label' => 'Objectifs du patient'))
            ->add('objectifsSoignants', ChoiceType::class, array(
                'label' => 'Objectifs des soignants',
                'multiple' => true,
                'expanded' => true,
                'attr' => [
                    'class' => 'radio-row',
                ],
                'choices' => array(
                    'Améliorer le LDL' => 'Améliorer le LDL',
                    'Améliorer l\'Hba1c' => 'Améliorer l\'Hba1c',
                    'Améliorer la tension artérielle' => 'Améliorer la tension artérielle',
                    'Augmenter l\'activité physique' => 'Augmenter l\'activité physique',
                    'Améliorer l\'observance des traitements' => 'Améliorer l\'observance des traitements',
                    'Diminuer le stress' => 'Diminuer le stress',
                    'Sevrer le tabac' => 'Sevrer le tabac',
                    'Diminuer le tour de taille' => 'Diminuer le tour de taille',
                    'Equilibrer l\'alimentation' => 'Equilibrer l\'alimentation'
                ),
                'required' => false
            ))
            ->add('suiviEducatifNegocie', TextAreaType::class, array('label' => 'Suivi éducatif négocié avec le patient'))

            ->add('rapatrier', HiddenType::class, array(
                'label' => ''
            ))

            ->add('validation', SubmitType::class, array('label' => 'Enregistrer'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DiagnosticEducatif::class,
        ]);
    }
}

<?php

namespace App\Form;

use App\Entity\DiagnosticEducatif;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
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
                'label' => 'Facteurs de risque',
                'expanded' => true,
                'multiple' => false,
                'placeholder' => false,
                'attr' => [
                    'class' => 'cntr',
                ],
                'choices' => array(
                    'Forte implication dans sa PEC' => 'Forte implication dans sa PEC',
                    'Implication dans sa PEC à renforcer' => 'Implication dans sa PEC à renforcer'
                ),
                'required' => false
            ))
            ->add('implicationLibre', TextAreaType::class, array('label' => ' '))
            ->add('prioriteSante', TextAreaType::class, array('label' => ' '))
            ->add('prioriteSanteLibre', TextAreaType::class, array('label' => ' '))

            ->add('connaissancesMaladie')
            ->add('connaissancesMaladieLibre')
            ->add('mecanismes')
            ->add('localisations')
            ->add('symptomes')
            ->add('chronicite')
            ->add('reagirSignesAlerte')
            ->add('identificationFDR')
            ->add('identificationFDRLibre')
            ->add('ometFDR')

            ->add('gestionTensionArterielle')
            ->add('gestionTensionArterielleLibre')
            ->add('gestionTensionArterielleSentimentAutoEfficacite')
            ->add('gestionTensionArterielleEtapeChangement')

            ->add('gestionHba1c')
            ->add('gestionHba1cLibre')
            ->add('gestionHba1cSentimentAutoEfficacite')
            ->add('gestionHba1cEtapeChangement')

            ->add('gestionLDL')
            ->add('gestionLDLLibre')
            ->add('gestionLDLSentimentAutoEfficacite')
            ->add('gestionLDLEtapeChangement')

            ->add('adhesionTraitement')
            ->add('adhesionTraitementLibre')
            ->add('adhesionTraitementSentimentAutoEfficacite')
            ->add('adhesionTraitementEtapeChangement')

            ->add('alimentation')
            ->add('alimentationLibre')
            ->add('alimentationSentimentAutoEfficacite')
            ->add('alimentationEtapeChangement')

            ->add('gestionStress')
            ->add('gestionStressLibre')
            ->add('gestionStressSentimentAutoEfficacite')
            ->add('gestionStressEtapeChangement')

            ->add('consommationTabac')
            ->add('consommationTabacNombreCigaretteJour')
            ->add('consommationTabacLibre')
            ->add('consommationTabacSentimentAutoEfficacite')
            ->add('consommationTabacEtapeChangement')

            ->add('gestionTourTaille')
            ->add('gestionTourTailleLibre')
            ->add('gestionTourTailleSentimentAutoEfficacite')
            ->add('gestionTourTailleEtapeChangement')

            ->add('activitePhysique')
            ->add('activitePhysiqueLibre')
            ->add('activitePhysiqueSentimentAutoEfficacite')
            ->add('activitePhysiqueEtapeChangement')

            ->add('gestionFDR')
            
            ->add('impactSurQualiteVie')
            ->add('stadeAcceptationMaladie')
            ->add('soutienSocial')
            ->add('projetDeVie')
            ->add('objectifsPatient')
            ->add('objectifsSoignants')
            ->add('suiviEducatifNegocie')

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

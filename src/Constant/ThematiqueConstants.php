<?php

namespace App\Constant;

class ThematiqueConstants
{
    public const CONSULTATION = [
        'consultation_dieteticienne' => 'Consultation diététicienne',
        'consultation_pharmacienne' => 'Consultation pharmacienne',
        'consultation_psychologue' => 'Consultation psychologue',
        'diagnostic_educatif' => 'Diagnostic éducatif',
    ];

    public const ENTRETIEN = [
        'activite_physique' => 'Activité physique',
        'atherosclerose' => 'Athérosclérose',
        'coaching_PRM' => 'Coaching PRM',
        'diabete' => 'Diabète',
        'diagnostic_educatif' => 'Diagnostic éducatif',
        'ETP_individuel' => 'ETP Individuel',
        'HTA' => 'HTA',
        'motivation_AP' => 'Motivation AP',
        'motivation_FDR' => 'Motivation FDR',
        'motivation_navigation' => 'Motivation navigation',
        'motivation_tabac' => 'Motivation tabac',
        'navigation' => 'Navigation',
        'reactu_1' => 'Réactu 1',
        'reactu_2' => 'Réactu 2',
        'reactu_3' => 'Réactu 3',
        'reactu_4' => 'Réactu 4',
        'reactu_5' => 'Réactu 5',
        'reactu_6' => 'Réactu 6',
        'signes_alerte_et_CAT' => 'Signes d\'alerte et CAT',
        'tabac' => 'Tabac',
    ];

    public const ATELIER = [
        'alimentation_insuffisance_cardiaque' => 'Alimentation et Insuffisance cardiaque',
        'cuisine_drive' => 'Cuisine Drive',
        'cuisine_mise_en_pratique' => 'Cuisine Mise en pratique',
        'facteurs_de_risque' => 'Facteurs de risque',
        'gerer_diabete' => 'Gérer mon diabète',
        'gerer_hta' => 'Gérer mon HTA',
        'maladie_CV_signes_alerte' => 'Maladie CV / Signes d\'alerte',
        'prevention_stress' => 'Prévention stress',
        'PRM1' => 'PRM1',
        'PRM2' => 'PRM2',
        'qu_est_ce_insuffisance_cardiaque' => 'Qu’est-ce que l’insuffisance cardiaque ?',
        'signes_alerte' => 'Signes d\'alerte',
        'stress_aigu' => 'Stress aigu',
        'sucres' => 'Sucres',
        'tabac' => 'Tabac',
    ];

    public const COACHING = [
        'M1/M2' => 'M1/M2',
        'M2/M3' => 'M2/M3',
        'M3_et_+' => 'M3 et +',
    ];

    /**
     * Libellés par catégorie, dans l'ordre attendu par les templates :
     * consultations, entretiens, ateliers, coachings, éducatives.
     */
    public static function lists(): array
    {
        return [
            array_values(self::CONSULTATION),
            array_values(self::ENTRETIEN),
            array_values(self::ATELIER),
            array_values(self::COACHING),
            [],
        ];
    }
}

<?php

namespace App\Controller;

use App\Entity\Patient;
use App\Entity\Slot;

use App\Constant\ThematiqueConstants;

use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class StatistiqueController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * @Route("/statistiques", name="statistiques")
     */
    public function index(Request $request): Response
    {
        $patients = $this->em->getRepository(Patient::class)->findAll();
        $dateStart = date_create_from_format("d/m/Y", "01/01/2022");
        $dateEnd = date_create_from_format("d/m/Y", "31/12/2023");

        $statistiques = [
            'entree' => [
                1 => 0,
                2 => 0,
                3 => 0,
                4 => 0
            ],
            'seance' => [
                1 => 0,
                2 => 0,
                3 => 0,
                4 => 0,
                5 => 0,
                6 => 0,
                7 => 0,
                8 => 0,
                9 => 0,
                10 => 0,
                11 => 0
            ],
            'sortie' => [
                1 => 0,
                2 => 0,
                3 => 0,
                4 => 0,
                5 => 0,
                6 => 0
            ],
            'modalite' => [
                1 => 0,
                2 => 0,
                3 => 0,
                4 => 0
            ]
        ];

        $statistiques['seance']['7'] += $this->statistique27($dateStart, $dateEnd);
        $statistiques['seance']['8'] += $this->statistique28($dateStart, $dateEnd);

        foreach ($patients as $patient) {
            $rdv = $patient->getRendezVous();

            $statistiques['entree']['1'] += $this->statistique11($rdv, $dateStart, $dateEnd);
            $statistiques['entree']['2'] += $this->statistique12($patient, $dateStart, $dateEnd);
            $statistiques['entree']['3'] += $this->statistique13($patient, $dateStart, $dateEnd);
            $statistiques['entree']['4'] += $this->statistique14($patient, $dateStart, $dateEnd);

            $statistiques['seance']['1'] += $this->statistique21($rdv, $dateStart, $dateEnd);
            $statistiques['seance']['2'] += $this->statistique22($rdv, $dateStart, $dateEnd);
            $statistiques['seance']['3'] += $this->statistique23($rdv, $dateStart, $dateEnd);
            $statistiques['seance']['4'] += $this->statistique24($rdv, $dateStart, $dateEnd);
            $statistiques['seance']['5'] += $this->statistique25($rdv, $dateStart, $dateEnd);

            $statistiques['seance']['6'] += $this->statistique26($rdv, $dateStart, $dateEnd);

            $statistiques['seance']['9'] += $this->statistique29($rdv, $dateStart, $dateEnd);
            $statistiques['seance']['10'] += $this->statistique210($rdv, $dateStart, $dateEnd);
            $statistiques['seance']['11'] += $this->statistique211($rdv, $dateStart, $dateEnd);

            $statistiques['sortie']['1'] += $this->statistique31($rdv, $dateStart, $dateEnd);
            $statistiques['sortie']['2'] += $this->statistique32($rdv, $dateStart, $dateEnd);
            $statistiques['sortie']['3'] += $this->statistique33($rdv, $dateStart, $dateEnd);
            $statistiques['sortie']['4'] += $this->statistique34($rdv, $dateStart, $dateEnd);
            $statistiques['sortie']['5'] += $this->statistique35($rdv, $dateStart, $dateEnd);
            $statistiques['sortie']['6'] += $this->statistique36($rdv, $dateStart, $dateEnd);

            $statistiques['modalite']['1'] += $this->statistique41($rdv, $dateStart, $dateEnd);
            $statistiques['modalite']['2'] = "Oui";
            $statistiques['modalite']['3'] += $this->statistique43($rdv, $dateStart, $dateEnd);
            $statistiques['modalite']['4'] += $this->statistique44($rdv, $dateStart, $dateEnd);
        }

        foreach (ThematiqueConstants::CONSULTATION as $k => $v) { $consultations[$k] = $v; }
        foreach (ThematiqueConstants::ENTRETIEN as $k => $v) { $entretiens[$k] = $v; }
        foreach (ThematiqueConstants::ATELIER as $k => $v) { $ateliers[$k] = $v; }
        foreach (ThematiqueConstants::COACHING as $k => $v) { $coachings[$k] = $v; }
        $thematiques = [
            "consultations" => $consultations,
            "entretiens" => $entretiens,
            "ateliers" => $ateliers,
            "coachings" => $coachings,
            "educatives" => ["" => ""]
        ];

        if ($request->isXmlHttpRequest()) {
            $slots = $this->em->getRepository(Slot::class)->findByDate($dateStart, $dateEnd);

            $response = [];
            $response['consultations'] = $this->createSlotCategorie($slots, $thematiques["consultations"], 'Consultation');
            $response['entretiens'] = $this->createSlotCategorie($slots, $thematiques["entretiens"], 'Entretien');
            $response['ateliers'] = $this->createSlotCategorie($slots, $thematiques["ateliers"], 'Atelier');
            $response['coachings'] = $this->createSlotCategorie($slots, $thematiques["coachings"], 'Coaching');
            $response['educatives'] = $this->createSlotCategorie($slots, $thematiques["educatives"], 'Educative');

            return new JsonResponse($response, Response::HTTP_OK);
        }

        return $this->render('statistique/index.html.twig', [
            'controller_name' => 'StatistiqueController',
            'statistiques' => $statistiques,
            'title' => 'Statistiques',
            'thematiques' => $thematiques
        ]);
    }

    private function createSlotCategorie(array $slots, $thematiques, string $categorie)
    {
        $jsonContent = array_reduce(array_keys($thematiques), function($carry, $key) {
            $carry[$key] = [
                'oui' => 0,
                'non' => 0,
                'null' => 0
            ];
            return $carry;
        }, []);

        foreach ($slots as $s) {
            $rendezVous = $s->getRendezVous();
            foreach ($rendezVous as $r) {
                if ($s->getCategorie() === $categorie) {
                    $thematique = array_search($r->getThematique(), $thematiques);
                    $etat = strtolower($r->getEtat());
                    if ($etat == "oui")
                        $jsonContent[$thematique]["oui"] += 1;
                    else if ($etat == "non")
                        $jsonContent[$thematique]["non"] += 1;
                    else
                        $jsonContent[$thematique]["null"] += 1;
                }
            }
        }
        return $jsonContent;
    }

    /**
     * 1 - Entrée dans le programme
     */
    private function statistique11($rendezVous, $dateStart, $dateEnd): int
    {
        foreach ($rendezVous as $r) {
            if (
                $r->getEtat() == "Oui"
                && $this->isInDateRange($dateStart, $dateEnd, $r->getDate())
                && $r->getCategorie() == "Entretien"
                && $r->getThematique() == "Diagnostic éducatif"
            ) {
                return 1;
            }
        }
        return 0;
    }

    private function statistique12(Patient $patient, $dateStart, $dateEnd): int
    {
        if (
            $patient->getDedate() != null
            && $this->isInDateRange($dateStart, $dateEnd, $patient->getDedate())
            && $patient->getOrientation() == "Orientation pro santé ext hôpital"
        ) {
            return 1;
        }
        return 0;
    }

    private function statistique13(Patient $patient, $dateStart, $dateEnd): int
    {
        if (
            $patient->getDedate()
            && $this->isInDateRange($dateStart, $dateEnd, $patient->getDedate())
            && $patient->getOrientation() == "Orientation pro santé au cours hospit"
        ) {
            return 1;
        }
        return 0;
    }

    private function statistique14(Patient $patient, $dateStart, $dateEnd): int
    {
        if (
            $patient->getDedate()
            && $this->isInDateRange($dateStart, $dateEnd, $patient->getDedate())
            && $patient->getOrientation() == "Orientation pro santé en Cs"
        ) {
            return 1;
        }
        return 0;
    }

    /**
     * 2 - Séances d'ETP et mode de prise en charge
     */
    private function statistique21($rendezVous, $dateStart, $dateEnd): int
    {
        $newDateStart = $dateStart;
        $hasDiagnosticEducatif = false;
        foreach ($rendezVous as $r) {
            if (
                $r->getEtat() == "Oui"
                && $this->isInDateRange($newDateStart, $dateEnd, $r->getDate())
                && $r->getCategorie() == "Entretien"
                && $r->getThematique() == "Diagnostic éducatif"
                && $r->getType() == "Hospit"
                && $hasDiagnosticEducatif == false
            ) {
                $hasDiagnosticEducatif = true;
                $newDateStart = $r->getDate();
            }

            if (
                $r->getEtat() == "Oui"
                && $this->isInDateRange($newDateStart, $dateEnd, $r->getDate())
                && $r->getType() == "Hospit"
                && $hasDiagnosticEducatif == true
            ) {
                return 1;
            }
        }
        return 0;
    }

    private function statistique22($rendezVous, $dateStart, $dateEnd): int
    {
        $newDateStart = $dateStart;
        $hasDiagnosticEducatif = false;
        foreach ($rendezVous as $r) {
            if (
                $r->getEtat() == "Oui"
                && $this->isInDateRange($newDateStart, $dateEnd, $r->getDate())
                && $r->getCategorie() == "Entretien"
                && $r->getThematique() == "Diagnostic éducatif"
                && ($r->getType() == "Ambu" || $r->getType() == "Tel")
                && $hasDiagnosticEducatif == false
            ) {
                $hasDiagnosticEducatif = true;
                $newDateStart = $r->getDate();
            }

            if (
                $r->getEtat() == "Oui"
                && $this->isInDateRange($newDateStart, $dateEnd, $r->getDate())
                && ($r->getType() == "Ambu" || $r->getType() == "Tel")
                && $hasDiagnosticEducatif == true
            ) {
                return 1;
            }
        }
        return 0;
    }

    private function statistique23($rendezVous, $dateStart, $dateEnd): int
    {
        return 0;
    }

    private function statistique24($rendezVous, $dateStart, $dateEnd): int
    {
        $newDateStart = $dateStart;
        $hasDiagnosticEducatif = false;
        foreach ($rendezVous as $r) {
            if (
                $r->getEtat() == "Oui"
                && $this->isInDateRange($newDateStart, $dateEnd, $r->getDate())
                && $r->getCategorie() == "Entretien"
                && $r->getThematique() == "Diagnostic éducatif"
                && $hasDiagnosticEducatif == false
            ) {
                $hasDiagnosticEducatif = true;
                $newDateStart = $r->getDate();
            }

            if (
                $r->getEtat() == "Oui"
                && $this->isInDateRange($newDateStart, $dateEnd, $r->getDate())
                && $hasDiagnosticEducatif == true
            ) {
                return 1;
            }
        }
        return 0;
    }

    private function statistique25($rendezVous, $dateStart, $dateEnd): int
    {
        foreach ($rendezVous as $r) {
            if (
                $r->getEtat() == "Oui"
                && $this->isInDateRange($dateStart, $dateEnd, $r->getDate())
                && $r->getType() == "Tel"
            ) {
                return 1;
            }
        }
        return 0;
    }

    /* */

    private function statistique26($rendezVous, $dateStart, $dateEnd): int
    {
        $ret = 0;
        foreach ($rendezVous as $r) {
            if (
                $r->getEtat() == "Oui"
                && $this->isInDateRange($dateStart, $dateEnd, $r->getDate())
                && ($r->getCategorie() == "Entretien" || $r->getCategorie() == "Consultation" || $r->getCategorie() == "Coaching")
            ) {
                $ret += 1;
            }
        }
        return $ret;
    }

    private function statistique27($dateStart, $dateEnd): int
    {
        $slots = $this->em->getRepository(Slot::class)->findAll();
        $educative = 0;
        $atelier = 0;
        foreach ($slots as $s) {
            $rendezVous = $s->getRendezVous();
            foreach ($rendezVous as $r) {
                if (
                    $r->getEtat() == "Oui"
                    && $this->isInDateRange($dateStart, $dateEnd, $s->getDate())
                    && $r->getCategorie() == "Educative"
                ) {
                    $educative += 1;
                }

                if (
                    $r->getEtat() == "Oui"
                    && $this->isInDateRange($dateStart, $dateEnd, $s->getDate())
                    && $r->getCategorie() == "Atelier"
                ) {
                    $atelier += 1;
                }
            }
        }
        return $atelier + (($educative / 3) * 10);
    }

    private function statistique28($dateStart, $dateEnd): float
    {
        $totalSeance = $this->statistique27($dateStart, $dateEnd);
        $totalSlotEducative = $this->em->getRepository(Slot::class)->findSlotEducative($dateStart, $dateEnd);
        $totalSlotAtelier = $this->em->getRepository(Slot::class)->findSlotAtelier($dateStart, $dateEnd);

        return round($totalSeance / ((($totalSlotEducative / 3) * 10) + $totalSlotAtelier), 2);
    }

    private function statistique29($rendezVous, $dateStart, $dateEnd): int
    {
        return 0;
    }

    private function statistique210($rendezVous, $dateStart, $dateEnd): int
    {
        return 0;
    }

    private function statistique211($rendezVous, $dateStart, $dateEnd): int
    {
        return 0;
    }

    /**
     * 3 - Sortie du programme
     */
    private function statistique31($rendezVous, $dateStart, $dateEnd): int
    {
        $newDateStart = $dateStart;
        $hasDiagnosticEducatif = false;
        $seance = false;
        $reactu = false;
        foreach ($rendezVous as $r) {
            if (
                $r->getEtat() == "Oui"
                && $this->isInDateRange($newDateStart, $dateEnd, $r->getDate())
                && $r->getCategorie() == "Entretien"
                && $r->getThematique() == "Diagnostic éducatif"
                && $hasDiagnosticEducatif == false
            ) {
                $hasDiagnosticEducatif = true;
                $newDateStart = $r->getDate();
            }

            if (
                $r->getEtat() == "Oui"
                && $this->isInDateRange($newDateStart, $dateEnd, $r->getDate())
                && $r->getCategorie() == "Entretien"
                && substr_compare($r->getThematique(), "Réactu", 0, 5) == 0
                && $hasDiagnosticEducatif == true
            ) {
                $reactu = true;
            } else if (
                $r->getEtat() == "Oui"
                && $this->isInDateRange($newDateStart, $dateEnd, $r->getDate())
                && $hasDiagnosticEducatif == true
            ) {
                $seance = true;
            }
        }
        return $hasDiagnosticEducatif && $seance && $reactu ? 1 : 0;
    }

    private function statistique32($rendezVous, $dateStart, $dateEnd): int
    {
        $newDateStart = $dateStart;
        $hasDiagnosticEducatif = false;
        $seance = false;
        $reactu = false;
        foreach ($rendezVous as $r) {
            if (
                $r->getEtat() == "Oui"
                && $this->isInDateRange($newDateStart, $dateEnd, $r->getDate())
                && $r->getCategorie() == "Entretien"
                && $r->getType() == "Hospit"
                && $r->getThematique() == "Diagnostic éducatif"
                && $hasDiagnosticEducatif == false
            ) {
                $hasDiagnosticEducatif = true;
                $newDateStart = $r->getDate();
            }

            if (
                $r->getEtat() == "Oui"
                && $this->isInDateRange($newDateStart, $dateEnd, $r->getDate())
                && $r->getType() != "Hospit"
            ) {
                return 0;
            }

            if (
                $r->getEtat() == "Oui"
                && $this->isInDateRange($newDateStart, $dateEnd, $r->getDate())
                && $r->getCategorie() == "Entretien"
                && substr_compare($r->getThematique(), "Réactu", 0, 5) == 0
                && $hasDiagnosticEducatif == true
            ) {
                $reactu = true;
            } else if (
                $r->getEtat() == "Oui"
                && $this->isInDateRange($newDateStart, $dateEnd, $r->getDate())
                && $hasDiagnosticEducatif == true
            ) {
                $seance = true;
            }
        }
        return $hasDiagnosticEducatif && $seance && $reactu ? 1 : 0;
    }

    private function statistique33($rendezVous, $dateStart, $dateEnd): int
    {
        $newDateStart = $dateStart;
        $hasDiagnosticEducatif = false;
        $seance = false;
        $reactu = false;
        foreach ($rendezVous as $r) {
            if (
                $r->getEtat() == "Oui"
                && $this->isInDateRange($newDateStart, $dateEnd, $r->getDate())
                && $r->getCategorie() == "Entretien"
                && $r->getType() == "Ambu"
                && $r->getThematique() == "Diagnostic éducatif"
                && $hasDiagnosticEducatif == false
            ) {
                $hasDiagnosticEducatif = true;
                $newDateStart = $r->getDate();
            }

            if (
                $r->getEtat() == "Oui"
                && $this->isInDateRange($newDateStart, $dateEnd, $r->getDate())
                && $r->getType() != "Ambu"
            ) {
                return 0;
            }

            if (
                $r->getEtat() == "Oui"
                && $this->isInDateRange($newDateStart, $dateEnd, $r->getDate())
                && $r->getCategorie() == "Entretien"
                && substr_compare($r->getThematique(), "Réactu", 0, 5) == 0
                && $hasDiagnosticEducatif == true
            ) {
                $reactu = true;
            } else if (
                $r->getEtat() == "Oui"
                && $this->isInDateRange($newDateStart, $dateEnd, $r->getDate())
                && $hasDiagnosticEducatif == true
            ) {
                $seance = true;
            }
        }
        return $hasDiagnosticEducatif && $seance && $reactu ? 1 : 0;
    }

    private function statistique34($rendezVous, $dateStart, $dateEnd): int
    {
        return $this->statistique31($rendezVous, $dateStart, $dateEnd);
    }

    private function statistique35($rendezVous, $dateStart, $dateEnd): int
    {
        return 0;
    }

    private function statistique36($rendezVous, $dateStart, $dateEnd): int
    {
        foreach ($rendezVous as $r) {
            if (
                $r->getEtat() == "Oui"
                && $this->isInDateRange($dateStart, $dateEnd, $r->getDate())
                && $r->getCategorie() == "Entretien"
                && substr_compare($r->getThematique(), "Réactu", 0, 5) == 0
            ) {
                return 1;
            }
        }
        return 0;
    }

    /**
     * 4 - Modalités de suivi - Coordination du parcours de soins
     */
    private function statistique41($rendezVous, $dateStart, $dateEnd): int
    {
        $newDateStart = $dateStart;
        $hasDiagnosticEducatif = false;
        $seance = 0;
        $evaluation = 0;
        foreach ($rendezVous as $r) {
            if (
                $r->getEtat() == "Oui"
                && $this->isInDateRange($newDateStart, $dateEnd, $r->getDate())
                && $r->getCategorie() == "Entretien"
                && $r->getThematique() == "Diagnostic éducatif"
                && $hasDiagnosticEducatif == false
            ) {
                $hasDiagnosticEducatif = true;
                $newDateStart = $r->getDate();
            }

            if (
                $r->getEtat() == "Oui"
                && $this->isInDateRange($newDateStart, $dateEnd, $r->getDate())
                && $hasDiagnosticEducatif == true
            ) {
                $seance += 1;
            }

            if (
                $r->getEtat() == "Oui"
                && $this->isInDateRange($newDateStart, $dateEnd, $r->getDate())
                && $r->getCategorie() == "Entretien"
                && $r->getThematique() == "Réactu1"
                && $hasDiagnosticEducatif == true
            ) {
                $evaluation += 1;
            }
        }
        return $hasDiagnosticEducatif && $seance >= 2 && $evaluation >= 1 ? 1 : 0;
    }

    private function statistique43($rendezVous, $dateStart, $dateEnd): int
    {
        $newDateStart = $dateStart;
        $hasDiagnosticEducatif = false;
        $seance = 0;
        $evaluation = 0;
        foreach ($rendezVous as $r) {
            if (
                $r->getEtat() == "Oui"
                && $this->isInDateRange($newDateStart, $dateEnd, $r->getDate())
                && $r->getCategorie() == "Entretien"
                && $r->getThematique() == "Diagnostic éducatif"
                && $hasDiagnosticEducatif == false
            ) {
                $hasDiagnosticEducatif = true;
                $newDateStart = $r->getDate();
            }

            if (
                $r->getEtat() == "Oui"
                && $this->isInDateRange($newDateStart, $dateEnd, $r->getDate())
                && $hasDiagnosticEducatif == true
            ) {
                $seance += 1;
            }

            if (
                $r->getEtat() == "Oui"
                && $this->isInDateRange($newDateStart, $dateEnd, $r->getDate())
                && $r->getCategorie() == "Entretien"
                && substr_compare($r->getThematique(), "Réactu", 0, 5) == 0
                && $hasDiagnosticEducatif == true
            ) {
                $evaluation += 1;
            }
        }
        return $hasDiagnosticEducatif && $seance >= 2 && $evaluation >= 1 ? 1 : 0;
    }

    private function statistique44($rendezVous, $dateStart, $dateEnd): int
    {
        foreach ($rendezVous as $r) {
            if (
                $r->getEtat() == "Oui"
                && $this->isInDateRange($dateStart, $dateEnd, $r->getDate())
                && $r->getCategorie() == "Entretien"
                && $r->getType() == "Hospit"
                && substr_compare($r->getThematique(), "Réactu", 0, 5) == 0
            ) {
                return 1;
            }
        }
        return 0;
    }

    /**
     *
     */
    private function isInDateRange($dateStart, $dateEnd, $date): bool
    {
        $start_ts = $dateStart->getTimestamp();
        $end_ts = $dateEnd->getTimestamp();
        $user_ts = $date->getTimestamp();
        return (($user_ts >= $start_ts) && ($user_ts <= $end_ts));
    }
}

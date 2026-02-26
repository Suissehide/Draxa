<?php

namespace App\Controller;

use App\Constant\ThematiqueConstants;
use App\Entity\Patient;
use App\Entity\Slot;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatistiqueController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * @Route("/statistiques", name="statistiques")
     */
    public function index(Request $request): Response
    {
        foreach (ThematiqueConstants::ENTRETIEN as $k => $v) {
            $entretiens[$k] = $v;
        }
        foreach (ThematiqueConstants::ATELIER as $k => $v) {
            $ateliers[$k] = $v;
        }
        foreach (ThematiqueConstants::COACHING as $k => $v) {
            $coachings[$k] = $v;
        }

        $entretiens[""] = "Autre";
        $ateliers[""] = "Autre";
        $coachings[""] = "Autre";

        $thematiques = [
            "entretiens" => $entretiens,
            "ateliers" => $ateliers,
            "coachings" => $coachings,
            "educatives" => ["" => "Total"]
        ];

        if ($request->isXmlHttpRequest()) {
            $dateStart = DateTime::createFromFormat("d/m/Y", date($request->get('dateStart')));
            $dateEnd = DateTime::createFromFormat("d/m/Y", date($request->get('dateEnd')));
            $slots = $this->em->getRepository(Slot::class)->findByDate($dateStart, $dateEnd);
            $patients = $this->em->createQuery(
                'SELECT p, r FROM App\Entity\Patient p LEFT JOIN p.rendezVous r ORDER BY r.date ASC'
            )->getResult();

            $statistiques = [
                'entree' => [
                    1 => 0,
                    2 => 0,
                    3 => 0,
                    4 => 0,
                    5 => 0
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
                    11 => 0,
                    12 => 0,
                    13 => 0
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
                    '1bis' => 0,
                    2 => 0,
                    3 => 0,
                    '3bis' => 0,
                    4 => 0
                ]
            ];

            $stat27 = $this->statistique27($slots, $dateStart, $dateEnd);
            $statistiques['seance']['8'] += $stat27;
            $statistiques['seance']['9'] += $this->statistique27bis($slots, $dateStart, $dateEnd);
            $statistiques['seance']['10'] += $this->statistique28($stat27, $slots, $dateStart, $dateEnd);

            $patientsManquants = ['orientation' => [], 'dedate' => [], 'mode' => [], 'spontane' => []];

            foreach ($patients as $patient) {
                $rdv = $patient->getRendezVous();

                $hasRdvInPeriod = false;
                foreach ($rdv as $r) {
                    if ($r->getEtat() === "Oui" && $this->isInDateRange($dateStart, $dateEnd, $r->getDate())) {
                        $hasRdvInPeriod = true;
                        break;
                    }
                }
                if ($hasRdvInPeriod) {
                    $info = [
                        'id' => $patient->getId(),
                        'nom' => $patient->getNom() ?? '',
                        'prenom' => $patient->getPrenom() ?? '',
                        'ddn' => $patient->getDate() ? $patient->getDate()->format('d/m/Y') : ''
                    ];
                    if (!$patient->getOrientation()) {
                        $patientsManquants['orientation'][] = $info;
                    }
                    if (!$patient->getDedate()) {
                        $patientsManquants['dedate'][] = $info;
                    }
                    if (!$patient->getMode()) {
                        $patientsManquants['mode'][] = $info;
                    }
                    if (in_array($patient->getOrientation(), ['NS', 'Venue spontanée'])) {
                        $patientsManquants['spontane'][] = $info;
                    }
                }

                $statistiques['entree']['1'] += $this->statistique11($patient, $rdv, $dateStart, $dateEnd);
                $statistiques['entree']['2'] += $this->statistique11bis($rdv, $dateStart, $dateEnd);
                $statistiques['entree']['3'] += $this->statistique12($patient, $rdv, $dateStart, $dateEnd);
                $statistiques['entree']['4'] += $this->statistique13($patient, $rdv, $dateStart, $dateEnd);
                $statistiques['entree']['5'] += $this->statistique14($patient, $rdv, $dateStart, $dateEnd);

                $statistiques['seance']['1'] += $this->statistique21($patient, $rdv, $dateStart, $dateEnd);
                $statistiques['seance']['2'] += $this->statistique22($patient, $rdv, $dateStart, $dateEnd);
                $statistiques['seance']['3'] += $this->statistique23($patient, $rdv, $dateStart, $dateEnd);
                $statistiques['seance']['4'] += $this->statistique24($patient, $rdv, $dateStart, $dateEnd);
                $statistiques['seance']['5'] += $this->statistique25($patient, $rdv, $dateStart, $dateEnd);

                $statistiques['seance']['6'] += $this->statistique26($rdv, $dateStart, $dateEnd);
                $statistiques['seance']['7'] += $this->statistique26bis($rdv, $dateStart, $dateEnd);

                $statistiques['seance']['11'] += $this->statistique29($rdv, $dateStart, $dateEnd);
                $statistiques['seance']['12'] += $this->statistique210($rdv, $dateStart, $dateEnd);
                $statistiques['seance']['13'] += $this->statistique211($rdv, $dateStart, $dateEnd);

                $statistiques['sortie']['1'] += $this->statistique31($patient, $rdv, $dateStart, $dateEnd);
                $statistiques['sortie']['2'] += $this->statistique32($patient, $rdv, $dateStart, $dateEnd);
                $statistiques['sortie']['3'] += $this->statistique33($patient, $rdv, $dateStart, $dateEnd);
                $statistiques['sortie']['4'] += $this->statistique34($patient, $rdv, $dateStart, $dateEnd);
                $statistiques['sortie']['5'] += $this->statistique35($patient, $rdv, $dateStart, $dateEnd);
                $statistiques['sortie']['6'] += $this->statistique36($patient, $rdv, $dateStart, $dateEnd);

                $statistiques['modalite']['1'] += $this->statistique41($patient, $rdv, $dateStart, $dateEnd);
                $statistiques['modalite']['1bis'] += $this->statistique41bis($patient, $rdv, $dateStart, $dateEnd);
                $statistiques['modalite']['2'] = "Oui";
                $statistiques['modalite']['3'] += $this->statistique43($patient, $rdv, $dateStart, $dateEnd);
                $statistiques['modalite']['3bis'] += $this->statistique43bis($patient, $rdv, $dateStart, $dateEnd);
                $statistiques['modalite']['4'] += $this->statistique44($patient, $rdv, $dateStart, $dateEnd);
            }

            $s = [];
            $s['entretiens'] = $this->createSlotCategorie($slots, $thematiques["entretiens"], 'Entretien');
            $s['ateliers'] = $this->createSlotCategorie($slots, $thematiques["ateliers"], 'Atelier');
            $s['coachings'] = $this->createSlotCategorie($slots, $thematiques["coachings"], 'Coaching');
            $s['educatives'] = $this->createSlotCategorie($slots, $thematiques["educatives"], 'Educative');

            $response['slots'] = $s;
            $response['statistiques'] = $statistiques;
            $response['patientsManquants'] = $patientsManquants;

            $categories = ['Consultation', 'Entretien', 'Coaching'];
            $soignants = [];
            foreach ($slots as $s) {
                $soignant = $s->getSoignant();
                if (!$soignant) continue;
                $nom = (string) $soignant;
                if (!isset($soignants[$nom])) {
                    $soignants[$nom] = [];
                    foreach ($categories as $cat) {
                        $soignants[$nom][$cat] = ['oui' => 0, 'non' => 0];
                    }
                }
                $cat = $s->getCategorie();
                if (!in_array($cat, $categories)) continue;
                foreach ($s->getRendezVous() as $r) {
                    if ($r->getEtat() === "Oui") {
                        $soignants[$nom][$cat]['oui']++;
                    } else if ($r->getEtat() === "Non") {
                        $soignants[$nom][$cat]['non']++;
                    }
                }
            }
            $response['soignants'] = $soignants;

            $ateliersStats = [];
            foreach ($slots as $s) {
                if ($s->getCategorie() !== 'Atelier') continue;
                $them = $s->getThematique();
                if (!$them) continue;
                if (!isset($ateliersStats[$them])) {
                    $ateliersStats[$them] = ['slots' => 0, 'oui' => 0, 'non' => 0];
                }
                $ateliersStats[$them]['slots']++;
                foreach ($s->getRendezVous() as $r) {
                    if ($r->getEtat() === "Oui") {
                        $ateliersStats[$them]['oui']++;
                    } else if ($r->getEtat() === "Non") {
                        $ateliersStats[$them]['non']++;
                    }
                }
            }
            $moyAteliers = [];
            foreach ($ateliersStats as $them => $data) {
                $nb = $data['slots'];
                $moyAteliers[$them] = [
                    'slots' => $nb,
                    'oui' => $data['oui'],
                    'non' => $data['non'],
                    'moyOui' => $nb > 0 ? round($data['oui'] / $nb, 1) : 0,
                ];
            }
            $response['moyAteliers'] = $moyAteliers;

            return new JsonResponse($response, Response::HTTP_OK);
        }

        return $this->render('statistique/index.html.twig', [
            'controller_name' => 'StatistiqueController',
            'title' => 'Statistiques',
            'thematiques' => $thematiques
        ]);
    }

    private function createSlotCategorie(array $slots, $thematiques, string $category)
    {
        $jsonContent = array_reduce(array_keys($thematiques), function ($carry, $key) {
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
                if ($s->getCategorie() === $category) {
                    $thematique = array_search($r->getThematique(), $thematiques);
                    if (!$thematique)
                        $thematique = "";
                    $etat = strtolower($r->getEtat());
                    if ($etat === "oui")
                        $jsonContent[$thematique]["oui"] += 1;
                    else if ($etat === "non")
                        $jsonContent[$thematique]["non"] += 1;
                    else
                        $jsonContent[$thematique]["null"] += 1;
                }
            }
        }
        return $jsonContent;
    }

    /**
     * Avoir une Dedate dans la range équivaut à un Entretien de thématique "Diagnostic éducatif".
     */
    private function getDiagnosticEducatifDate(Patient $patient, $rendezVous, $dateStart, $dateEnd): ?DateTime
    {
        if ($this->isInDateRange($dateStart, $dateEnd, $patient->getDedate())) {
            return $patient->getDedate();
        }
        foreach ($rendezVous as $r) {
            if (
                $r->getEtat() === "Oui"
                && $this->isInDateRange($dateStart, $dateEnd, $r->getDate())
                && $r->getCategorie() === "Entretien"
                && $r->getThematique() === "Diagnostic éducatif"
            ) {
                return $r->getDate();
            }
        }
        return null;
    }

    /**
     * 1 - Entrée dans le programme
     */
    private function statistique11(Patient $patient, $rendezVous, $dateStart, $dateEnd): int
    {
        return $this->getDiagnosticEducatifDate($patient, $rendezVous, $dateStart, $dateEnd) !== null ? 1 : 0;
    }

    private function statistique11bis($rendezVous, $dateStart, $dateEnd): int
    {
        foreach ($rendezVous as $r) {
            if (
                $r->getEtat() === "Oui"
                && $this->isInDateRange($dateStart, $dateEnd, $r->getDate())
                && $r->getCategorie() === "Entretien"
                && $r->getThematique() === "Diagnostic éducatif"
                && $r->getType() === "Tel"
            ) {
                return 1;
            }
        }
        return 0;
    }

    private function statistique12(Patient $patient, $rendezVous, $dateStart, $dateEnd): int
    {
        if (
            $patient->getDedate() != null
            && $this->isInDateRange($dateStart, $dateEnd, $patient->getDedate())
            && $patient->getOrientation() === "Orientation pro santé ext hôpital"
        ) {
            return 1;
        }
        return 0;
    }

    private function statistique13(Patient $patient, $rendezVous, $dateStart, $dateEnd): int
    {
        if (
            $patient->getDedate()
            && $this->isInDateRange($dateStart, $dateEnd, $patient->getDedate())
            && $patient->getOrientation() === "Orientation pro santé au cours hospit"
        ) {
            return 1;
        }
        return 0;
    }

    private function statistique14(Patient $patient, $rendezVous, $dateStart, $dateEnd): int
    {
        if (
            $patient->getDedate()
            && $this->isInDateRange($dateStart, $dateEnd, $patient->getDedate())
            && $patient->getOrientation() === "Orientation pro santé en Cs"
        ) {
            return 1;
        }
        return 0;
    }

    /**
     * 2 - Séances d'ETP et mode de prise en charge
     */
    private function statistique21(Patient $patient, $rendezVous, $dateStart, $dateEnd): int
    {
        $deDate = $this->getDiagnosticEducatifDate($patient, $rendezVous, $dateStart, $dateEnd);
        if ($deDate === null) {
            return 0;
        }

        $hasHospit = false;
        foreach ($rendezVous as $r) {
            if (
                $r->getEtat() === "Oui"
                && $this->isInDateRange($deDate, $dateEnd, $r->getDate())
            ) {
                if ($r->getType() === "Hospit") {
                    $hasHospit = true;
                } else {
                    return 0;
                }
            }
        }
        return $hasHospit ? 1 : 0;
    }

    private function statistique22(Patient $patient, $rendezVous, $dateStart, $dateEnd): int
    {
        $deDate = $this->getDiagnosticEducatifDate($patient, $rendezVous, $dateStart, $dateEnd);
        if ($deDate === null) {
            return 0;
        }

        $hasExtern = false;
        foreach ($rendezVous as $r) {
            if (
                $r->getEtat() === "Oui"
                && $this->isInDateRange($deDate, $dateEnd, $r->getDate())
            ) {
                if ($r->getType() === "Ambu" || $r->getType() === "Tel") {
                    $hasExtern = true;
                } else {
                    return 0;
                }
            }
        }
        return $hasExtern ? 1 : 0;
    }

    private function statistique23(Patient $patient, $rendezVous, $dateStart, $dateEnd): int
    {
        return 0;
    }

    private function statistique24(Patient $patient, $rendezVous, $dateStart, $dateEnd): int
    {
        $deDate = $this->getDiagnosticEducatifDate($patient, $rendezVous, $dateStart, $dateEnd);
        if ($deDate === null) {
            return 0;
        }

        // Complément de 2.1 et 2.2 : tout ce qui n'est ni purement Hospit ni purement externe
        $allHospit = true;
        $hasHospit = false;
        $allExtern = true;
        $hasExtern = false;

        foreach ($rendezVous as $r) {
            if (
                $r->getEtat() === "Oui"
                && $this->isInDateRange($deDate, $dateEnd, $r->getDate())
            ) {
                if ($r->getType() === "Hospit") {
                    $hasHospit = true;
                } else {
                    $allHospit = false;
                }
                if ($r->getType() === "Ambu" || $r->getType() === "Tel") {
                    $hasExtern = true;
                } else {
                    $allExtern = false;
                }
            }
        }

        if ($hasHospit && $allHospit) return 0; // compté en 2.1
        if ($hasExtern && $allExtern) return 0; // compté en 2.2
        return 1;
    }

    private function statistique25(Patient $patient, $rendezVous, $dateStart, $dateEnd): int
    {
        return 0;
    }

    /* */

    private function statistique26($rendezVous, $dateStart, $dateEnd): int
    {
        $ret = 0;
        foreach ($rendezVous as $r) {
            if (
                $r->getEtat() === "Oui"
                && $this->isInDateRange($dateStart, $dateEnd, $r->getDate())
                && ($r->getCategorie() === "Entretien" || $r->getCategorie() === "Consultation" || $r->getCategorie() === "Coaching")
            ) {
                $ret += 1;
            }
        }
        return $ret;
    }

    private function statistique26bis($rendezVous, $dateStart, $dateEnd): int
    {
        $ret = 0;
        foreach ($rendezVous as $r) {
            if (
                $r->getEtat() === "Oui"
                && $this->isInDateRange($dateStart, $dateEnd, $r->getDate())
                && ($r->getCategorie() === "Entretien" || $r->getCategorie() === "Consultation" || $r->getCategorie() === "Coaching")
                && $r->getType() === "Tel"
            ) {
                $ret += 1;
            }
        }
        return $ret;
    }

    private function statistique27(array $slots, $dateStart, $dateEnd): int
    {
        $atelier = 0;
        foreach ($slots as $s) {
            $rendezVous = $s->getRendezVous();
            foreach ($rendezVous as $r) {
                if (
                    $r->getEtat() === "Oui"
                    && $this->isInDateRange($dateStart, $dateEnd, $s->getDate())
                    && $r->getCategorie() === "Atelier"
                ) {
                    $atelier += 1;
                }
            }
        }
        return $atelier;
    }

    private function statistique27bis(array $slots, $dateStart, $dateEnd): int
    {
        return 0;
    }

    private function statistique28(int $totalSeance, array $slots, $dateStart, $dateEnd): float
    {
        $totalSlotEducative = 0;
        $totalSlotAtelier = 0;
        foreach ($slots as $s) {
            if ($s->getCategorie() === 'Educative') {
                $totalSlotEducative++;
            }
            if ($s->getCategorie() === 'Atelier') {
                $totalSlotAtelier++;
            }
        }

        $total = (($totalSlotEducative / 3) * 10) + $totalSlotAtelier;

        return $total > 0 ? round($totalSeance / $total, 2) : 0;
    }

    private function statistique29($rendezVous, $dateStart, $dateEnd): int
    {
        $ret = 0;
        foreach ($rendezVous as $r) {
            if (
                $r->getEtat() === "Oui"
                && $this->isInDateRange($dateStart, $dateEnd, $r->getDate())
                && $r->getAccompagnant() !== null
                && $r->getAccompagnant() !== ""
            ) {
                $ret++;
            }
        }
        return $ret;
    }

    private function statistique210($rendezVous, $dateStart, $dateEnd): int
    {
        $ret = 0;
        foreach ($rendezVous as $r) {
            if (
                $r->getEtat() === "Oui"
                && $this->isInDateRange($dateStart, $dateEnd, $r->getDate())
                && $r->getAccompagnant() !== null
                && $r->getAccompagnant() !== ""
            ) {
                $ret += 1;
            }
        }
        return $ret;
    }

    private function statistique211($rendezVous, $dateStart, $dateEnd): int
    {
        return 0;
    }

    /**
     * 3 - Sortie du programme
     */
    private function statistique31(Patient $patient, $rendezVous, $dateStart, $dateEnd): int
    {
        $deDate = $this->getDiagnosticEducatifDate($patient, $rendezVous, $dateStart, $dateEnd);
        if ($deDate === null) {
            return 0;
        }

        $seance = false;
        $reactu = false;
        foreach ($rendezVous as $r) {
            if (
                $r->getEtat() === "Oui"
                && $this->isInDateRange($deDate, $dateEnd, $r->getDate())
            ) {
                if (
                    $r->getCategorie() === "Entretien"
                    && substr_compare($r->getThematique(), "Réactu", 0, 5) === 0
                ) {
                    $reactu = true;
                } else {
                    $seance = true;
                }
            }
        }
        return $seance && $reactu ? 1 : 0;
    }

    private function statistique32(Patient $patient, $rendezVous, $dateStart, $dateEnd): int
    {
        $deDate = $this->getDiagnosticEducatifDate($patient, $rendezVous, $dateStart, $dateEnd);
        if ($deDate === null) {
            return 0;
        }

        $seance = false;
        $reactu = false;
        foreach ($rendezVous as $r) {
            if (
                $r->getEtat() === "Oui"
                && $this->isInDateRange($deDate, $dateEnd, $r->getDate())
            ) {
                if ($r->getType() !== "Hospit") {
                    return 0;
                }
                if (
                    $r->getCategorie() === "Entretien"
                    && substr_compare($r->getThematique(), "Réactu", 0, 5) === 0
                ) {
                    $reactu = true;
                } else {
                    $seance = true;
                }
            }
        }
        return $seance && $reactu ? 1 : 0;
    }

    private function statistique33(Patient $patient, $rendezVous, $dateStart, $dateEnd): int
    {
        $deDate = $this->getDiagnosticEducatifDate($patient, $rendezVous, $dateStart, $dateEnd);
        if ($deDate === null) {
            return 0;
        }

        $seance = false;
        $reactu = false;
        foreach ($rendezVous as $r) {
            if (
                $r->getEtat() === "Oui"
                && $this->isInDateRange($deDate, $dateEnd, $r->getDate())
            ) {
                if ($r->getType() !== "Ambu") {
                    return 0;
                }
                if (
                    $r->getCategorie() === "Entretien"
                    && substr_compare($r->getThematique(), "Réactu", 0, 5) === 0
                ) {
                    $reactu = true;
                } else {
                    $seance = true;
                }
            }
        }
        return $seance && $reactu ? 1 : 0;
    }

    private function statistique34(Patient $patient, $rendezVous, $dateStart, $dateEnd): int
    {
        return $this->statistique31($patient, $rendezVous, $dateStart, $dateEnd);
    }

    private function statistique35(Patient $patient, $rendezVous, $dateStart, $dateEnd): int
    {
        return 0;
    }

    private function statistique36(Patient $patient, $rendezVous, $dateStart, $dateEnd): int
    {
        foreach ($rendezVous as $r) {
            if (
                $r->getEtat() === "Oui"
                && $this->isInDateRange($dateStart, $dateEnd, $r->getDate())
                && $r->getCategorie() === "Entretien"
                && substr_compare($r->getThematique(), "Réactu", 0, 5) === 0
            ) {
                return 1;
            }
        }
        return 0;
    }

    /**
     * 4 - Modalités de suivi - Coordination du parcours de soins
     */
    private function isReactu($r): bool
    {
        return $r->getCategorie() === "Entretien"
            && in_array($r->getThematique(), ["Réactu 1", "Réactu 2", "Réactu 3", "Réactu 4", "Réactu 5"]);
    }

    private function statistique41(Patient $patient, $rendezVous, $dateStart, $dateEnd): int
    {
        $deDate = $this->getDiagnosticEducatifDate($patient, $rendezVous, $dateStart, $dateEnd);
        if ($deDate === null) {
            return 0;
        }

        $seance = 0;
        foreach ($rendezVous as $r) {
            if (
                $r->getEtat() === "Oui"
                && $this->isInDateRange($deDate, $dateEnd, $r->getDate())
            ) {
                $seance++;
            }
        }
        return $seance >= 3 ? 1 : 0;
    }

    /**
     * "Finir par une Réactu" : il existe une Réactu avec au moins un rdv Oui avant elle.
     */
    private function hasReactuPrecededByRdv($rendezVous, $deDate, $dateEnd): bool
    {
        $earliestTs = null;
        $reactuTimestamps = [];
        foreach ($rendezVous as $r) {
            if ($r->getEtat() !== "Oui" || !$this->isInDateRange($deDate, $dateEnd, $r->getDate())) continue;
            $ts = $r->getDate()->getTimestamp();
            if ($earliestTs === null || $ts < $earliestTs) $earliestTs = $ts;
            if ($this->isReactu($r)) $reactuTimestamps[] = $ts;
        }
        if (!$reactuTimestamps || $earliestTs === null) return false;
        foreach ($reactuTimestamps as $rts) {
            if ($rts > $earliestTs) return true;
        }
        return false;
    }

    /**
     * "Commencer par une Réactu" : il existe une Réactu avec au moins un rdv Oui après elle.
     */
    private function hasReactuFollowedByRdv($rendezVous, $deDate, $dateEnd): bool
    {
        $latestTs = null;
        $reactuTimestamps = [];
        foreach ($rendezVous as $r) {
            if ($r->getEtat() !== "Oui" || !$this->isInDateRange($deDate, $dateEnd, $r->getDate())) continue;
            $ts = $r->getDate()->getTimestamp();
            if ($latestTs === null || $ts > $latestTs) $latestTs = $ts;
            if ($this->isReactu($r)) $reactuTimestamps[] = $ts;
        }
        if (!$reactuTimestamps || $latestTs === null) return false;
        foreach ($reactuTimestamps as $rts) {
            if ($rts < $latestTs) return true;
        }
        return false;
    }

    private function statistique41bis(Patient $patient, $rendezVous, $dateStart, $dateEnd): int
    {
        $deDate = $this->getDiagnosticEducatifDate($patient, $rendezVous, $dateStart, $dateEnd);
        if ($deDate === null) return 0;

        $reactuTs = [];
        $allTs = [];
        foreach ($rendezVous as $r) {
            if ($r->getEtat() !== "Oui" || !$this->isInDateRange($deDate, $dateEnd, $r->getDate())) continue;
            $ts = $r->getDate()->getTimestamp();
            $allTs[] = $ts;
            if ($this->isReactu($r)) $reactuTs[] = $ts;
        }
        if (empty($reactuTs)) return 0;

        // Au moins 3 rdvs Oui avant une Réactu
        $lastReactu = max($reactuTs);
        $before = 0;
        foreach ($allTs as $ts) {
            if ($ts < $lastReactu) $before++;
        }
        return $before >= 3 ? 1 : 0;
    }

    private function statistique43(Patient $patient, $rendezVous, $dateStart, $dateEnd): int
    {
        $deDate = $this->getDiagnosticEducatifDate($patient, $rendezVous, $dateStart, $dateEnd);
        if ($deDate === null) return 0;

        $reactuTs = [];
        $allTs = [];
        foreach ($rendezVous as $r) {
            if ($r->getEtat() !== "Oui" || !$this->isInDateRange($deDate, $dateEnd, $r->getDate())) continue;
            $ts = $r->getDate()->getTimestamp();
            $allTs[] = $ts;
            if ($this->isReactu($r)) $reactuTs[] = $ts;
        }
        if (empty($reactuTs)) return 0;

        // Au moins 3 rdvs Oui après une Réactu
        $firstReactu = min($reactuTs);
        $after = 0;
        foreach ($allTs as $ts) {
            if ($ts > $firstReactu) $after++;
        }
        return $after >= 3 ? 1 : 0;
    }

    private function statistique43bis(Patient $patient, $rendezVous, $dateStart, $dateEnd): int
    {
        $deDate = $this->getDiagnosticEducatifDate($patient, $rendezVous, $dateStart, $dateEnd);
        if ($deDate === null) return 0;

        $reactuTs = [];
        $allTs = [];
        foreach ($rendezVous as $r) {
            if ($r->getEtat() !== "Oui" || !$this->isInDateRange($deDate, $dateEnd, $r->getDate())) continue;
            $ts = $r->getDate()->getTimestamp();
            $allTs[] = $ts;
            if ($this->isReactu($r)) $reactuTs[] = $ts;
        }
        if (count($reactuTs) < 2) return 0;

        $firstReactu = min($reactuTs);
        $lastReactu = max($reactuTs);
        if ($firstReactu >= $lastReactu) return 0;

        $between = 0;
        foreach ($allTs as $ts) {
            if ($ts > $firstReactu && $ts < $lastReactu) $between++;
        }
        return $between >= 3 ? 1 : 0;
    }

    private function statistique44(Patient $patient, $rendezVous, $dateStart, $dateEnd): int
    {
        return 0;
    }

    /**
     *
     */
    private function isInDateRange($dateStart, $dateEnd, $date): bool
    {
        if (!$date) {
            return false;
        }
        $start_ts = $dateStart->getTimestamp();
        $end_ts = $dateEnd->getTimestamp();
        $user_ts = $date->getTimestamp();
        return (($user_ts >= $start_ts) && ($user_ts <= $end_ts));
    }
}

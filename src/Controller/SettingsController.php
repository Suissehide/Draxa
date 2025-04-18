<?php

namespace App\Controller;

use App\Constant\ThematiqueConstants;

use App\Entity\Semaine;
use App\Entity\Slot;
use App\Entity\Soignant;

use App\Form\SemaineType;
use App\Form\SlotType;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SettingsController extends AbstractController
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
     * @Route("/settings/{year}", name="settings")
     */
    public function settings(int $year): Response
    {
        $semaine = new Semaine();
        $semaineForm = $this->createForm(SemaineType::class, $semaine);

        $slot = new Slot();
        $slotForm = $this->createForm(SlotType::class, $slot);

        $consultations = [];
        foreach (ThematiqueConstants::CONSULTATION as $consultation) { $consultations[] = $consultation; }
        $entretiens = [];
        foreach (ThematiqueConstants::ENTRETIEN as $entretien) { $entretiens[] = $entretien; }
        $ateliers = [];
        foreach (ThematiqueConstants::ATELIER as $atelier) { $ateliers[] = $atelier; }
        $coachings = [];
        foreach (ThematiqueConstants::COACHING as $coaching) { $coachings[] = $coaching; }
        $educatives = [];
        $thematiques = array( $consultations, $entretiens, $ateliers, $coachings, $educatives );

        return $this->render('settings/index.html.twig', [
            'title' => 'Settings',
            'controller_name' => 'SettingsController',
            'semaines' => $this->em->getRepository(Semaine::class)->findByYear($year),
            'semaineForm' => $semaineForm->createView(),
            'slotForm' => $slotForm->createView(),
            'dates_semaines' => $this->em->getRepository(Semaine::class)->findAllDates(),
            'year' => $year,
            'thematiques' => $thematiques
        ]);
    }
}

<?php

namespace App\Controller;

use App\Constant\ThematiqueConstants;
use App\Entity\Semaine;
use App\Entity\Slot;
use App\Form\SemaineType;
use App\Form\SlotType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SettingsController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    #[Route(path: '/settings/{year}', name: 'settings', methods: 'GET')]
    public function settings(int $year): Response
    {
        $semaineForm = $this->createForm(SemaineType::class, new Semaine());
        $slotForm = $this->createForm(SlotType::class, new Slot());

        return $this->render('settings/index.html.twig', [
            'title' => 'Settings',
            'controller_name' => 'SettingsController',
            'semaines' => $this->em->getRepository(Semaine::class)->findByYear($year),
            'semaineForm' => $semaineForm->createView(),
            'slotForm' => $slotForm->createView(),
            'dates_semaines' => $this->em->getRepository(Semaine::class)->findAllDates(),
            'year' => $year,
            'thematiques' => ThematiqueConstants::lists(),
        ]);
    }
}

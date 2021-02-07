<?php

namespace App\Controller;

use App\Entity\Semaine;
use App\Entity\Slot;

use App\Form\SemaineType;
use App\Form\SlotType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SettingController extends AbstractController
{
    /**
     * @Route("/settings", name="settings")
     */
    public function index(): Response
    {
        $em = $this->getDoctrine()->getManager();

        $semaine = new Semaine();
        $semaineForm = $this->createForm(SemaineType::class, $semaine);

        $slot = new Slot();
        $slotForm = $this->createForm(SlotType::class, $slot);

        return $this->render('setting/index.html.twig', [
            'title' => 'Settings',
            'controller_name' => 'SettingController',
            'semaines' => $em->getRepository(Semaine::class)->findBy([], ['dateDebut' => 'ASC']),
            'semaineForm' => $semaineForm->createView(),
            'slotForm' => $slotForm->createView(),
        ]);
    }

    /**
     * @Route("/soignant", name="soignant")
     */
    public function soignant(): Response
    {
        $em = $this->getDoctrine()->getManager();

        $soignant = new Soignant();
        $soignantForm = $this->createForm(SoignantType::class, $soignant);

        return $this->render('setting/soignant.html.twig', [
            'title' => 'Soignants',
            'controller_name' => 'SoignantController',
            'soignants' => $em->getRepository(Soignant::class)->findAll(),
            'soignantForm' => $soignantForm->createView(),
        ]);
    }
}

<?php

namespace App\Controller;

use App\Entity\Semaine;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/annee")
 */
class YearController extends AbstractController
{
    /**
     * @Route("/", name="year")
     */
    public function index(): Response
    {
        $em = $this->getDoctrine()->getManager();

        $semaines = $em->getRepository(Semaine::class)->findAll();
        $data = [];
        foreach($semaines as $semaine) {
            $data[] = [
                'startDate' => $semaine->getDateDebut()->format('d/m/Y'),
                'endDate' => $semaine->getDateFin()->format('d/m/Y'),
                'color' => count($semaine->getSlots()) > 0 ? '#07a8a0' : 'orange',
            ];
        }

        return $this->render('year/index.html.twig', [
            'title' => 'Years',
            'data' => $data,
            'controller_name' => 'YearController',
        ]);
    }
}

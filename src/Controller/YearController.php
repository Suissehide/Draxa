<?php

namespace App\Controller;

use App\Entity\Semaine;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/annee')]
class YearController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    #[Route(path: '/', name: 'year', methods: 'GET')]
    public function index(): Response
    {
        $semaines = $this->em->getRepository(Semaine::class)->findAll();
        $data = [];
        foreach ($semaines as $semaine) {
            $data[] = [
                'startDate' => $semaine->getDateDebut()->format('d/m/Y'),
                'endDate' => $semaine->getDateFin()->format('d/m/Y'),
                'color' => !$semaine->getSlots()->isEmpty() ? '#0a6a96' : '#f0ad4e',
            ];
        }

        return $this->render('year/index.html.twig', [
            'title' => 'Years',
            'data' => $data,
            'controller_name' => 'YearController',
        ]);
    }
}

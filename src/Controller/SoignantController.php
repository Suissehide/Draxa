<?php

namespace App\Controller;

use App\Entity\Soignant;
use App\Form\SoignantType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/soignant')]
class SoignantController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    #[Route(path: '/', name: 'soignant', methods: 'GET')]
    public function index(): Response
    {
        return $this->render('soignant/index.html.twig', [
            'title' => 'Soignants',
            'controller_name' => 'SoignantController',
            'soignants' => $this->em->getRepository(Soignant::class)->findAll(),
            'soignantForm' => $this->createForm(SoignantType::class, new Soignant())->createView(),
        ]);
    }

    #[Route(path: '/add', name: 'soignant_add', methods: 'POST')]
    public function add(Request $request): Response
    {
        if (!$request->isXmlHttpRequest()) {
            return new JsonResponse(null, Response::HTTP_BAD_REQUEST);
        }

        $soignant = (new Soignant())
            ->setNom($request->get('nom'))
            ->setPrenom($request->get('prenom'))
            ->setStatus(true);
        $this->em->persist($soignant);
        $this->em->flush();

        return new JsonResponse($soignant->getId());
    }

    #[Route(path: '/{id}', name: 'soignant_delete', methods: 'DELETE')]
    public function delete(Request $request, int $id): Response
    {
        if (!$request->isXmlHttpRequest()) {
            return new JsonResponse(null, Response::HTTP_BAD_REQUEST);
        }

        $soignant = $this->em->getRepository(Soignant::class)->find($id);
        if (!$soignant) {
            return new JsonResponse(false);
        }

        foreach ($soignant->getSlots() as $slot) {
            $slot->setSoignant(null);
        }
        $this->em->remove($soignant);
        $this->em->flush();

        return new JsonResponse(true);
    }

    /**
     * Active (en fin de liste de priorité) ou désactive des soignants.
     */
    #[Route(path: '/update', name: 'status_update', methods: 'POST')]
    public function update(Request $request): Response
    {
        if (!$request->isXmlHttpRequest()) {
            return new JsonResponse(null, Response::HTTP_BAD_REQUEST);
        }

        $active = $request->get('status') === 'true';
        $repository = $this->em->getRepository(Soignant::class);
        $priority = $repository->getPriorityMax();

        // L'ordre reçu détermine l'ordre de priorité.
        foreach ((array) $request->get('soignants') as $soignantId) {
            $soignant = $repository->find((int) $soignantId);
            if (!$soignant) {
                continue;
            }
            $soignant->setStatus($active);
            $soignant->setPriority($active ? ++$priority : 0);
        }
        $this->em->flush();

        return new JsonResponse(true);
    }

    #[Route(path: '/priority/swap', name: 'status_priority_swap', methods: 'POST')]
    public function swap(Request $request): Response
    {
        if (!$request->isXmlHttpRequest()) {
            return new JsonResponse(null, Response::HTTP_BAD_REQUEST);
        }

        $repository = $this->em->getRepository(Soignant::class);
        $soignant1 = $repository->find((int) $request->get('soignant1'));
        $soignant2 = $repository->find((int) $request->get('soignant2'));
        if (!$soignant1 || !$soignant2) {
            return new JsonResponse(false, Response::HTTP_NOT_FOUND);
        }

        $priority = $soignant1->getPriority();
        $soignant1->setPriority($soignant2->getPriority());
        $soignant2->setPriority($priority);
        $this->em->flush();

        return new JsonResponse(true);
    }
}

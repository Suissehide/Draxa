<?php

namespace App\Controller;

use App\Entity\Soignant;
use App\Form\SoignantType;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("/soignant")
 */
class SoignantController extends AbstractController
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
     * @Route("/", name="soignant")
     */
    public function index(): Response
    {
        $soignant = new Soignant();
        $soignantForm = $this->createForm(SoignantType::class, $soignant);

        return $this->render('soignant/index.html.twig', [
            'title' => 'Soignants',
            'controller_name' => 'SoignantController',
            'soignants' => $this->em->getRepository(Soignant::class)->findAll(),
            'soignantForm' => $soignantForm->createView(),
        ]);
    }

    /**
     * @Route("/add", name="soignant_add", methods="POST")
     */
    public function add(Request $request) : Response
    {
        if ($request->isXmlHttpRequest()) {
            $nom = $request->get('nom');
            $prenom = $request->get('prenom');

            $soignant = new Soignant();
            $soignant->setNom($nom);
            $soignant->setPrenom($prenom);
            $soignant->setStatus(true);
            $this->em->persist($soignant);
            $this->em->flush();

            return new JsonResponse($soignant->getId());
        }
    }

    /**
     * @Route("/{id}", name="soignant_delete", methods="DELETE")
     */
    public function delete(Request $request, ManagerRegistry $doctrine, int $id) : Response
    {
        $soignant = $doctrine->getRepository(Soignant::class)->find($id);
        if ($request->isXmlHttpRequest()) {
            if ($soignant) {
                $this->em->remove($soignant);
                $this->em->flush();
                return new JsonResponse(true);
            }
            return new JsonResponse(false);
        }
    }

    /**
     * @Route("/update", name="status_update", methods="POST")
     */
    public function update(Request $request) : Response
    {
        if ($request->isXmlHttpRequest()) {
            $soignantsId = $request->get('soignants');
            $status = $request->get('status');
            $priorityMax = $this->em->getRepository(Soignant::class)->getPriorityMax();

            dump($priorityMax);

            foreach ($soignantsId as $soignantId) {
                $soignant = $this->em->getRepository(Soignant::class)->findOneBy(array('id' => $soignantId));
                $soignant->setStatus($status === 'true' ? 1 : 0);
                if ($status === 'true') {
                    $priorityMax += 1;
                    $soignant->setPriority($priorityMax);
                } else {
                    $soignant->setPriority(0);
                }
            }
            $this->em->flush();
            return new JsonResponse(true);
        }
    }

    /**
     * @Route("/priority/set", name="status_priority_set", methods="POST")
     */
    public function priority(Request $request) : Response
    {
        if ($request->isXmlHttpRequest()) {
            $soignantsId = $request->get('soignants');
            $priorityMax = $this->em->getRepository(Soignant::class)->getPriorityMax();
            
            foreach ($soignantsId as $soignantId) {
                $priorityMax += 1;
                $soignant = $this->em->getRepository(Soignant::class)->findOneBy(array('id' => $soignantId));
                $soignant->setPriority($priorityMax);
            }
            $this->em->flush();
            return new JsonResponse(true);
        }
    }

    
    /**
     * @Route("/priority/swap", name="status_priority_swap", methods="POST")
     */
    public function swap(Request $request) : Response
    {
        if ($request->isXmlHttpRequest()) {
            $soignantId1 = $request->get('soignant1');
            $soignantId2 = $request->get('soignant2');

            $soignant1 = $this->em->getRepository(Soignant::class)->findOneBy(array('id' => $soignantId1));
            $soignant2 = $this->em->getRepository(Soignant::class)->findOneBy(array('id' => $soignantId2));
            $priority_temp = $soignant1->getPriority();
            $soignant1->setPriority($soignant2->getPriority());
            $soignant2->setPriority($priority_temp);

            $this->em->flush();
            return new JsonResponse(true);
        }
    }

    /**
     * @Route("/priority/reset", name="status_priority_reset", methods="POST")
     */
    public function reset(Request $request) : Response
    {
        if ($request->isXmlHttpRequest()) {
            $soignantsId = $request->get('soignants');

            foreach ($soignantsId as $soignantId) {
                $soignant = $this->em->getRepository(Soignant::class)->findOneBy(array('id' => $soignantId));
                $soignant->setPriority(0);
            }
            $this->em->flush();
            return new JsonResponse(true);
        }
    }
}

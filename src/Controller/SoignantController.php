<?php

namespace App\Controller;

use App\Entity\Soignant;
use App\Form\SoignantType;

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
     * @Route("/", name="soignant")
     */
    public function index(): Response
    {
        $em = $this->getDoctrine()->getManager();

        $soignant = new Soignant();
        $soignantForm = $this->createForm(SoignantType::class, $soignant);

        return $this->render('soignant/index.html.twig', [
            'title' => 'Soignants',
            'controller_name' => 'SoignantController',
            'soignants' => $em->getRepository(Soignant::class)->findAll(),
            'soignantForm' => $soignantForm->createView(),
        ]);
    }

    /**
     * @Route("/add", name="soignant_add", methods="POST")
     */
    public function add(Request $request) : Response
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();

            $nom = $request->request->get('nom');
            $prenom = $request->request->get('prenom');

            $soignant = new Soignant();
            $soignant->setNom($nom);
            $soignant->setPrenom($prenom);
            $soignant->setStatus(true);
            $em->persist($soignant);
            $em->flush();

            return new JsonResponse($soignant->getId());
        }
    }

    /**
     * @Route("/{id}", name="soignant_delete", methods="DELETE")
     */
    public function delete(Request $request, Soignant $soignant) : Response
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            if ($soignant) {
                $em->remove($soignant);
                $em->flush();
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
            $em = $this->getDoctrine()->getManager();

            $soignantsId = $request->request->get('soignants');
            $status = $request->request->get('status');
            $priorityMax = $em->getRepository(Soignant::class)->getPriorityMax();

            dump($priorityMax);

            foreach ($soignantsId as $soignantId) {
                $soignant = $em->getRepository(Soignant::class)->findOneBy(array('id' => $soignantId));
                $soignant->setStatus($status == 'true' ? 1 : 0);
                if ($status == 'true') {
                    $priorityMax += 1;
                    $soignant->setPriority($priorityMax);
                } else {
                    $soignant->setPriority(0);
                }
            }
            $em->flush();
            return new JsonResponse(true);
        }
    }

    /**
     * @Route("/priority/set", name="status_priority_set", methods="POST")
     */
    public function priority(Request $request) : Response
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();

            $soignantsId = $request->request->get('soignants');
            $priorityMax = $em->getRepository(Soignant::class)->getPriorityMax();
            
            foreach ($soignantsId as $soignantId) {
                $priorityMax += 1;
                $soignant = $em->getRepository(Soignant::class)->findOneBy(array('id' => $soignantId));
                $soignant->setPriority($priorityMax);
            }
            $em->flush();
            return new JsonResponse(true);
        }
    }

    
    /**
     * @Route("/priority/swap", name="status_priority_swap", methods="POST")
     */
    public function swap(Request $request) : Response
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();

            $soignantId1 = $request->request->get('soignant1');
            $soignantId2 = $request->request->get('soignant2');

            $soignant1 = $em->getRepository(Soignant::class)->findOneBy(array('id' => $soignantId1));
            $soignant2 = $em->getRepository(Soignant::class)->findOneBy(array('id' => $soignantId2));
            $priority_temp = $soignant1->getPriority();
            $soignant1->setPriority($soignant2->getPriority());
            $soignant2->setPriority($priority_temp);

            $em->flush();
            return new JsonResponse(true);
        }
    }

    /**
     * @Route("/priority/reset", name="status_priority_reset", methods="POST")
     */
    public function reset(Request $request) : Response
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();

            $soignantsId = $request->request->get('soignants');

            foreach ($soignantsId as $soignantId) {
                $soignant = $em->getRepository(Soignant::class)->findOneBy(array('id' => $soignantId));
                $soignant->setPriority(0);
            }
            $em->flush();
            return new JsonResponse(true);
        }
    }
}

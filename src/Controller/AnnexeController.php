<?php

namespace App\Controller;

use App\Entity\Annexe;
use App\Form\AnnexeType;
use App\Repository\AnnexeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("/annexe")
 */
class AnnexeController extends Controller
{
    /**
     * @Route("/", name="annexe_index", methods="GET")
     */
    public function index(AnnexeRepository $annexeRepository): Response
    {
        return $this->render('annexe/index.html.twig', ['annexes' => $annexeRepository->findAll()]);
    }

    /**
     * @Route("/new", name="annexe_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $annexe = new Annexe();
        $form = $this->createForm(AnnexeType::class, $annexe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($annexe);
            $em->flush();

            return $this->redirectToRoute('annexe_index');
        }

        return $this->render('annexe/new.html.twig', [
            'annexe' => $annexe,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="annexe_show", methods="GET")
     */
    public function show(Annexe $annexe): Response
    {
        return $this->render('annexe/show.html.twig', ['annexe' => $annexe]);
    }

    /**
     * @Route("/{id}/edit", name="annexe_edit", methods="GET|POST")
     */
    public function edit(Request $request, Annexe $annexe): Response
    {
        $form = $this->createForm(AnnexeType::class, $annexe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('annexe_edit', ['id' => $annexe->getId()]);
        }

        return $this->render('annexe/edit.html.twig', [
            'annexe' => $annexe,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="annexe_delete", methods="DELETE")
     */
    public function delete(Request $request, Annexe $annexe): Response
    {
        if ($this->isCsrfTokenValid('delete'.$annexe->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($annexe);
            $em->flush();
        }

        return $this->redirectToRoute('annexe_index');
    }

    /**
     * @Route("/remove/{id}", name="annexe_remove", methods="DELETE")
     */
    public function remove(Request $request, Annexe $annexe): Response
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            if ($annexe) {
                $em->remove($annexe);
                $em->flush();
                return new JsonResponse(true);
            }
        }
        return new JsonResponse(false);
    }
}

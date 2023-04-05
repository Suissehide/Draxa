<?php

namespace App\Controller;

use App\Entity\Todo;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("/todo")
 */
class TodoController extends AbstractController
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
     * @Route("/getTodos", name="todo_get", methods="GET")
     */
    public function getTodos(Request $request): Response
    {
        $todoObjects = $this->em->getRepository(Todo::class)->findAll();

        if ($request->isXmlHttpRequest()) {
            $todos = [];
            foreach($todoObjects as $t) {
                $todos[] = [
                    'id' => $t->getId(),
                    'enable' => $t->getEnable(),
                    'description' => $t->getDescription(),
                    'datetime' => $t->getDatetime()->format('d/m/Y H:i'),
                ];
            }

            return new JsonResponse($todos);
        }
    }

    
    /**
     * @Route("/add", name="todo_add", methods="GET|POST")
     */
    public function add(Request $request): Response
    {
        if ($request->isXmlHttpRequest()) {
            $todo = new Todo();

            $description = $request->get('description');

            $todo->setEnable(false);
            $todo->setDatetime(new \DateTime());
            $todo->setDescription($description);

            $this->em->persist($todo);
            $this->em->flush();

            return new JsonResponse($todo->getId());
        }
    }

    /**
     * @Route("/remove", name="todo_remove", methods="GET|POST")
     */
    public function remove(Request $request): Response
    {
        if ($request->isXmlHttpRequest()) {
            $taskId = $request->get('taskId');

            $todo = $this->em->getRepository(Todo::class)->find($taskId);

            $this->em->remove($todo);
            $this->em->flush();

            return new JsonResponse(true);
        }
    }

    /**
     * @Route("/clear", name="todo_clear", methods="GET|POST")
     */
    public function clear(Request $request): Response
    {
        if ($request->isXmlHttpRequest()) {
            $todos = $this->em->getRepository(Todo::class)->findAll();
            foreach($todos as $todo) {
                $this->em->remove($todo);
            }
            $this->em->flush();

            return new JsonResponse(true);
        }
    }

    /**
     * @Route("/enable", name="todo_enable", methods="GET|POST")
     */
    public function enable(Request $request): Response
    {
        if ($request->isXmlHttpRequest()) {
            $enable = $request->get('enable');
            $taskId = $request->get('taskId');

            $todo = $this->em->getRepository(Todo::class)->find($taskId);

            $todo->setEnable($enable === "true");
            $this->em->flush();

            return new JsonResponse(true);
        }
    }
    
    /**
     * @Route("/edit", name="todo_edit", methods="GET|POST")
     */
    public function edit(Request $request): Response
    {
        if ($request->isXmlHttpRequest()) {
            $description = $request->get('description');
            $taskId = $request->get('taskId');

            $todo = $this->em->getRepository(Todo::class)->find($taskId);
            $todo->setDescription($description);
            $this->em->flush();

            return new JsonResponse(true);
        }
    }
}

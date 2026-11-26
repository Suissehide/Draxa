<?php

namespace App\Controller;

use App\Entity\Todo;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/todo')]
class TodoController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    #[Route(path: '/getTodos', name: 'todo_get', methods: 'GET')]
    public function getTodos(): JsonResponse
    {
        $todos = [];
        foreach ($this->em->getRepository(Todo::class)->findAll() as $todo) {
            $todos[] = [
                'id' => $todo->getId(),
                'enable' => $todo->getEnable(),
                'description' => $todo->getDescription(),
                'datetime' => $todo->getDatetime()?->format('d/m/Y H:i'),
            ];
        }

        return new JsonResponse($todos);
    }

    #[Route(path: '/add', name: 'todo_add', methods: 'POST')]
    public function add(Request $request): JsonResponse
    {
        $todo = (new Todo())
            ->setEnable(false)
            ->setDatetime(new \DateTime())
            ->setDescription($request->get('description'));

        $this->em->persist($todo);
        $this->em->flush();

        return new JsonResponse($todo->getId());
    }

    #[Route(path: '/remove', name: 'todo_remove', methods: 'POST')]
    public function remove(Request $request): JsonResponse
    {
        $this->em->remove($this->findTodo($request));
        $this->em->flush();

        return new JsonResponse(true);
    }

    #[Route(path: '/clear', name: 'todo_clear', methods: 'POST')]
    public function clear(): JsonResponse
    {
        $this->em->createQuery('DELETE FROM App\Entity\Todo t')->execute();

        return new JsonResponse(true);
    }

    #[Route(path: '/enable', name: 'todo_enable', methods: 'POST')]
    public function enable(Request $request): JsonResponse
    {
        $this->findTodo($request)->setEnable($request->get('enable') === 'true');
        $this->em->flush();

        return new JsonResponse(true);
    }

    #[Route(path: '/edit', name: 'todo_edit', methods: 'POST')]
    public function edit(Request $request): JsonResponse
    {
        $this->findTodo($request)->setDescription($request->get('description'));
        $this->em->flush();

        return new JsonResponse(true);
    }

    private function findTodo(Request $request): Todo
    {
        return $this->em->getRepository(Todo::class)->find((int) $request->get('taskId'))
            ?? throw $this->createNotFoundException();
    }
}

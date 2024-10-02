<?php

namespace App\Controller;

use App\Entity\Tache;
use App\Repository\TacheRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/tache')]
class TacheApiController extends AbstractController
{
    #[Route('', methods: ['GET'])]
    public function index(TacheRepository $tacheRepository): JsonResponse
    {
        // Fetch all tasks and serialize them using the "tache:read" group
        $tasks = $tacheRepository->findAll();
        return $this->json($tasks, 200, [], ['groups' => 'tache:read']);
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        // Decode JSON from the request
        $data = json_decode($request->getContent(), true);
    
        // Check if $data is null or does not contain required fields
        if ($data === null || !isset($data['titre'], $data['description'], $data['terminee'])) {
            return $this->json(['error' => 'Invalid JSON input'], 400);
        }
        
        // Retrieve the current user
        $user = $this->getUser();
        
        // Check if the user is authenticated
        if (!$user) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }
    
        // Create a new Tache entity
        $task = new Tache();
        $task->setTitre($data['titre']);
        $task->setDescription($data['description']);
        $task->setTerminee($data['terminee']);
        $task->setUserId($user->getId()); // Set user ID directly
    
        // Persist the task
        $entityManager->persist($task);
        $entityManager->flush();
    
        return $this->json($task, 201, [], ['groups' => 'tache:read']);
    }
    

    #[Route('/taches/{id}', methods: ['PUT'])]
    public function update(Request $request, int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $task = $entityManager->getRepository(Tache::class)->find($id);
    
        if (!$task) {
            return $this->json(['error' => 'Task not found'], 404);
        }
    
        $data = json_decode($request->getContent(), true);
        
        if (isset($data['titre'])) {
            $task->setTitre($data['titre']);
        }
        if (isset($data['description'])) {
            $task->setDescription($data['description']);
        }
        if (isset($data['terminee'])) {
            $task->setTerminee($data['terminee']);
        }
    
        $entityManager->flush();
    
        return $this->json($task, 200, [], ['groups' => 'tache:read']);
    }
    

    #[Route('/delete/{id}', methods: ['DELETE'])]
    public function delete(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $task = $entityManager->getRepository(Tache::class)->find($id);
    
        if (!$task) {
            return new JsonResponse(['error' => 'Task not found'], 404);
        }
    
        $entityManager->remove($task);
        $entityManager->flush();
    
        return new JsonResponse(null, 204);
    }
    
}

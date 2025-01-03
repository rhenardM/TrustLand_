<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Owner;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
class OwnerController extends AbstractController
{
    #[Route('/api/owners', name: 'api_owners', methods: ['POST'])]
    public function createOwner(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        // Récupérer et décoder les données JSON
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return new JsonResponse(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
        }

        $name = $data['name'] ?? null;
        $firstName = $data['firstName'] ?? null;
        $dateOfBirth = $data['dateOfBirth'] ?? null;
        $userId = $data['user_id'] ?? null;

        // Vérifications des données
        if (!$name || !$firstName || !$dateOfBirth || !$userId) {
            return new JsonResponse(
                ['error' => 'Missing required fields: name, firstName, dateOfBirth, or user_id'],
                Response::HTTP_BAD_REQUEST
            );
        }
        try {
            $dateOfBirth = new \DateTime($dateOfBirth);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Invalid date format'], Response::HTTP_BAD_REQUEST);
        }
        // Rechercher l'utilisateur
        $user = $entityManager->getRepository(User::class)->find($userId);

        if (!$user) {
           // return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
            //$serializedOwner = $serializer->serialize($owner, 'json');
            return new JsonResponse(['message' => 'Owner created successfully.'], 
            Response::HTTP_CREATED);
        }
        // Créer et persister l'entité Owner
        $owner = new Owner();
        $owner->setName($name);
        $owner->setFirstName($firstName);
        $owner->setDateOfBirth($dateOfBirth);
        $owner->setUser($user);

        $entityManager->persist($owner);
        $entityManager->flush();

        return new JsonResponse(
            ['message' => 'Owner created successfully.', 'owner_id' => $owner->getId()],
            Response::HTTP_CREATED
        );
    }
}

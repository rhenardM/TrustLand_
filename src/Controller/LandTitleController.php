<?php

namespace App\Controller;

use App\Entity\Owner;
use App\Entity\Status;
use App\Entity\LandTitle;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LandTitleController extends AbstractController
{
    #[Route('api/land/title', name: 'api_land_title', methods: ['POST'])]
    public function createLandTitle(
        Request $request,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return new JsonResponse(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
        }

        // Hachage de la chaîne
        $hash = hash('sha256', json_encode($data));

        $titleNumber = $data['titleNumber'] ?? null;
        $description = $data['description'] ?? null;
        $issueDate = $data['issueDate'] ?? null;
        $expirationDate = $data['expirationDate'] ?? null;
        $ownerId = $data['owner_id'] ?? null;
        $statusId = $data['status_id'] ?? null;

        // Vérification des champs requis
        if (!$titleNumber || !$issueDate || !$expirationDate || !$description || !$ownerId || !$statusId) {
            return new JsonResponse(['error' => 'Missing required fields'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $issueDate = new \DateTime($issueDate);
            $expirationDate = new \DateTime($expirationDate);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Invalid date format'], Response::HTTP_BAD_REQUEST);
        }

        // Vérification de l'intégrité du hachage
        if (isset($data['hash']) && $data['hash'] !== $hash) {
            return new JsonResponse(['error' => 'Hash mismatch'], Response::HTTP_BAD_REQUEST);
        }

        // Rechercher l'entité Owner
        $owner = $entityManager->getRepository(Owner::class)->find($ownerId);
        if (!$owner) {
            return new JsonResponse(['error' => 'Owner not found'], Response::HTTP_NOT_FOUND);
        }

        // Rechercher l'entité Status
        $status = $entityManager->getRepository(Status::class)->find($statusId);
        if (!$status) {
            return new JsonResponse(['error' => 'Status not found'], Response::HTTP_NOT_FOUND);
        }

        // Création de l'objet LandTitle
        $landTitle = new LandTitle();
        $landTitle->setHash($hash);
        $landTitle->setTitleNumber($titleNumber);
        $landTitle->setDescription($description);
        $landTitle->setIssueDate($issueDate);
        $landTitle->setExpirationDate($expirationDate);
        $landTitle->setOwner($owner);
        $landTitle->setStatus($status);

        // Enregistrer dans la base de données
        $entityManager->persist($landTitle);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Land Title created successfully.'], Response::HTTP_CREATED);
    }
}

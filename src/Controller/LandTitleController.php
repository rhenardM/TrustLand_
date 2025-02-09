<?php

namespace App\Controller;

use App\Entity\Owner;
use App\Entity\Status;
use App\Entity\LandTitle;
use App\Service\BlockchainService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LandTitleController extends AbstractController
{
    private BlockchainService $blockchainService;

    public function __construct(BlockchainService $blockchainService)
    {
        $this->blockchainService = $blockchainService;
    }

    #[Route('/api/land/title', name: 'api_land_title', methods: ['POST'])]
    public function createLandTitle(
        Request $request,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return new JsonResponse(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
        }

        $titleNumber = $data['titleNumber'] ?? null;
        $description = $data['description'] ?? null;
        $issueDate = $data['issueDate'] ?? null;
        $expirationDate = $data['expirationDate'] ?? null;
        $ownerId = $data['owner_id'] ?? null;
        $statusId = $data['status_id'] ?? null;

        if (!$titleNumber || !$description || !$issueDate || !$expirationDate || !$ownerId || !$statusId) {
            return new JsonResponse(['error' => 'Missing required fields'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $issueDate = new \DateTime($issueDate);
            $expirationDate = new \DateTime($expirationDate);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Invalid date format'], Response::HTTP_BAD_REQUEST);
        }

        // Génération du hash
        $hash = hash('sha256', json_encode($data));

        // Vérifier l'intégrité des données via le hash
        if (isset($data['hash']) && $data['hash'] !== $hash) {
            return new JsonResponse(['error' => 'Hash mismatch'], Response::HTTP_BAD_REQUEST);
        }

        // Recherche de l'entité Owner
        $owner = $entityManager->getRepository(Owner::class)->find($ownerId);
        if (!$owner) {
            return new JsonResponse(['error' => 'Owner not found'], Response::HTTP_NOT_FOUND);
        }

        // Recherche de l'entité Status
        $status = $entityManager->getRepository(Status::class)->find($statusId);
        if (!$status) {
            return new JsonResponse(['error' => 'Status not found'], Response::HTTP_NOT_FOUND);
        }

        // Création et sauvegarde du LandTitle
        $landTitle = new LandTitle();
        $landTitle->setTitleNumber($titleNumber);
        $landTitle->setDescription($description);
        $landTitle->setIssueDate($issueDate);
        $landTitle->setExpirationDate($expirationDate);
        $landTitle->setOwner($owner);
        $landTitle->setStatus($status);
        $landTitle->setHash($hash);

        $entityManager->persist($landTitle);
        $entityManager->flush();

        // **Stocker les informations dans la blockchain**
        try {
            $txHash = $this->blockchainService->storeLandTitle($titleNumber, $hash);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Blockchain error: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse([
            'message' => 'Land Title created successfully.',
            'transaction' => $txHash
        ], Response::HTTP_CREATED);
    }
}

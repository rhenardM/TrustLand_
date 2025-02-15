<?php

// src/Controller/LandTitleMutationController.php
namespace App\Controller;

use App\Entity\LandTitle;
use App\Service\LandTitleMutationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class LandTitleMutationController extends AbstractController
{
    #[Route('/api/land-title/{id}/mutate', name: 'land_title_mutate', methods: ['POST'])]
    public function mutateOwner(
        LandTitle $landTitle,
        Request $request,
        LandTitleMutationService $mutationService
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $newOwner = $data['newOwner'] ?? null;

        if (!$newOwner) {
            return new JsonResponse(['error' => 'New owner is required'], Response::HTTP_BAD_REQUEST);
        }

        $newLandTitle = $mutationService->mutateOwner($landTitle, $newOwner);

        return new JsonResponse([
            'message' => 'Owner mutation successful.',
            'newLandTitleId' => $newLandTitle->getId(),
        ], Response::HTTP_OK);
    }
}
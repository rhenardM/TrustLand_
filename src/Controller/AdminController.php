<?php

namespace App\Controller;

use App\Entity\LandTitle;
use App\Repository\LandTitleRepository;
// use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    public function __construct
    (
        private LandTitleRepository $landTitleRepository
    ){}

    //Récupérer tous les documents archivés.
    #[Route('/admin/archived-land-titles', name: 'admin_archived_land_titles', methods: ['GET'])]
    public function getArchivedLandTitles(): JsonResponse
    {
        $archivedTitles = $this->landTitleRepository->findBy(['isArchived' => true]);

        $data = [];
        foreach ($archivedTitles as $title) {
            $data[] = $this->formatLandTitleData($title);
        }

        return new JsonResponse($data);
    }

    //Récupérer tous les documents non archivés (actifs).
    #[Route('/admin/active-land-titles', name: 'admin_active_land_titles', methods: ['GET'])]
    public function getActiveLandTitles(): JsonResponse
    {
        $activeTitles = $this->landTitleRepository->findBy(['isArchived' => false]);

        $data = [];
        foreach ($activeTitles as $title) {
            $data[] = $this->formatLandTitleData($title);
        }

        return new JsonResponse($data);
    }

    //Récupérer le compte des documents archivés et non archivés.
    #[Route('/admin/land-titles-count', name: 'admin_land_titles_count', methods: ['GET'])]
    public function getLandTitlesCount(): JsonResponse
    {
        $archivedCount = $this->landTitleRepository->count(['isArchived' => true]);
        $activeCount = $this->landTitleRepository->count(['isArchived' => false]);

        return new JsonResponse([
            'archived' => $archivedCount,
            'active' => $activeCount,
        ]);
    }

    // Formater les données d'un titre foncier pour la réponse JSON.
    private function formatLandTitleData(LandTitle $landTitle): array
    {
        return [
            'id' => $landTitle->getId(),
            'titleNumber' => $landTitle->getTitleNumber(),
            'owner' => $landTitle->getOwner()->getName() . ' ' . $landTitle->getOwner()->getFirstName(),
            'previousOwner' => $landTitle->getPreviousOwner(),
            'issueDate' => $landTitle->getIssueDate()->format('Y-m-d'),
            'expirationDate' => $landTitle->getExpirationDate()->format('Y-m-d'),
            'status' => $landTitle->getStatus()->getDescription(),
            'pdfPath' => $landTitle->getPdfPath(),
            'isArchived' => $landTitle->getIsArchived(),
        ];
    }
}
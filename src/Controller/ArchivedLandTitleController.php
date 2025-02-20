<?php 

namespace App\Controller;

use App\Repository\LandTitleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ArchivedLandTitleController extends AbstractController
{
    #[Route('/api/archived-land-titles', name: 'archived_land_titles', methods: ['GET'])]
    public function getArchivedTitles(LandTitleRepository $landTitleRepository): JsonResponse
    {
        // Récupérer les titres archivés
        $archivedTitles = $landTitleRepository->findArchivedTitles();

        // Formater les données pour la réponse JSON
        $data = [];
        foreach ($archivedTitles as $title) {
            $data[] = [
                'id' => $title->getId(),
                'titleNumber' => $title->getTitleNumber(),
                'owner' => $title->getOwner()->getFirstName(), // Supposons que `owner` est un objet
                'previousOwner' => $title->getPreviousOwner(),
                'issueDate' => $title->getIssueDate()->format('Y-m-d'),
                'expirationDate' => $title->getExpirationDate()->format('Y-m-d'),
                'status' => $title->getStatus(),
                'pdfPath' => $title->getPdfPath(),
            ];
        }

        // Retourner la réponse JSON
        return new JsonResponse($data, JsonResponse::HTTP_OK);
    }
}
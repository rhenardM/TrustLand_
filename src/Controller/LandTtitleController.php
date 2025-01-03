<?php

namespace App\Controller;

use App\Entity\LandTitle;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
//use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LandTtitleController extends AbstractController
{
    #[Route('api/land/ttitle', name: 'api_land_ttitle', methods: ['POST'])]
    //#[IsGranted('ROLE_ADMIN')]
    public function LandTitle(
        Request $request,
        EntityManagerInterface $entityManager
    ): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return new JsonResponse(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
        }
        // Hachage de la chaîne  
        $hash = hash('sha256', json_encode($data));  

        $titleNumber = $data['titleNumber'] ?? null;  
        $issueDate = $data['issueDate'] ?? null;  
        $expirationDate = $data['expirationDate'] ?? null;  
        $owner = $data['owner'] ?? null;  
        $status = $data['status'] ?? null;  

            // Vérification des champs requis  
        if (!$titleNumber || !$issueDate || !$expirationDate || !$owner || !$status) {  
            return new JsonResponse(['error' => 'Missing required fields'], Response::HTTP_BAD_REQUEST);  
        }  

            // Vérification de l'intégrité du hachage  
        if (isset($data['hash']) && $data['hash'] !== $hash) {  
            return new JsonResponse(['error' => 'Hash mismatch'], Response::HTTP_BAD_REQUEST);  
        }  

        // Création de l'objet LandTitle  
        $landTitle = new LandTitle();  
        $landTitle->setHash($hash); // Utiliser le hachage généré  
        $landTitle->setTitleNumber($titleNumber);  
        $landTitle->setIssueDate(new \DateTime($issueDate));  
        $landTitle->setExpirationDate(new \DateTime($expirationDate));  
        $landTitle->setOwner($owner);  
        $landTitle->setStatus($status);  

        $entityManager->persist($landTitle);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Land Ttitle created successfully.'], Response::HTTP_CREATED); 
    }
}

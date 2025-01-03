<?php

namespace App\Controller;

use App\Entity\Status;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Constraints\Json;

class StatusController extends AbstractController
{
    #[Route('api/status', name: 'api_status')]
    public function index(

    Request $request,
    EntityManagerInterface $entityManager

    ): Response
    {

        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return new JsonResponse(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
        }

        $description = $data['description'] ?? null;
        $date = $data['date'] ?? null;

        if (!$description || !$date) {
            return new JsonResponse(['error' => 'Missing required fields'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $date = new \DateTime($date);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Invalid date format'], Response::HTTP_BAD_REQUEST);
        }

        $status = new Status();
        $status->setDescription($description);
        $status->setDate($date);

        $entityManager->persist($status);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Status created successfully.'], Response::HTTP_CREATED);
    }
}

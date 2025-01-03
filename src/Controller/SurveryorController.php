<?php

namespace App\Controller;

use App\Entity\Surveryor;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SurveryorController extends AbstractController
{
    #[Route('api/surveryor', name: 'api_surveryor', methods: ['POST', 'GET', 'PUT', 'DELETE']) ]
    public function Surveryor
    (
        Request $request, 
        EntityManagerInterface $entityManager
    ):  JsonResponse

    {
        $data = json_decode($request->getContent(), true);

        $name = $data['name'] ?? null;
        $firstName = $data['firstName'] ?? null;
        $phone = $data['phone'] ?? null;
        $email = $data['email'] ?? null;


        $surveryor = new Surveryor();
        $surveryor->setName($name);
        $surveryor->setFirstName($firstName);
        $surveryor->setPhone($phone);
        $surveryor->setEmail($email);

        $entityManager->persist($surveryor);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Surveryor created successfully.'], Response::HTTP_CREATED);
    }
}

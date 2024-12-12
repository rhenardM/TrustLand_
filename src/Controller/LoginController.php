<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class LoginController extends AbstractController
{
    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
       // dump($data); 

        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        // Vérifiez que l'email et le mot de passe sont fournis
        if (!$email || !$password) {
            return new JsonResponse(['error' => 'Email and password are required.'], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Récupérez l'utilisateur par son email
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        // Vérifiez si l'utilisateur existe et si le mot de passe est valide
        if (!$user || !$passwordHasher->isPasswordValid($user, $password)) {
            return new JsonResponse(['error' => 'Invalid credentials.'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        // Connexion réussie
        return new JsonResponse(['message' => 'Login successful.'], JsonResponse::HTTP_OK);
    }
}
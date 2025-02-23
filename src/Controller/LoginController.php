<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

class LoginController extends AbstractController
{
    private $entityManager;
    private $passwordHasher;
    private $jwtManager;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        JWTTokenManagerInterface $jwtManager
    ) {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
        $this->jwtManager = $jwtManager;
    }

    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        // Vérifiez que l'email et le mot de passe sont fournis
        if (!$email || !$password) {
            return new JsonResponse(['error' => 'Email and password are required.'], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Récupérez l'utilisateur par son email
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        // Vérifiez si l'utilisateur existe et si le mot de passe est valide
        if (!$user || !$this->passwordHasher->isPasswordValid($user, $password)) {
            return new JsonResponse(['error' => 'Invalid credentials.'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        // Générer un token JWT
        $token = $this->jwtManager->create($user);

        // Récupérer le rôle de l'utilisateur
        $roles = $user->getRoles();

        // Déterminer la route de redirection en fonction du rôle
        
        // $redirect = in_array('ROLE_ADMIN', $roles) ? '/admin' : '/dashboard';
            // Déterminer la route de redirection en fonction du rôle
            // if (in_array('ROLE_SUPER_ADMIN', $roles)) {
            //     $redirect = '/api/admin';
            // } elseif (in_array('ROLE_ADMIN', $roles)) {
            //     $redirect = '/admin';
            // } else {
            //     $redirect = '/dashboard';
            // }

            // Ajout de la condition pour le rôle ROLE_CADASTRE, une approche plus lisible et efficace que les if successifs.
            // Elle évite l'imbrication de if/else et facilite la maintenance.
            $redirect = match (true) {
                in_array('ROLE_SUPER_ADMIN', $roles) => '/api/admin',
                in_array('ROLE_ADMIN', $roles) => '/admin',
                in_array('ROLE_CTI', $roles) => '/api/admin',
                in_array('ROLE_CADASTRE', $roles) => '/admin', 
                in_array('ROLE_OWNER', $roles) => '/user',     
                in_array('ROLE_USER', $roles) => '/user',
                default => '/dashboard',
            };

        // Réponse JSON avec le token et les informations de l'utilisateur
        return new JsonResponse([
            'success' => true,
            'token' => $token,
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'roles' => $roles,
            ],
            'redirect' => $redirect,
        ], JsonResponse::HTTP_OK);
    }
}
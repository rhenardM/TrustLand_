<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;



class RegistrationController extends AbstractController
{
        // integrate roles CTI for create land title
        #[IsGranted('ROLE_CTI')]    
    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    public function register
    (
        Request $request, 
        UserPasswordHasherInterface $passwordHasher, 
        EntityManagerInterface $entityManager
    ):  JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;
        $firstName = $data['firstName'] ?? null;
        $lastName = $data['lastName'] ?? null;
        $phone = $data['phone'] ?? null;
        $address = $data['address'] ?? null;

        if (!$email || !$password || !$firstName || !$lastName) {
            return new JsonResponse(['error' => 'All fields are required.'], Response::HTTP_BAD_REQUEST);
        }
        // Vérifier si un utilisateur avec cet email existe déjà
        $existingUser = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        if ($existingUser) {
            return new JsonResponse(['error' => 'Email is already taken.'], Response::HTTP_CONFLICT);
        }
        // Créer un nouvel utilisateur
        $user = new User();
        $user->setEmail($email);
        $user->setFirstName($firstName);
        $user->setLastName($lastName);
        $user->setPhone($phone);
        $user->setAddress($address);
        $user->setRoles($data['roles']); 
        $user->setPassword($passwordHasher->hashPassword($user, $password));

        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse(['message' => 'User registered successfully.'], Response::HTTP_CREATED);
    }

}

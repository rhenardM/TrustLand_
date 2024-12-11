<?php 

namespace App\Controller\Api;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/api/login")
 */

class LoginController extends AbstractController
{
    #[Route('/api/login', name: 'api_login', methods: ['GET', 'POST'])]

    public function ApiLogin()  
    {  
        $user = $this->getUser();  
        if (!$user) {  
            return new JsonResponse(['error' => 'Unauthorized'], 401);  
        }  
    
        $userData = [  
            'email' => $user->getEmail(),  
            'firstName' => $user->getFirstName(),        
            'lastName' => $user->getLastName(),  
        ];  
    
        return $this->json($userData);  
    }
}
<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser; // Import nécessaire
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LandTtitleControllerTest extends WebTestCase
{
    public function testCreateLandTitle(): void
    {
        /** @var KernelBrowser $client */ // Annotation pour le type
        $client = static::createClient();

        // Données de test
        $data = [
            'titleNumber' => '123456',
            'description' => 'Test Land Title Description',
            'issueDate' => '2023-12-01',
            'expirationDate' => '2033-12-01',
            'owner' => 'John Doe',
            'status' => 'Active',
        ];

        // Requête POST à l'API
        $client->request(
            'POST',
            '/api/land/title',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );

        // Récupération de la réponse
        $response = $client->getResponse();

        // Vérification de la réponse
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(201);

        // Vérification du contenu de la réponse
        $this->assertJson($response->getContent());
        $this->assertStringContainsString('Land Ttitle created successfully.', $response->getContent());
    }
}

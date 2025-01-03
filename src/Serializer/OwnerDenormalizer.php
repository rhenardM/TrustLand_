<?php  

namespace App\Serializer;  

use App\Entity\User;  
use Doctrine\ORM\EntityManagerInterface;  
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;  
use Symfony\Component\Serializer\Exception\UnexpectedValueException;  

class OwnerDenormalizer implements DenormalizerInterface  
{  
    private EntityManagerInterface $entityManager;  

    public function __construct(EntityManagerInterface $entityManager)  
    {  
        $this->entityManager = $entityManager;  
    }  

    // Cette méthode indique si ce dénormaliseur supporte le type spécifié  
    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool  
    {  
        return $type === 'App\Entity\Owner' && isset($data['user']);  
    }  

    // Dénormalisation des données en un objet de type donné  
    public function denormalize(mixed $data, string $class, ?string $format = null, array $context = []): mixed
        {
            if (!isset($data['user_id']) || !is_string($data['user_id'])) {
                throw new UnexpectedValueException('The "user_id" field is missing or invalid.');
            }

            // Extraire l'ID de l'utilisateur depuis l'URI
            $userId = str_replace('/api/register/', '', $data['user_id']);
            $user = $this->entityManager->getRepository(User::class)->find($userId);

            if (!$user) {
                throw new UnexpectedValueException('User not found.');
            }

            // Instancier l'objet Owner
            $object = new $class();

            // Associer l'utilisateur à l'objet Owner
            if (method_exists($object, 'setUser')) {
                $object->setUser($user);
            }

            // Attribuer les autres valeurs aux propriétés
            foreach ($data as $key => $value) {
                $setter = 'set' . ucfirst($key);
                if (method_exists($object, $setter) && $key !== 'user_id') {
                    $object->$setter($value);
                }
            }
            
            dump($object);
            return $object;
        }   

    // Implémentation de la méthode getSupportedTypes pour l'interface  
    public function getSupportedTypes(?string $format): array  
    {  
        return [  
            'App\Entity\Owner' => true,  
        ];  
    }  
}
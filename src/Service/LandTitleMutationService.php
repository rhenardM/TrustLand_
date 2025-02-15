<?php

namespace App\Service;

use App\Entity\LandTitle;
use App\Entity\Owner;
use Doctrine\ORM\EntityManagerInterface;

class LandTitleMutationService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function mutateOwner(LandTitle $landTitle, string $newOwner): LandTitle
    {
        // Créer un objet Owner à partir de la valeur de chaîne
        $owner = $this->entityManager->getRepository(Owner::class)->findOneBy(['id' => $newOwner]);

        if (!$owner) {
            // Si l'owner n'existe pas, vous pouvez créer un nouveau owner ou lancer une exception
            // ...
        }

        // Archiver l'ancien titre
        $landTitle->setIsArchived(true);
        $this->entityManager->persist($landTitle);

        // Créer un nouveau titre avec le nouveau propriétaire
        $newLandTitle = clone $landTitle;
        $newLandTitle->setPreviousOwner($landTitle->getOwner());
        $newLandTitle->setOwner($owner); // Utiliser l'objet Owner trouvé au lieu de la chaîne $newOwner
        $newLandTitle->setIsArchived(false);

        $this->entityManager->persist($newLandTitle);
        $this->entityManager->flush();

        return $newLandTitle;
    }
}
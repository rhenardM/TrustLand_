<?php

namespace App\Service;

use App\Entity\LandTitle;
use App\Entity\Owner;
use Doctrine\ORM\EntityManagerInterface;

class LandTitleMutationService
{
    private EntityManagerInterface $entityManager;
    private PdfGeneratorService $pdfGeneratorService;

    public function __construct(
        EntityManagerInterface $entityManager,
        PdfGeneratorService $pdfGeneratorService
        )
    {
        $this->entityManager = $entityManager;
        $this->pdfGeneratorService = $pdfGeneratorService;
    }

    public function mutateOwner(LandTitle $landTitle, string $newOwner): LandTitle
    {
        // Trouver ou créer un objet Owner à partir de la valeur de chaîne
        $owner = $this->entityManager->getRepository(Owner::class)->findOneBy(['firstName' => $newOwner]);
        $owner = $this->entityManager->getRepository(Owner::class)->findOneBy(['name' => $newOwner]);

        if (!$owner) {
            // Si l'owner n'existe pas, créer un nouvel owner
            $owner = new Owner();
            $owner->setName($newOwner);
            $owner->setFirstName($newOwner); 
            $owner->setDateOfBirth(new \DateTime('2021-02-15')); // Ex de definition d'une date en dure`dateOfBirth` (exemple)
            $this->entityManager->persist($owner);
        }

        // Archiver l'ancien titre
        $landTitle->setIsArchived(true);
        $landTitle->setPreviousOwner($landTitle->getOwner()->getName()); // Convertir l'objet Owner en chaîne
        $landTitle->setPreviousOwner($landTitle->getOwner()->getFirstName()); // Utiliser `firstName`
        $this->entityManager->persist($landTitle);

        // Creation d'un nouveau titre avec le nouveau propriétaire
        $newLandTitle = clone $landTitle;
        $newLandTitle->setOwner($owner); // Utilisation l'objet Owner trouvé ou créé
        $newLandTitle->setIsArchived(false);
        $newLandTitle->setPreviousOwner(null); // Réinitialiser le propriétaire précédent

        // Génération  d'un nouveau PDF pour le nouveau titre
            $newPdfPath = $this->pdfGeneratorService->generateLandTitlePdf($newLandTitle);
            $newLandTitle->setPdfPath($newPdfPath); // Mettre à jour le chemin du PDF
        

        $this->entityManager->persist($newLandTitle);
        $this->entityManager->flush();

        return $newLandTitle;
    }
}
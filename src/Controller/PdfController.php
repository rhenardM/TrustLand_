<?php 

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class PdfController extends AbstractController
{
    #[Route('/pdf/{pdfPath}', name: 'view_pdf', methods: ['GET'])]
    public function viewPdf(string $pdfPath): BinaryFileResponse
    {
        $filePath = 'uploads/pdf/' . $pdfPath;

        if (!file_exists($filePath)) {
            throw $this->createNotFoundException('PDF not found.');
        }

        // Retourner le fichier PDF pour téléchargement ou visualisation
        return $this->file($filePath, null, ResponseHeaderBag::DISPOSITION_INLINE);
    }
}
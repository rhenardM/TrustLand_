<?php

namespace App\Service;

use Dompdf\Dompdf;
use Dompdf\Options;
use App\Entity\Owner;
use App\Entity\Status;
use App\Entity\LandTitle;
use Doctrine\ORM\EntityManagerInterface;

class PdfGeneratorService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function generateLandTitlePdf( LandTitle $landTitle): string
    {
        // $owner = $this->entityManager->getRepository(Owner::class)->find($landTitle->getOwnerId());
        // $status = $this->entityManager->getRepository(Status::class)->find($landTitle->getStatusId());
        
        // Récupération du propriétaire et du statut actuel
        $owner = $landTitle->getOwner();
        if ($owner) {
            $owner = $this->entityManager->getRepository(Owner::class)->find($owner->getId());
        }
        $status = $landTitle->getStatus();

        if ($status) {
            $status = $this->entityManager->getRepository(Status::class)->find($status->getId());
        }


        // Configurer Dompdf
        $options = new Options();
        $options->set('defaultFont', 'popins');
        $dompdf = new Dompdf($options);

        // Générer le contenu HTML du PDF
        $html = $this->renderPdfHtml($landTitle, $owner, $status);

        // Charger le HTML dans Dompdf
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Sauvegarder le PDF dans un fichier
        $pdfPath = 'uploads/pdf/land_title_' . uniqid() . '.pdf';
        file_put_contents($pdfPath, $dompdf->output());

        return $pdfPath;
    }

    private function renderPdfHtml(LandTitle $landTitle, Owner $owner, Status $status): string
    {
        // Styles CSS
        $styles = "
            <style>
                body { font-family: Arial, sans-serif; }
                h1 { color: #2c3e50; text-align: center; }
                .info-container { margin: 20px; padding: 20px; border: 1px solid #ddd; border-radius: 8px; }
                table { width: 100%; border-collapse: collapse; }
                th, td { border: 1px solid #ddd; padding: 8px; }
                th { background-color: #f2f2f2; }
                .footer { text-align: center; margin-top: 30px; font-size: 12px; color: #7f8c8d; }
            </style>
        ";

        // Contenu HTML
        $html = "
            <html>
                <head>{$styles}</head>
                <body>
                    <div style='text-align: center;'>
                        <img src='https://example.com/logo.png' alt='Logo' style='width: 150px;'>
                    </div>
                    <h1>Land Title Information</h1>
                    <table>
                        <tr>
                            <th>Field</th>
                            <th>Value</th>
                        </tr>
                        <tr>
                            <td><strong>Title Number</strong></td>
                            <td>{$landTitle->getTitleNumber()}</td>
                        </tr>
                        <tr>
                            <td><strong>Owner</strong></td>
                            <td>{$owner->getFirstName()} {$owner->getName()}</td>
                        </tr>
                        <tr>
                            <td><strong>Issue Date</strong></td>
                            <td>{$landTitle->getIssueDate()->format('Y-m-d')}</td>
                        </tr>
                        <tr>
                            <td><strong>Expiration Date</strong></td>
                            <td>{$landTitle->getExpirationDate()->format('Y-m-d')}</td>
                        </tr>
                        <tr>
                            <td><strong>Description</strong></td>
                            <td>{$landTitle->getDescription()}</td>
                        </tr>
                        <tr>
                            <td><strong>Status</strong></td>
                            <td>{$status->getDescription()}</td>
                        </tr>
                    </table>
                    <div class='footer'>
                        <p>Generated on: " . date('Y-m-d H:i:s') . "</p>
                    </div>
                </body>
            </html>
        ";

        return $html;
    }
}
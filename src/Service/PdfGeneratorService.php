<?php

// src/Service/PdfGeneratorService.php
namespace App\Service;

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfGeneratorService
{
    public function generateLandTitlePdf(array $data): string
    {
        // Configurer Dompdf
        $options = new Options();
        $options->set('defaultFont', 'Helvetica');
        $dompdf = new Dompdf($options);

        // Générer le contenu HTML du PDF
        $html = $this->renderPdfHtml($data);

        // Charger le HTML dans Dompdf
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Sauvegarder le PDF dans un fichier
        $pdfPath = 'uploads/pdf/land_title_' . uniqid() . '.pdf';
        file_put_contents($pdfPath, $dompdf->output());

        return $pdfPath;
    }

    private function renderPdfHtml(array $data): string
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
                            <td>{$data['titleNumber']}</td>
                        </tr>
                        <tr>
                            <td><strong>Owner</strong></td>
                            <td>{$data['owner_id']}</td>
                        </tr>
                        <tr>
                            <td><strong>Issue Date</strong></td>
                            <td>{$data['issueDate']}</td>
                        </tr>
                        <tr>
                            <td><strong>Expiration Date</strong></td>
                            <td>{$data['expirationDate']}</td>
                        </tr>
                        <tr>
                            <td><strong>Status</strong></td>
                            <td>{$data['status_id']}</td>
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
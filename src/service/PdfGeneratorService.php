<?php

// src/Service/PdfGeneratorService.php

namespace App\Service;

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfGeneratorService
{
    public function generatePdf($htmlContent)
    {
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($htmlContent);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->output();
    }
}

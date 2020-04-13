<?php

namespace App\Controller;

use Dompdf\Dompdf;
use Dompdf\Options;
use App\Entity\CustomerOrder;
use Doctrine\Persistence\ObjectManager;
use App\Repository\CustomerOrderRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PdfController extends AbstractController
{
    /**
     * @Route("/pdf/{id}", name="pdf")
     * 
     * @param CustomerOrder $product
     * @param ObjectManager $manager
     * @return Response
     */
    public function index(CustomerOrder $order) {

        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        
        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        $totalHT = 0;
        $totalTTC = 0;

        foreach ($order->getItems() as $item) {
            $totalHT = $totalHT + ($item->getQuantity() * $item->getProduct()->getNoTaxePrice());
            $totalTTC = $totalTTC + ($item->getQuantity() * $item->getProduct()->getTaxePrice());
        };
        
        //Retrieve the HTML generated in our twig file
        $html = $this->renderView('pdf/invoice.html.twig', [
            'order' => $order,
            'totalHT' => $totalHT,
            'totalTTC' => $totalTTC
        ]);
        
        // Load HTML to Dompdf
        $dompdf->loadHtml($html);
        
        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (force download)
        $dompdf->stream("invoice.pdf", [
            "Attachment" => true
        ]);
        
        

        return $this->redirect($_SERVER['HTTP_REFERER']);
    }
}

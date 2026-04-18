<?php

namespace App\Services;

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfService
{
    protected $dompdf;

    public function __construct()
    {
        $this->dompdf = new Dompdf();
        
        // Set options
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);
        $this->dompdf->setOptions($options);
    }

    public function generateOrderPdf($order)
    {
        // Load view
        $html = view('pdf.order-detail', compact('order'))->render();
        
        // Load HTML to Dompdf
        $this->dompdf->loadHtml($html);
        
        // Set paper size and orientation
        $this->dompdf->setPaper('A4', 'portrait');
        
        // Render the HTML as PDF
        $this->dompdf->render();
        
        return $this->dompdf;
    }

    public function downloadOrderPdf($order)
    {
        $this->generateOrderPdf($order);
        
        // Tampilkan PDF di browser (bisa download atau print)
        return $this->dompdf->stream('order-' . $order->order_number . '.pdf', ['Attachment' => false]);
    }

    public function downloadOrderReceipt($order)
    {
        $this->generateOrderReceipt($order);
        
        // Tampilkan PDF receipt di browser
        return $this->dompdf->stream('struk-' . $order->order_number . '.pdf', ['Attachment' => false]);
    }

    public function generateOrderReceipt($order)
    {
        // Load receipt view
        $html = view('pdf.order-receipt', compact('order'))->render();
        
        // Load HTML to Dompdf
        $this->dompdf->loadHtml($html);
        
        // Set paper size untuk struk (lebih kecil)
        $this->dompdf->setPaper([0, 0, 226.77, 595.28], 'portrait'); // 80mm x 210mm
        
        // Render HTML as PDF
        $this->dompdf->render();
        
        return $this->dompdf;
    }

    public function outputOrderPdf($order)
    {
        $this->generateOrderPdf($order);
        
        // Output the PDF as string
        return $this->dompdf->output();
    }
}

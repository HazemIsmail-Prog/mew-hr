<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use setasign\Fpdi\Fpdi;

class GeneratePdfTemplate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pdf:generate-template';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a PDF template with form fields';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generating PDF template...');
        
        // Create a new PDF instance
        $pdf = new Fpdi();
        
        // Add a page
        $pdf->AddPage();
        
        // Set font
        $pdf->SetFont('Helvetica', '', 12);
        
        // Add title
        $pdf->SetFont('Helvetica', 'B', 16);
        $pdf->Cell(0, 10, 'User Information Form', 0, 1, 'C');
        $pdf->Ln(10);
        
        // Add form fields
        $pdf->SetFont('Helvetica', 'B', 12);
        $pdf->Cell(40, 10, 'Name:', 0, 0);
        $pdf->SetFont('Helvetica', '', 12);
        $pdf->TextField('name', 100, 10, ['border' => 1]);
        $pdf->Ln(15);
        
        $pdf->SetFont('Helvetica', 'B', 12);
        $pdf->Cell(40, 10, 'Email:', 0, 0);
        $pdf->SetFont('Helvetica', '', 12);
        $pdf->TextField('email', 100, 10, ['border' => 1]);
        $pdf->Ln(15);
        
        $pdf->SetFont('Helvetica', 'B', 12);
        $pdf->Cell(40, 10, 'Date:', 0, 0);
        $pdf->SetFont('Helvetica', '', 12);
        $pdf->TextField('date', 100, 10, ['border' => 1]);
        $pdf->Ln(15);
        
        $pdf->SetFont('Helvetica', 'B', 12);
        $pdf->Cell(40, 10, 'Signature:', 0, 0);
        $pdf->Ln(10);
        $pdf->ImageField('signature', 50, 10, ['border' => 1]);
        $pdf->Ln(20);
        
        // Add a signature line
        $pdf->Line(50, $pdf->GetY(), 150, $pdf->GetY());
        $pdf->Ln(5);
        $pdf->SetFont('Helvetica', 'I', 10);
        $pdf->Cell(0, 10, 'Please sign above', 0, 1, 'C');
        
        // Save the PDF
        $templatePath = storage_path('app/templates/form-template.pdf');
        $pdf->Output('F', $templatePath);
        
        $this->info('PDF template generated successfully at: ' . $templatePath);
        
        return Command::SUCCESS;
    }
} 
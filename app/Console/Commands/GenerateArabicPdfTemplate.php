<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use setasign\Fpdi\Fpdi;

class GenerateArabicPdfTemplate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pdf:generate-arabic-template';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate an Arabic PDF template with form fields';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generating Arabic PDF template...');
        
        // Create a new PDF instance
        $pdf = new Fpdi();
        
        // Add a page
        $pdf->AddPage();
        
        // Set RTL mode
        $pdf->SetRTL(true);
        
        // Add title
        $pdf->SetFont('Helvetica', 'B', 18);
        $pdf->Cell(0, 15, 'نموذج معلومات المستخدم', 0, 1, 'C');
        $pdf->Ln(10);
        
        // Add form fields
        $pdf->SetFont('Helvetica', 'B', 12);
        $pdf->Cell(40, 10, 'الاسم:', 0, 0);
        $pdf->SetFont('Helvetica', '', 12);
        $pdf->TextField('name', 100, 10, ['border' => 1]);
        $pdf->Ln(15);
        
        $pdf->SetFont('Helvetica', 'B', 12);
        $pdf->Cell(40, 10, 'البريد الإلكتروني:', 0, 0);
        $pdf->SetFont('Helvetica', '', 12);
        $pdf->TextField('email', 100, 10, ['border' => 1]);
        $pdf->Ln(15);
        
        $pdf->SetFont('Helvetica', 'B', 12);
        $pdf->Cell(40, 10, 'التاريخ:', 0, 0);
        $pdf->SetFont('Helvetica', '', 12);
        $pdf->TextField('date', 100, 10, ['border' => 1]);
        $pdf->Ln(15);
        
        $pdf->SetFont('Helvetica', 'B', 12);
        $pdf->Cell(40, 10, 'التوقيع:', 0, 0);
        $pdf->Ln(10);
        $pdf->ImageField('signature', 50, 10, ['border' => 1]);
        $pdf->Ln(20);
        
        // Add a signature line
        $pdf->Line(50, $pdf->GetY(), 150, $pdf->GetY());
        $pdf->Ln(5);
        $pdf->SetFont('Helvetica', 'I', 10);
        $pdf->Cell(0, 10, 'يرجى التوقيع أعلاه', 0, 1, 'C');
        
        // Add footer
        $pdf->SetY(-30);
        $pdf->SetFont('Helvetica', 'I', 8);
        $pdf->Cell(0, 10, 'تم إنشاء هذا النموذج بواسطة النظام', 0, 1, 'C');
        $pdf->Cell(0, 10, 'جميع الحقوق محفوظة © ' . date('Y'), 0, 1, 'C');
        
        // Save the PDF
        $templatePath = storage_path('app/templates/form-template.pdf');
        $pdf->Output('F', $templatePath);
        
        $this->info('Arabic PDF template generated successfully at: ' . $templatePath);
        
        return Command::SUCCESS;
    }
} 
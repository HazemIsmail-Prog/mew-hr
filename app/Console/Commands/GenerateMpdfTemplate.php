<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Mpdf\Mpdf;

class GenerateMpdfTemplate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pdf:generate-mpdf-template';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a PDF template using mPDF with Arabic support';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generating PDF template using mPDF...');
        
        // Create a new Mpdf instance
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_header' => '3',
            'margin_top' => '20',
            'margin_bottom' => '20',
            'margin_footer' => '3',
            'tempDir' => storage_path('app/temp'),
            'fontDir' => [
                base_path('public/fonts'),
            ],
            'fontdata' => [
                'cairo' => [
                    'R' => 'Cairo-Regular.ttf',
                    'B' => 'Cairo-Bold.ttf',
                    'useOTL' => 0xFF,
                    'useKashida' => 75,
                ],
            ],
            'default_font' => 'cairo',
            'direction' => 'rtl',
            'auto_language_detection' => true,
            'auto_arabic' => true,
            'is_arabic' => true,
            'is_rtl' => true,
        ]);
        
        // Add HTML content with form fields
        $html = '
        <!DOCTYPE html>
        <html lang="ar" dir="rtl">
        <head>
            <meta charset="utf-8">
            <title>نموذج معلومات المستخدم</title>
            <style>
                body {
                    font-family: cairo, Arial, sans-serif;
                    line-height: 1.6;
                    color: #333;
                }
                .header {
                    text-align: center;
                    margin-bottom: 30px;
                    border-bottom: 1px solid #ddd;
                    padding-bottom: 20px;
                }
                .header h1 {
                    font-weight: bold;
                    color: #2c3e50;
                }
                .form-group {
                    margin-bottom: 20px;
                }
                .form-label {
                    font-weight: bold;
                    display: block;
                    margin-bottom: 5px;
                }
                .form-field {
                    width: 100%;
                    padding: 8px;
                    border: 1px solid #ddd;
                    border-radius: 4px;
                    background-color: #f9f9f9;
                }
                .signature-area {
                    margin-top: 30px;
                    border: 1px dashed #ddd;
                    padding: 20px;
                    text-align: center;
                }
                .signature-line {
                    border-top: 1px solid #000;
                    margin-top: 50px;
                    width: 70%;
                    margin-left: auto;
                    margin-right: auto;
                }
                .footer {
                    margin-top: 50px;
                    text-align: center;
                    font-size: 12px;
                    color: #777;
                    border-top: 1px solid #ddd;
                    padding-top: 20px;
                }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>نموذج معلومات المستخدم</h1>
            </div>
            
            <div class="form-group">
                <label class="form-label">الاسم:</label>
                <input type="text" class="form-field" name="name" />
            </div>
            
            <div class="form-group">
                <label class="form-label">البريد الإلكتروني:</label>
                <input type="email" class="form-field" name="email" />
            </div>
            
            <div class="form-group">
                <label class="form-label">التاريخ:</label>
                <input type="text" class="form-field" name="date" />
            </div>
            
            <div class="signature-area">
                <label class="form-label">التوقيع:</label>
                <div class="signature-line"></div>
                <p>يرجى التوقيع أعلاه</p>
            </div>
            
            <div class="footer">
                <p>تم إنشاء هذا النموذج بواسطة النظام</p>
                <p>جميع الحقوق محفوظة © ' . date('Y') . '</p>
            </div>
        </body>
        </html>
        ';
        
        // Write the HTML to the PDF
        $mpdf->WriteHTML($html);
        
        // Save the PDF
        $templatePath = storage_path('app/templates/form-template.pdf');
        $mpdf->Output($templatePath, 'F');
        
        $this->info('PDF template generated successfully at: ' . $templatePath);
        
        return Command::SUCCESS;
    }
} 
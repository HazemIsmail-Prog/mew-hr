<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Mpdf\Mpdf;

class GeneratePdfFormTemplate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pdf:generate-form-template';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a PDF form template with actual form fields';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generating PDF form template with actual form fields...');
        
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
                    padding: 20px;
                }
                .header {
                    text-align: center;
                    margin-bottom: 30px;
                    border-bottom: 2px solid #2c3e50;
                    padding-bottom: 20px;
                }
                .header h1 {
                    font-weight: bold;
                    color: #2c3e50;
                    font-size: 24pt;
                }
                .form-group {
                    margin-bottom: 25px;
                    position: relative;
                }
                .form-label {
                    font-weight: bold;
                    display: block;
                    margin-bottom: 8px;
                    color: #2c3e50;
                }
                .form-field {
                    width: 100%;
                    height: 35px;
                    border: 1px solid #bdc3c7;
                    border-radius: 4px;
                    background-color: #f9f9f9;
                }
                .signature-area {
                    margin-top: 40px;
                    border: 2px dashed #bdc3c7;
                    padding: 20px;
                    text-align: center;
                    background-color: #f9f9f9;
                }
                .signature-box {
                    width: 300px;
                    height: 100px;
                    margin: 20px auto;
                    border: 1px solid #bdc3c7;
                    background-color: white;
                }
                .footer {
                    margin-top: 50px;
                    text-align: center;
                    font-size: 10pt;
                    color: #7f8c8d;
                    border-top: 1px solid #bdc3c7;
                    padding-top: 20px;
                }
                .required {
                    color: #e74c3c;
                    margin-right: 5px;
                }
                .field-hint {
                    font-size: 9pt;
                    color: #7f8c8d;
                    margin-top: 5px;
                }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>نموذج معلومات المستخدم</h1>
                <p>يرجى تعبئة جميع الحقول المطلوبة</p>
            </div>
            
            <div class="form-group">
                <label class="form-label">الاسم الكامل<span class="required">*</span></label>
                <input type="text" class="form-field" name="name" required />
                <div class="field-hint">يرجى كتابة الاسم الكامل كما هو في الهوية</div>
            </div>
            
            <div class="form-group">
                <label class="form-label">البريد الإلكتروني<span class="required">*</span></label>
                <input type="email" class="form-field" name="email" required />
                <div class="field-hint">سيتم استخدام هذا البريد للتواصل معك</div>
            </div>
            
            <div class="form-group">
                <label class="form-label">رقم الهاتف</label>
                <input type="tel" class="form-field" name="phone" />
                <div class="field-hint">يرجى إدخال رقم الهاتف مع رمز الدولة</div>
            </div>
            
            <div class="form-group">
                <label class="form-label">تاريخ التقديم<span class="required">*</span></label>
                <input type="text" class="form-field" name="date" value="' . date('Y-m-d') . '" readonly />
            </div>
            
            <div class="signature-area">
                <label class="form-label">التوقيع<span class="required">*</span></label>
                <div class="signature-box"></div>
                <div class="field-hint">يرجى التوقيع داخل المربع أعلاه</div>
            </div>
            
            <div class="footer">
                <p>تم إنشاء هذا النموذج بواسطة النظام - ' . date('Y') . ' ©</p>
                <p>جميع الحقوق محفوظة</p>
            </div>
        </body>
        </html>
        ';
        
        // Write the HTML to the PDF
        $mpdf->WriteHTML($html);
        
        // Save the PDF
        $templatePath = storage_path('app/templates/form-template.pdf');
        $mpdf->Output($templatePath, 'F');
        
        $this->info('PDF form template generated successfully at: ' . $templatePath);
        
        return Command::SUCCESS;
    }
} 
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>{{$title}}</title>
    <style>
        @font-face {
            font-family: 'Cairo';
            src: url('{{ asset('fonts/Cairo-Regular.ttf') }}') format('truetype');
            font-weight: normal;
            font-style: normal;
            font-display: swap;
        }
        
        @font-face {
            font-family: 'Cairo';
            src: url('{{ asset('fonts/Cairo-Bold.ttf') }}') format('truetype');
            font-weight: bold;
            font-style: normal;
            font-display: swap;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Cairo', 'Arial', 'Helvetica', 'sans-serif' !important;
            line-height: 1.6;
            color: #0066cc;
        }
        .reason {
            position: absolute;
            width: 100%;
            top: 400px;
            right: 0;
            left: 0;
            text-align: center;
            font-size: 18px;
            font-weight: bold;
        }
        .start-date {
            position: absolute;
            top: 460px;
            right: 180px;
            font-size: 18px;
            font-weight: bold;
        }
        .end-date {
            position: absolute;
            top: 460px;
            right: 330px;
            font-size: 18px;
            font-weight: bold;
        }
        .name {
            position: absolute;
            top: 495px;
            right: 170px;
            font-size: 18px;
            font-weight: bold;

        }
        .cid {
            position: absolute;
            top: 534px;
            right: 170px;
            font-size: 18px;
            font-weight: bold;
        }
        .file-number {
            position: absolute;
            top: 570px;
            right: 170px;
            font-size: 18px;
            font-weight: bold;
        }
        .employee-signature {
            position: absolute;
            top: 600px;
            right: 170px;
            font-size: 18px;
            font-weight: bold;
            width: 120px;
            height: 60px;
        }
        .manager-signature {
            position: absolute;
            bottom: 110px;
            left: 75px;
            font-size: 18px;
            font-weight: bold;
            width: 300px;
            height: 150px;
        }
        .manager-stamp {
            position: absolute;
            bottom: 110px;
            left: 125px;
            font-size: 18px;
            font-weight: bold;
            width: 200px;
        }
        .direction-in {
            position: absolute;
            top: 710px;
            right: 73px;
            font-size: 18px;
            font-weight: bold;
            border:2px solid #0066cc;
            border-radius: 10px;
            width: 65px;
            height: 35px;
        }
        .direction-out {
            position: absolute;
            top: 710px;
            right: 158px;
            font-size: 18px;
            font-weight: bold;
            border:2px solid #0066cc;
            border-radius: 10px;
            width: 77px;
            height: 35px;
        }
        .direction-in-out {
            position: absolute;
            top: 710px;
            right: 255px;
            font-size: 18px;
            font-weight: bold;
            border:2px solid #0066cc;
            border-radius: 10px;
            width: 140px;
            height: 35px;
        }
        .direction-text-in {
            position: absolute;
            top: 750px;
            right: 85px;
            font-size: 18px;
            font-weight: bold;
            width: 65px;
            height: 35px;
        }
        .direction-text-out {
            position: absolute;
            top: 750px;
            right: 175px;
            font-size: 18px;
            font-weight: bold;
            width: 65px;
            height: 35px;
        }
        .direction-text-in-out {
            position: absolute;
            top: 750px;
            right: 305px;
            font-size: 18px;
            font-weight: bold;
            width: 65px;
            height: 35px;
        }
    </style>
</head>
<body>
    <div class="reason">{{$exemption->reason}}</div>
    <div class="start-date">{{$exemption->date->format('d/m/Y')}}</div>
    <div class="end-date">{{$exemption->date->format('d/m/Y')}}</div>
    <div class="name">{{$exemption->user->name}}</div>
    <div class="cid">{{$exemption->user->cid}}</div>
    <div class="file-number">{{$exemption->user->file_number}}</div>
    <div class="employee-signature">
        <img  class="employee-signature" src="data:image/png;base64,{{ $employeeSignatureBase64 }}" alt="توقيع المستخدم" class="signature-image">
    </div>  
    <div @class([
        'direction-in' => $exemption->direction == 'in',
        'direction-out' => $exemption->direction == 'out',
        'direction-in-out' => $exemption->direction == 'in-out'
        ])>
    </div>
    <div @class([
        'direction-text-in' => $exemption->direction == 'in',
        'direction-text-out' => $exemption->direction == 'out',
        'direction-text-in-out' => $exemption->direction == 'in-out'
        ])>
        إعفاء
    </div>
    @if($managerSignatureBase64)
        <div class="manager-signature">
            <img  class="manager-signature" src="data:image/png;base64,{{ $managerSignatureBase64 }}" alt="توقيع المستخدم" class="signature-image">
        </div>  
        <div class="manager-stamp">
            <img  class="manager-stamp" src="data:image/png;base64,{{ $managerStampBase64 }}" alt="توقيع المستخدم" class="signature-image">
        </div>  

    @endif
</body>
</html>


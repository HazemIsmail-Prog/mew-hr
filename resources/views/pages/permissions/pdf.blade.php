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
            top: 410px;
            right: 500px;
            left: 60px;
            text-align: right;
            font-size: 18px;
            font-weight: bold;
        }
        .duration {
            position: absolute;
            width: 30px;
            top: 410px;
            right: 110px;
            text-align: center;
            font-size: 18px;
            font-weight: bold;
        }
        .time {
            position: absolute;
            width: 65px;
            top: 410px;
            right: 344px;
            text-align: center;
            font-size: 18px;
            font-weight: bold;
        }
        .day {
            position: absolute;
            width: 70px;
            top: 372px;
            right: 455px;
            text-align: center;
            font-size: 18px;
            font-weight: bold;
        }
        .date {
            position: absolute;
            top: 372px;
            right: 600px;
            text-align: center;
            font-size: 18px;
            font-weight: bold;
        }

        .name {
            position: absolute;
            top: 500px;
            right: 170px;
            font-size: 18px;
            font-weight: bold;

        }
        .department {
            position: absolute;
            top: 538px;
            right: 170px;
            font-size: 18px;
            font-weight: bold;
        }
        .cid {
            position: absolute;
            top: 575px;
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
            bottom: 210px;
            left: 75px;
            font-size: 18px;
            font-weight: bold;
            width: 300px;
            height: 150px;
        }

    </style>
</head>
<body>
    <div class="duration">{{$permission->duration}}</div>
    <div class="time">{{$permission->time->format('H:i')}}</div>
    <div class="reason">{{$permission->reason}}</div>
    <div class="day">{{$permission->date->translatedFormat('l', 'ar')}}</div>
    <div class="date">{{$permission->date->format('d/m/Y')}}</div>
    <div class="name">{{$permission->user->name}}</div>
    <div class="department">{{$permission->user->department->name}}</div>
    <div class="cid">{{$permission->user->cid}}</div>
    <div class="employee-signature">
        <img  class="employee-signature" src="data:image/png;base64,{{ $employeeSignatureBase64 }}" alt="توقيع المستخدم" class="signature-image">
    </div>  
    @if($managerSignatureBase64)
        <div class="manager-signature">
            <img  class="manager-signature" src="data:image/png;base64,{{ $managerSignatureBase64 }}" alt="توقيع المستخدم" class="signature-image">
        </div>  
    @endif
</body>
</html>


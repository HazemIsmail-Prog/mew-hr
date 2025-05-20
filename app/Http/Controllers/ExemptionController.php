<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExemptionRequest;
use App\Http\Requests\UpdateExemptionRequest;
use App\Http\Resources\ExemptionResource;
use App\Models\Exemption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ExemptionController extends Controller
{
    public function index()
    {
        abort_if(!request()->user()->can('viewAny', Exemption::class), 403);
        if(request()->wantsJson()){
            $exemptions = Exemption::query()
            ->where('user_id', auth()->id())
            ->latest('date')
            ->paginate(10);
            return ExemptionResource::collection($exemptions);
        }
        return view('pages.exemptions.index');
    }

    public function store(StoreExemptionRequest $request)
    {
        abort_if(!request()->user()->can('create', Exemption::class), 403);
        $exemption = Exemption::create($request->validated());
        return new ExemptionResource($exemption);
    }

    public function update(UpdateExemptionRequest $request, Exemption $exemption)
    {
        abort_if(!request()->user()->can('update', $exemption), 403);
        abort_if($exemption->status !== 'pending', 403, __('Something went wrong, please refresh the page'));
        $exemption->update($request->validated());
        return new ExemptionResource($exemption);
    }

    public function destroy(Exemption $exemption)
    {
        abort_if(!request()->user()->can('delete', $exemption), 403);
        abort_if($exemption->status !== 'pending', 403, __('Something went wrong, please refresh the page'));
        $exemption->delete();
        return response()->json(['message' => 'Exemption deleted successfully']);
    }

    public function changeStatus(Exemption $exemption, Request $request)
    {
        abort_if(!request()->user()->can('changeStatus', $exemption), 403);
        $request->validate([
            'status' => 'required|in:approved,rejected,for review',
        ]);
        $exemption->status = $request->status;
        switch($request->status){
            case 'approved':
                $exemption->approved_at = now();
                $exemption->approved_by = auth()->id();
                $exemption->rejected_at = null;
                $exemption->rejected_by = null;

                break;
            case 'rejected':
                $exemption->rejected_at = now();
                $exemption->rejected_by = auth()->id();
                $exemption->approved_at = null;
                $exemption->approved_by = null;
                break;
            case 'for review':
                $exemption->rejected_at = null;
                $exemption->rejected_by = null;
                $exemption->approved_at = null;
                $exemption->approved_by = null;
                break;
        }
        $exemption->save();
        return new ExemptionResource($exemption);
    }

    public function massApprove(Request $request)
    {
        $exemptions = Exemption::whereIn('id', $request->exemptions)->get();
        foreach ($exemptions as $exemption) {
            $exemption->status = 'approved';
            $exemption->approved_at = now();
            $exemption->approved_by = auth()->id();
            $exemption->rejected_at = null;
            $exemption->rejected_by = null;
            $exemption->save();
        }
        return response()->json(['message' => 'Exemptions approved successfully']);
    }

    public function show(Exemption $exemption)
    {
        abort_if(!request()->user()->can('view', $exemption), 403);
        // // Get the signature image as base64
        $employeeSignatureBase64 = null;
        if ($exemption->user->signature) {
            $employeeSignaturePath = Storage::disk('signatures')->path($exemption->user->getRawOriginal('signature'));
            $employeeSignatureBase64 = base64_encode(file_get_contents($employeeSignaturePath));
        }

        $managerSignatureBase64 = null;
        $managerStampBase64 = null;
        if ($exemption->approved_by) {
            $managerSignaturePath = Storage::disk('signatures')->path($exemption->approvedByUser->getRawOriginal('signature'));
            $managerSignatureBase64 = base64_encode(file_get_contents($managerSignaturePath));
            $managerStampPath = Storage::disk('stamps')->path($exemption->approvedByUser->getRawOriginal('stamp'));
            $managerStampBase64 = base64_encode(file_get_contents($managerStampPath));
        }


        $title = 'اعفاء' . ' ' . __($exemption->direction) . ' بتاريخ ' . $exemption->date->format('d-m-Y');


        $data = [
            'title' => $title,
            'user' => $exemption->user,
            'exemption' => $exemption,
            'employeeSignatureBase64' => $employeeSignatureBase64,
            'managerSignatureBase64' => $managerSignatureBase64,
            'managerStampBase64' => $managerStampBase64,
        ];

        $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $mpdf = new \Mpdf\Mpdf([
            'setAutoTopMargin' => 'pad',
            'setAutoTopMargin' => 'stretch',
            'autoMarginPadding' => 0,
            'orientation' => 'P',
            'format' => 'A4',
            'fontDir' => array_merge($fontDirs, [
                public_path('fonts'),
            ]),
            'fontdata' => $fontData + [ // lowercase letters only in font key
                'cairo' => [
                    'R' => 'Cairo-Regular.ttf',
                    'B' => 'Cairo-Bold.ttf',
                    'useOTL' => 0xFF,
                    'useKashida' => 75,
                ]
            ],
            'default_font' => 'cairo',
        ]);

        $mpdf->SetWatermarkImage(public_path('images/exemption.jpg'));
        $mpdf->watermarkImgBehind = true;
        $mpdf->showWatermarkImage = true;
        $mpdf->showImageErrors = false;
        $mpdf->watermarkImageAlpha = 1;
        $mpdf->SetProtection(['copy', 'print'], '', 'pass');
        $mpdf->WriteHTML(view('pages.exemptions.pdf', $data)->render());
        // download the pdf
        // return $mpdf->Output($data['title'].'.pdf', 'I');
        return $mpdf->Output($data['title'].'.pdf', 'D');
    }
}

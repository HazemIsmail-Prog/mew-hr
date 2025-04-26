<?php

namespace App\Http\Controllers;

use App\Models\Mission;
use App\Http\Requests\StoreMissionRequest;
use App\Http\Requests\UpdateMissionRequest;
use App\Http\Resources\MissionResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Mpdf\Mpdf;

class MissionController extends Controller
{

    public function index()
    {
        abort_if(!request()->user()->can('viewAny', Mission::class), 403);
        if(request()->wantsJson()){
            $missions = Mission::query()
            ->where('user_id', auth()->id())
            ->latest('date')
            ->paginate(5);
            return MissionResource::collection($missions);
        }
        return view('pages.missions.index');
    }

    public function store(StoreMissionRequest $request)
    {
        abort_if(!request()->user()->can('create', Mission::class), 403);
        $mission = Mission::create($request->validated());
        return new MissionResource($mission);
    }

    public function update(UpdateMissionRequest $request, Mission $mission)
    {
        abort_if(!request()->user()->can('update', $mission), 403);
        abort_if($mission->status !== 'pending', 403, __('Something went wrong, please refresh the page'));
        $mission->update($request->validated());
        return new MissionResource($mission);
    }

    public function destroy(Mission $mission)
    {
        abort_if(!request()->user()->can('delete', $mission), 403);
        abort_if($mission->status !== 'pending', 403, __('Something went wrong, please refresh the page'));
        $mission->delete();
        return response()->json(['message' => 'Mission deleted successfully']);
    }

    public function changeStatus(Mission $mission, Request $request)
    {
        abort_if(!request()->user()->can('changeStatus', $mission), 403);
        $request->validate([
            'status' => 'required|in:approved,rejected,for review',
        ]);
        $mission->status = $request->status;
        switch($request->status){
            case 'approved':
                $mission->approved_at = now();
                $mission->approved_by = auth()->id();
                $mission->rejected_at = null;
                $mission->rejected_by = null;

                break;
            case 'rejected':
                $mission->rejected_at = now();
                $mission->rejected_by = auth()->id();
                $mission->approved_at = null;
                $mission->approved_by = null;
                break;
            case 'for review':
                $mission->rejected_at = null;
                $mission->rejected_by = null;
                $mission->approved_at = null;
                $mission->approved_by = null;
                break;
        }
        $mission->save();
        return new MissionResource($mission);
    }

    public function massApprove(Request $request)
    {
        $missions = Mission::whereIn('id', $request->missions)->get();
        foreach ($missions as $mission) {
            $mission->status = 'approved';
            $mission->approved_at = now();
            $mission->approved_by = auth()->id();
            $mission->rejected_at = null;
            $mission->rejected_by = null;
            $mission->save();
        }
        return response()->json(['message' => 'Missions approved successfully']);
    }

    public function show(Mission $mission)
    {
        abort_if(!request()->user()->can('view', $mission), 403);
        // // Get the signature image as base64
        $employeeSignatureBase64 = null;
        if ($mission->user->signature) {
            $employeeSignaturePath = Storage::disk('signatures')->path($mission->user->getRawOriginal('signature'));
            $employeeSignatureBase64 = base64_encode(file_get_contents($employeeSignaturePath));
        }

        $managerSignatureBase64 = null;
        $managerStampBase64 = null;
        if ($mission->approved_by) {
            $managerSignaturePath = Storage::disk('signatures')->path($mission->approvedByUser->getRawOriginal('signature'));
            $managerSignatureBase64 = base64_encode(file_get_contents($managerSignaturePath));
            $managerStampPath = Storage::disk('stamps')->path($mission->approvedByUser->getRawOriginal('stamp'));
            $managerStampBase64 = base64_encode(file_get_contents($managerStampPath));
        }


        $title = 'تكليف' . ' ' . __($mission->direction) . ' بتاريخ ' . $mission->date->format('d-m-Y');


        $data = [
            'title' => $title,
            'user' => $mission->user,
            'mission' => $mission,
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

        $mpdf->SetWatermarkImage(public_path('images/mission.jpg'));
        $mpdf->watermarkImgBehind = true;
        $mpdf->showWatermarkImage = true;
        $mpdf->showImageErrors = false;
        $mpdf->watermarkImageAlpha = 1;
        $mpdf->SetProtection(['copy', 'print'], '', 'pass');
        $mpdf->WriteHTML(view('pages.missions.pdf', $data)->render());
        // download the pdf
        return $mpdf->Output($data['title'].'.pdf', 'D');
    }
}

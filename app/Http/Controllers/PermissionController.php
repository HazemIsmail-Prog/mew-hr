<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\User;
use App\Http\Requests\StorePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;
use Illuminate\Http\Request;
use App\Http\Resources\PermissionResource;
use Illuminate\Support\Facades\Storage;
use Mpdf\Mpdf;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        abort_if(!request()->user()->can('viewAny', Permission::class), 403);
        if(request()->wantsJson()){
            $permissions = Permission::query()
                ->where('user_id', auth()->id())
                ->with(['user', 'approvedByUser', 'rejectedByUser'])
                ->when($request->user_id, function($query, $user_id) {
                    return $query->where('user_id', $user_id);
                })
                ->when($request->status, function($query, $status) {
                    return $query->where('status', $status);
                })
                ->when($request->date_from, function($query, $date_from) {
                    return $query->where('date', '>=', $date_from);
                })
                ->when($request->date_to, function($query, $date_to) {
                    return $query->where('date', '<=', $date_to);
                })
                ->latest()
                ->paginate(5);
            return PermissionResource::collection($permissions);
        }
        return view('pages.permissions.index');
    }

    public function store(StorePermissionRequest $request)
    {
        abort_if(!request()->user()->can('create', Permission::class), 403);
        $permission = Permission::create($request->validated());
        return new PermissionResource($permission);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePermissionRequest $request, Permission $permission)
    {
        abort_if(!request()->user()->can('update', $permission), 403);
        abort_if($permission->status !== 'pending', 403, __('Something went wrong, please refresh the page'));
        $permission->update($request->validated());
        return new PermissionResource($permission);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Permission $permission)
    {
        abort_if(!request()->user()->can('delete', $permission), 403);
        abort_if($permission->status !== 'pending', 403, __('Something went wrong, please refresh the page'));
        $permission->delete();
        return response()->json(['message' => 'Permission deleted successfully']);
    }

    public function changeStatus(Permission $permission, Request $request)
    {
        abort_if(!request()->user()->can('changeStatus', $permission), 403);
        $request->validate([
            'status' => 'required|in:approved,rejected,for review',
        ]);
        $permission->status = $request->status;
        switch($request->status){
            case 'approved':
                $permission->approved_at = now();
                $permission->approved_by = auth()->id();
                $permission->rejected_at = null;
                $permission->rejected_by = null;

                break;
            case 'rejected':
                $permission->rejected_at = now();
                $permission->rejected_by = auth()->id();
                $permission->approved_at = null;
                $permission->approved_by = null;
                break;
            case 'for review':
                $permission->rejected_at = null;
                $permission->rejected_by = null;
                $permission->approved_at = null;
                $permission->approved_by = null;
                break;
        }
        $permission->save();
        return new PermissionResource($permission);
    }

    public function massApprove(Request $request)
    {
        $permissions = Permission::whereIn('id', $request->permissions)->get();
        foreach ($permissions as $permission) {
            $permission->status = 'approved';
            $permission->approved_at = now();
            $permission->approved_by = auth()->id();
            $permission->rejected_at = null;
            $permission->rejected_by = null;
            $permission->save();
        }
        return response()->json(['message' => 'Permissions approved successfully']);
    }

    public function show(Permission $permission)
    {
        abort_if(!request()->user()->can('view', $permission), 403);
        // // Get the signature image as base64
        $employeeSignatureBase64 = null;
        if ($permission->user->signature) {
            $employeeSignaturePath = Storage::disk('signatures')->path($permission->user->getRawOriginal('signature'));
            $employeeSignatureBase64 = base64_encode(file_get_contents($employeeSignaturePath));
        }

        $managerSignatureBase64 = null;
        $managerStampBase64 = null;
        if ($permission->approved_by) {
            $managerSignaturePath = Storage::disk('signatures')->path($permission->approvedByUser->getRawOriginal('signature'));
            $managerSignatureBase64 = base64_encode(file_get_contents($managerSignaturePath));
            $managerStampPath = Storage::disk('stamps')->path($permission->approvedByUser->getRawOriginal('stamp'));
            $managerStampBase64 = base64_encode(file_get_contents($managerStampPath));
        }

        $data = [
            'title' => 'اذن' . ' ' . __($permission->type) . ' بتاريخ ' . $permission->date->format('d-m-Y'),
            'user' => $permission->user,
            'permission' => $permission,
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

        if($permission->type == 'in'){
            $mpdf->SetWatermarkImage(public_path('images/p-in.jpg'));
        }else{
            $mpdf->SetWatermarkImage(public_path('images/p-out.jpg'));
        }

        $mpdf->watermarkImgBehind = true;
        $mpdf->showWatermarkImage = true;
        $mpdf->showImageErrors = false;
        $mpdf->watermarkImageAlpha = 1;
        $mpdf->SetProtection(['copy', 'print'], '', 'pass');
        $mpdf->WriteHTML(view('pages.permissions.pdf', $data)->render());
        // download the pdf
        return $mpdf->Output($data['title'].'.pdf', 'D');
    }
}

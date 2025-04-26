<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Permission;
use App\Models\Mission;
use App\Models\Request as RequestModel;
use App\Http\Resources\PermissionResource;
use App\Http\Resources\MissionResource;
use App\Models\User;
use App\Models\Exemption;
use App\Http\Resources\ExemptionResource;

class RequestController extends Controller
{

    public function getCounts()
    {
        $permissionsCount = Permission::whereIn('user_id', $this->getChildrenIds())->where('status', 'pending')->count();
        $missionsCount = Mission::whereIn('user_id', $this->getChildrenIds())->where('status', 'pending')->count();
        $exemptionsCount = Exemption::whereIn('user_id', $this->getChildrenIds())->where('status', 'pending')->count();
        return response()->json([
            'permissionsCount' => $permissionsCount,
            'missionsCount' => $missionsCount,
            'exemptionsCount' => $exemptionsCount
        ]);
    }


    private function getChildrenIds(){

        $supervisors = User::where('replacement_id', auth()->user()->id)
            ->orWhere('id', auth()->user()->id)
            ->get();
        $childrenIds = [];
        foreach ($supervisors as $supervisor) {
            array_push($childrenIds, $supervisor->children()->pluck('id'));
        }
        return collect($childrenIds)->flatten()->values()->all();
    }


    public function missions(Request $request)
    {
        abort_if(!request()->user()->can('viewAny', RequestModel::class), 403);

        if($request->wantsJson()){
            $missions = Mission::query()
                ->whereIn('user_id', $this->getChildrenIds())
                ->orderBy('user_id', 'asc')
                ->orderBy('date', 'desc')
                ->with('user')
                ->when($request->user, function ($query) use ($request) {
                    $query->where('user_id', $request->user);
                })
                ->when($request->status, function ($query) use ($request) {
                    $query->where('status', $request->status);
                })
                ->paginate(10);
                return MissionResource::collection($missions);
        }

        $users = User::whereIn('id', $this->getChildrenIds())->orderBy('name')->get();

        return view('pages.requests.missions', compact('users'));


    }

    public function permissions(Request $request)
    {
        if($request->wantsJson()){
            $permissions = Permission::query()
                ->whereIn('user_id', $this->getChildrenIds())
                ->orderBy('user_id', 'asc')
                ->orderBy('date', 'desc')
                ->with('user')
                ->when($request->user, function ($query) use ($request) {
                    $query->where('user_id', $request->user);
                })
                ->when($request->status, function ($query) use ($request) {
                    $query->where('status', $request->status);
                })
                ->paginate(10);
            return PermissionResource::collection($permissions);
        }

        $users = User::whereIn('id', $this->getChildrenIds())->orderBy('name')->get();

        return view('pages.requests.permissions', compact('users'));
    }

    public function exemptions(Request $request)
    {
        if($request->wantsJson()){
            $exemptions = Exemption::query()
                ->whereIn('user_id', $this->getChildrenIds())
                ->orderBy('user_id', 'asc')
                ->orderBy('date', 'desc')
                ->with('user')
                ->when($request->user, function ($query) use ($request) {
                    $query->where('user_id', $request->user);
                })
                ->when($request->status, function ($query) use ($request) {
                    $query->where('status', $request->status);
                })
                ->paginate(10);
            return ExemptionResource::collection($exemptions);
        }

        $users = User::whereIn('id', $this->getChildrenIds())->orderBy('name')->get();

        return view('pages.requests.exemptions', compact('users'));
    }
    
}

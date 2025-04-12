<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Permission;
use App\Models\Mission;
use App\Models\Request as RequestModel;
use App\Http\Resources\PermissionResource;
use App\Http\Resources\MissionResource;


class RequestController extends Controller
{
    public function index()
    {
        abort_if(!request()->user()->can('viewAny', RequestModel::class), 403);
        $users = auth()->user()->children()->get();
        return view('pages.requests.index', compact('users'));
    }

    public function getCounts()
    {
        $permissionsCount = Permission::whereIn('user_id', auth()->user()->children()->pluck('id'))->where('status', 'pending')->count();
        $missionsCount = Mission::whereIn('user_id', auth()->user()->children()->pluck('id'))->where('status', 'pending')->count();
        return response()->json([
            'permissionsCount' => $permissionsCount,
            'missionsCount' => $missionsCount
        ]);
    }



    public function missions(Request $request)
    {
        $missions = Mission::query()
            ->whereIn('user_id', auth()->user()->children()->pluck('id'))
            ->latest()
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

    public function permissions(Request $request)
    {
        $permissions = Permission::query()
            ->whereIn('user_id', auth()->user()->children()->pluck('id'))
            ->latest()
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
}

<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Http\Requests\StoreDepartmentRequest;
use App\Http\Requests\UpdateDepartmentRequest;
use Illuminate\Http\Request;
use App\Http\Resources\DepartmentResource;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        abort_if(!$request->user()->can('viewAny', Department::class), 403);

        if(request()->wantsJson()){
            $departments = Department::query()
            // ->latest()
            ->paginate(10);
            return DepartmentResource::collection($departments);
        }
        return view('pages.departments.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDepartmentRequest $request)
    {
        abort_if(!$request->user()->can('create', Department::class), 403);
        $department = Department::create($request->validated());
        return new DepartmentResource($department);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDepartmentRequest $request, Department $department)
    {
        abort_if(!$request->user()->can('update', $department), 403);
        $department->update($request->validated());
        return new DepartmentResource($department);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Department $department)
    {
        abort_if(!$request->user()->can('delete', $department), 403);
        $department->delete();
        return response()->json(['message' => 'Department deleted successfully']);
    }
}

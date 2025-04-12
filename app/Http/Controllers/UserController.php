<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Department;
use App\Http\Resources\UserResource;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Mpdf\Mpdf;

class UserController extends Controller
{
    public function index(Request $request)
    {
        abort_if(!request()->user()->can('viewAny', User::class), 403);
        if(request()->wantsJson()){
            $users = User::query()
            ->with('department')
            ->with('supervisor')
            ->latest()
            ->paginate(10);
            return UserResource::collection($users);
        }

        $departments = Department::all();
        $supervisors = User::where('role', 'supervisor')->get();
        return view('pages.users.index', compact('departments', 'supervisors'));
    }

    public function store(Request $request)
    {
        abort_if(!request()->user()->can('create', User::class), 403);
        // Create the user
        $user = User::factory()->create();
        
        $signaturePath = $this->saveSignatureAndReturnItsFullPath($request->signature, $user);
        if($signaturePath){
            $user->signature = $signaturePath;
            $user->save();
        }
        
        return response()->json(['message' => 'User created successfully']);
    }

    private function saveSignatureAndReturnItsFullPath($signature, $user)
    {

        // first remove all files in the signatures folder which start with the "user.id_"
        $files = Storage::disk('signatures')->files();
        foreach($files as $file){
            if(str_starts_with($file, $user->id . '_')){
                unlink(Storage::disk('signatures')->path($file));
            }
        }

        // Handle signature if provided
        if (isset($signature) && !empty($signature)) {
            
            // Save signature to file
            $signatureData = $signature;
            $signatureFileName = $user->id . '_' . now()->timestamp . '.png';
            $base64Data = preg_replace('/^data:image\/\w+;base64,/', '', $signatureData);
            
            // Decode the base64 data
            $imageData = base64_decode($base64Data);
            // dd($imageData);

            Storage::disk('signatures')->put($signatureFileName, $imageData);
            
            // Update the signature path in the data
            return $signatureFileName;
        }
        return null;
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        abort_if(!request()->user()->can('update', $user), 403);
        $data = $request->validated();

        if($request->signature){
            $signaturePath = $this->saveSignatureAndReturnItsFullPath($request->signature, $user);
            if($signaturePath){
                $data['signature'] = $signaturePath;
            }
        }
        // Update the user
        $user->update($data);
        return response()->json(['message' => 'User updated successfully']);
    }

    public function destroy(User $user)
    {
        abort_if(!request()->user()->can('delete', $user), 403);
        // Delete the signature file if it exists
        if ($user->signature && file_exists(public_path($user->signature))) {
            unlink(public_path($user->signature));
        }
        $user->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }
}

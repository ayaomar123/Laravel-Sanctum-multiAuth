<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdminResource;
use App\Http\Resources\UserResource;
use App\Models\Admin;
use App\Models\User;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|unique:admins',
            'password' => 'required|confirmed',
            'phone' => 'required|unique:admins',
        ]);
        $data['password'] = bcrypt($data['password']);

        $admin = Admin::create($data);

        return response()->json([
            'message' => "Registered Successfully",
            'data' => AdminResource::collection(Admin::query()->where('id',$admin->id)->get()),
            'token' => $admin->createToken('Api Admin Token')->plainTextToken
        ]);
    }

    public function login(Request $request)
    {

        $data = $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);
        $admin = Admin::where('email', $request->email)->first();
        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return response()->json([
                'msg' => "Credentials not match"
            ],401);
        }
        return response()->json([
            'msg' => "login Successfully",
            'data' => AdminResource::collection(Admin::query()->where('id',$admin->id)->get()),
            'token' => $admin->createToken('Admin')->plainTextToken
        ]);

    }

    public function logout()
    {
        auth()->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logout successfully'
        ], 200);
    }


    public function updateProfile(Request $request)
    {

        if(count($request->all()) >0) {
            $admin = auth()->user();
            $admin->update($request->all());
            return response()->json([
                'msg' => 'Admin Profile Updated Successfully',
                'data' => AdminResource::collection(Admin::query()->where('id', $admin->id)->get())
            ], 200);
        }
        return response()->json([
            'msg' => 'No Item to Update',
        ], 422);
    }

    public function editPassword()
    {
        $data = \request()->validate([
            'password' => 'required|confirmed',
        ]);
        $admin = Admin::find(\auth()->user()->id);
        $requestData = \request()->all();
        if(\request()->password == \request()->password_confirmation){
            $requestData['password'] = bcrypt($requestData['password']);
        }
        else{
            unset($requestData['password']);
        }
        $admin->update($requestData);
        return response()->json([
            'message' => 'Admin Updated Password Successfully',
            'data' => AdminResource::collection(Admin::query()->where('id',$admin->id)->get())
        ], 200);
    }

    public function getAllUser(){
        return \response()->json([
           UserResource::collection(User::all())
        ]);
    }
}

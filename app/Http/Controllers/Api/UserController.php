<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|unique:users',
            'password' => 'required|confirmed',
            'phone' => 'required|unique:users',
        ]);

        $data['password'] = bcrypt($data['password']);

        $user = User::create($data);
        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json([
            'msg' => 'User register successfully',
           'data' => UserResource::collection(User::query()->where('id',$user->id)->get())
        ]);
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if (!Auth::attempt($data)) {
            return response()->json([
               'error' => "Credentials not match",
            ]);
        }

        return response()->json([
           'msg' => "User Login Successfully",
           'data' => UserResource::collection(User::query()->where('id',\auth()->user()->id)->get()),
           'token' => auth()->user()->createToken('API Token')->plainTextToken
        ]);

    }

    public function logout()
    {
        auth()->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logout successfully'
        ], 200);
    }

    public function profile(){
        return \response()->json([
           'data' => \auth()->user()
        ]);
    }
    public function editProfile(Request $request){

        if(count($request->all()) >0) {
            $user = auth()->user();
            $user->update($request->all());
            return response()->json([
                'msg' => 'Profile Updated Successfully',
                'data' => UserResource::collection(User::query()->where('id', $user->id)->get())
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
        $user = User::find(\auth()->user()->id);
        if ($user->password == \request()->old){
            dd("1");
        }
        $requestData = \request()->all();
        if(\request()->password == \request()->password_confirmation){
            $requestData['password'] = bcrypt($requestData['password']);
        }
        else{
            unset($requestData['password']);
        }
        $user->update($requestData);
        return response()->json([
            'message' => 'User Updated Password Successfully',
            'data' => UserResource::collection(User::query()->where('id',$user->id)->get())
        ], 200);
    }

}

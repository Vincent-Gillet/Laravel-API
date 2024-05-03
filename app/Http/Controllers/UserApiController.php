<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\Fluent\Concerns\Has;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use App\Http\Controllers\Controller;

class UserApiController extends Controller
{
    public function index () {
        $users = User::all();
        return response()->json([$users]);
    }

    public function store (Request $request) {
        $request->validate([
            'name' => 'required|max:255',
            'email' => 'required',
            'password' => [
                'required',
                'min:8',
                'regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!$#%]).*$/',
            ],
          ]);
          $users = User::create($request->all());      
          return response()->json([$users]);
    }

    public function show ($id) {
        $user = User::find($id);
        return response()->json($user);
    }

    public function update (Request $request, $id) {
        $request->validate([
            'name' => 'required|max:255',
            'email' => 'required',
            'password' => 'required',
          ]);

        $user = User::find($id);
        $user->update($request->all());
        return response()->json([$user]);
    }

    public function destroy ($id) {
        $user = User::find($id);
        $user -> delete();
        return response()->json(null, 204);
    }



    /**
     * Create User
     * @param Request $request
     * @return User 
     */
    public function createUser(Request $request)
    {

        $request->validate([
            'name' => 'required|max:255',
            'email' => 'required',
            'password' => 'required',
        ]);
    
        $user = User::create($request->all());   
    
        $token = $user->createToken('app_token', ['*']);
        
        return [
            'token' => $token->plainTextToken,
        ];
    }



    /**
     * Login The User
     * @param Request $request
     * @return User
     */
    public function loginUser(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only(['email', 'password']))) {

            if(auth('sanctum')->check()){
                auth()->user()->tokens()->delete();
            }

            $token = Auth::user()->createToken('app_token',['*'])->plainTextToken;
            return response()->json(['token' => $token]);
        }

        return response()->json(['message' => 'Invalid credentials'], 401);
    }
}

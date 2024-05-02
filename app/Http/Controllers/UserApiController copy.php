<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

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


        // try {
        //     //Validated
        //     $validateUser = Validator::make($request->all(), 
        //     [
        //         'name' => 'required',
        //         'email' => 'required|email|unique:users,email',
        //         'password' => 'required'
        //     ]);

        //     if($validateUser->fails()){
        //         return response()->json([
        //             'status' => false,
        //             'message' => 'validation error',
        //             'errors' => $validateUser->errors()
        //         ], 401);
        //     }

        //     $user = User::create([
        //         'name' => $request->name,
        //         'email' => $request->email,
        //         'password' => Hash::make($request->password)
        //     ]);

        //     return response()->json([
        //         'status' => true,
        //         'message' => 'User Created Successfully',
        //         'token' => $user->createToken("API TOKEN")->plainTextToken
        //     ], 200);

        // } catch (\Throwable $th) {
        //     return response()->json([
        //         'status' => false,
        //         'message' => $th->getMessage()
        //     ], 500);
        // }
    }



    /**
     * Login The User
     * @param Request $request
     * @return User
     */
    public function loginUser(Request $request)
    {

        $request->validate([
            'name' => 'required|max:255',
            'email' => 'required',
            'password' => 'required',
          ]);

        $user = User::find($id);
        $user->update($request->all());


        if(auth('sanctum')->check()){
            auth()->user()->tokens()->delete();
         }
         $token = Auth::user()
                  ->createToken('app_token',['*'])
                  ->plainTextToken;


        return response()->json([$user, $token]);

    //     try {
    //         $validateUser = Validator::make($request->all(), 
    //         [
    //             'email' => 'required|email',
    //             'password' => 'required'
    //         ]);

    //         if($validateUser->fails()){
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'validation error',
    //                 'errors' => $validateUser->errors()
    //             ], 401);
    //         }

    //         if(!Auth::attempt($request->only(['email', 'password']))){
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'Email & Password does not match with our record.',
    //             ], 401);
    //         }

    //         $user = User::where('email', $request->email)->first();

    //         return response()->json([
    //             'status' => true,
    //             'message' => 'User Logged In Successfully',
    //             'token' => $user->createToken("API TOKEN")->plainTextToken
    //         ], 200);

    //     } catch (\Throwable $th) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => $th->getMessage()
    //         ], 500);
    //     }
    }
    
}

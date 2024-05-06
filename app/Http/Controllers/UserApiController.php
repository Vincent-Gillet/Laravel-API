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

/**
 * @OA\Info(
 *     title="My API",
 *     version="0.1",
 *     description="This is a sample API for demonstration purposes",
 *     termsOfService="http://example.com/terms/",
 *     contact={
 *         "email": "api@example.com"
 *     },
 *     license={
 *         "name": "Apache 2.0",
 *         "url": "http://www.apache.org/licenses/LICENSE-2.0.html"
 *     }
 * )
 * @OA\Server(url="http://localhost:8000")
 */

class UserApiController extends Controller
{

/**
 * @OA\Get(
 *     path="/api/users",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(response="200", description="User details"),
 *     @OA\Response(response="404", description="User not found")
 * )
 */

    public function index () {
        $users = User::all();
        return response()->json([$users]);
    }

/**
 * @OA\Post(
 *     path="/api/users",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name","email","password"},
 *             @OA\Property(property="name", type="string", format="text", example="John Doe"),
 *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *             @OA\Property(property="password", type="string", format="password", example="Password123!"),
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User created successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="name", type="string", example="John Doe"),
 *             @OA\Property(property="email", type="string", example="john@example.com"),
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Bad request",
 *     )
 * )
 */

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

/**
 * @OA\Get(
 *     path="/api/users/{id}",
 *     summary="Get user by ID",
 *     tags={"Users"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID of the user to retrieve",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="User details",
 *         @OA\JsonContent(
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="name", type="string", example="John Doe"),
 *             @OA\Property(property="email", type="string", example="john@example.com"),
 *             @OA\Property(property="password", type="string", example="Password123!"),
 *         )
 *     ),
 *     @OA\Response(response="404", description="User not found")
 * )
 */
    
    public function show ($id) {
        $user = User::find($id);
        return response()->json($user);
    }

/**
 * @OA\Put(
 *     path="/api/users/{id}",
 *     summary="Update an existing user",
 *     tags={"Users"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID of the user to update",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name","email","password"},
 *             @OA\Property(property="name", type="string", format="text", example="John Doe"),
 *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *             @OA\Property(property="password", type="string", format="password", example="Password123!"),
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User updated successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="name", type="string", example="John Doe"),
 *             @OA\Property(property="email", type="string", example="john@example.com"),
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Bad request",
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User not found",
 *     )
 * )
 */

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

/**
 * @OA\Delete(
 *     path="/api/users/{id}",
 *     summary="Delete a user",
 *     tags={"Users"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID of the user to delete",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=204,
 *         description="User deleted successfully"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User not found",
 *     )
 * )
 */

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

/**
 * @OA\Post(
 *     path="/api/users/create",
 *     summary="Create a new user",
 *     tags={"Users"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name","email","password"},
 *             @OA\Property(property="name", type="string", format="text", example="John Doe"),
 *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *             @OA\Property(property="password", type="string", format="password", example="Password123!"),
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User created successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="name", type="string", example="John Doe"),
 *             @OA\Property(property="email", type="string", example="john@example.com"),
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Bad request",
 *     )
 * )
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

/**
 * @OA\Post(
 *     path="/api/users/login",
 *     summary="Login a user",
 *     tags={"Users"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email","password"},
 *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *             @OA\Property(property="password", type="string", format="password", example="Password123!"),
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User logged in successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="token", type="string", example="app_token"),
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Invalid credentials",
 *     )
 * )
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




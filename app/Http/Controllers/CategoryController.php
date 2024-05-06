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
use App\Models\Category;

/**
 * @OA\Schema(
 *     schema="Category",
 *     required={"title", "description"},
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="The unique identifier of the category",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="title",
 *         type="string",
 *         description="The title of the category",
 *         example="Category 1"
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         description="The description of the category",
 *         example="This is a great category"
 *     )
 * )
 */

class CategoryController extends Controller
{

/**
 * @OA\Get(
 *     path="/api/categories",
 *     summary="Get a list of categories",
 *     tags={"Categories"},
 *     @OA\Response(
 *         response=200,
 *         description="List of categories",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(ref="#/components/schemas/Category")
 *         )
 *     )
 * )
 */

    public function index () {
        $categories = Category::all();
        return response()->json([$categories]);
    }

/**
 * @OA\Post(
 *     path="/api/categories",
 *     summary="Create a new category",
 *     tags={"Categories"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"title","description"},
 *             @OA\Property(property="title", type="string", example="Category 1"),
 *             @OA\Property(property="description", type="string", example="This is a great category")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Category created successfully",
 *         @OA\JsonContent(ref="#/components/schemas/Category")
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid input",
 *     )
 * )
 */

    public function store (Request $request) {
        $request->validate([
            'title' => 'required|max:255',
            'description' => 'required',
          ]);
          $categorie = Category::create($request->all());      
          return response()->json([$categorie]);
    }

/**
 * @OA\Get(
 *     path="/api/categories/{id}",
 *     summary="Get a specific category",
 *     tags={"Categories"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID of the category to return",
 *         required=true,
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Category details",
 *         @OA\JsonContent(ref="#/components/schemas/Category")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Category not found",
 *     )
 * )
 */

    public function show ($id) {
        $categorie = Category::find($id);
        return response()->json($categorie);
    }

/**
 * @OA\Put(
 *     path="/api/categories/{id}",
 *     summary="Update a category",
 *     tags={"Categories"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID of the category to update",
 *         required=true,
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"title","description"},
 *             @OA\Property(property="title", type="string", example="Updated Category"),
 *             @OA\Property(property="description", type="string", example="This is an updated category")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Category updated successfully",
 *         @OA\JsonContent(ref="#/components/schemas/Category")
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid input",
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Category not found",
 *     )
 * )
 */

    public function update (Request $request, $id) {
        $request->validate([
            'title' => 'required|max:255',
            'description' => 'required',
          ]);

        $categorie = Category::find($id);
        $categorie->update($request->all());
        return response()->json([$categorie]);
    }

/**
 * @OA\Delete(
 *     path="/api/categories/{id}",
 *     summary="Delete a category",
 *     tags={"Categories"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID of the category to delete",
 *         required=true,
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *     @OA\Response(
 *         response=204,
 *         description="Category deleted successfully"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Category not found",
 *     )
 * )
 */

    public function destroy ($id) {
        $categorie = Category::find($id);
        $categorie -> delete();
        return response()->json(null, 204);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Schema(
 *     schema="Product",
 *     required={"name", "price"},
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="The unique identifier of the product",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="The name of the product",
 *         example="Product 1"
 *     ),
 *     @OA\Property(
 *         property="price",
 *         type="number",
 *         format="float",
 *         description="The price of the product",
 *         example=99.99
 *     ),
 *     @OA\Property(
 *         property="categories",
 *         type="array",
 *         @OA\Items(type="string"),
 *         description="The categories of the product",
 *         example={"Category 1", "Category 2"}
 *     )
 * )
 */


class ProductApiController extends Controller
{

/**
 * @OA\Get(
 *     path="/api/products",
 *     summary="Get a list of products",
 *     tags={"Products"},
 *     @OA\Response(
 *         response=200,
 *         description="List of products",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(ref="#/components/schemas/Product")
 *         )
 *     )
 * )
 */

    public function index (Request $request) {

        $categories = Category::pluck('title');

        // Charger les produits avec les relations "categories"
        $productsQuery = Product::with('categories');
    
        // Obtenir les produits avec les informations sur les catégories
        $products = $productsQuery->get();
    
        // Formater les données des produits pour inclure uniquement les noms des catégories
        $formattedProducts = $products->map(function ($product) {
            $productCategories = $product->categories->pluck('title');
            $product->unsetRelation('categories'); // Supprimer les détails des catégories des produits
            $product->setAttribute('categories', $productCategories); // Ajouter uniquement les noms des catégories
            return $product;
        });
    
        // Retourner les produits et les catégories au format JSON
        return response()->json([
            'products' => $formattedProducts,
            'categories' => $categories
        ]);

    }

/**
 * @OA\Post(
 *     path="/api/products",
 *     summary="Create a new product",
 *     tags={"Products"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name","description","price","stock"},
 *             @OA\Property(property="name", type="string", example="Product 1"),
 *             @OA\Property(property="description", type="string", example="This is a great product"),
 *             @OA\Property(property="price", type="number", format="float", example=99.99),
 *             @OA\Property(property="stock", type="integer", example=10),
 *             @OA\Property(property="picture", type="string", format="binary")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Product created successfully",
 *         @OA\JsonContent(ref="#/components/schemas/Product")
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid input",
 *     )
 * )
 */

    public function store (Request $request) {
        $request->validate([
            'name' => 'required|max:255',
            'description' => 'required',
            'price' => 'required',
            'stock' => 'required',
        ]);
        $product = new Product();
        $product->name = $request->name;
        $product->description = $request->description;
        $product->price = $request->price;
        $product->stock = $request->stock;

        if ($request->hasFile('picture')) {
            $filename = time() . '.' . $request->picture->extension();
            $path = $request->file('picture')->storeAs('public/picture', $filename);
            $product->picture = $path;
          }

          $product->save();

        if ($request->has('categories')) {
            $product->categories()->attach($request->categories);
        }

        return response()->json([$product]);
    }

/**
 * @OA\Get(
 *     path="/api/products/{id}",
 *     summary="Get product by ID",
 *     tags={"Products"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID of the product to retrieve",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Product details",
 *         @OA\JsonContent(ref="#/components/schemas/Product")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Product not found",
 *     )
 * )
 */

    public function show ($id) {
        $product = Product::find($id);
        return response()->json($product);
    }

/**
 * @OA\Put(
 *     path="/api/products/{id}",
 *     summary="Update a product",
 *     tags={"Products"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID of the product to update",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name","description","price","stock"},
 *             @OA\Property(property="name", type="string", example="Product 1"),
 *             @OA\Property(property="description", type="string", example="This is a great product"),
 *             @OA\Property(property="price", type="number", format="float", example=99.99),
 *             @OA\Property(property="stock", type="integer", example=10),
 *             @OA\Property(property="picture", type="string", format="binary")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Product updated successfully",
 *         @OA\JsonContent(ref="#/components/schemas/Product")
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid input",
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Product not found",
 *     )
 * )
 */

    public function update (Request $request, $id) {
        $request->validate([
            'name' => 'required|max:255',
            'description' => 'required',
            'price' => 'required',
            'stock' => 'required',
            'picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
    
        $product = Product::find($id);
    
        $product->name = $request->input('name');
        $product->description = $request->input('description');
        $product->price = $request->input('price');
        $product->stock = $request->input('stock');
    
        if ($request->hasFile('picture')) {
            $filename = time() . '.' . $request->picture->extension();
            $path = $request->file('picture')->storeAs('public/picture', $filename);
            $product->picture = $path;
        }
    
        $product->save();
    
        if ($request->has('categories')) {
            $product->categories()->sync($request->categories);
        }
    
        return response()->json([$product]);
    }

/**
 * @OA\Delete(
 *     path="/api/products/{id}",
 *     summary="Delete a product",
 *     tags={"Products"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID of the product to delete",
 *         required=true,
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *     @OA\Response(
 *         response=204,
 *         description="Product deleted successfully"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Product not found",
 *     )
 * )
 */

    public function destroy ($id) {
        $product = Product::find($id);
        $product -> delete();
        return response()->json(null, 204);
    }
}

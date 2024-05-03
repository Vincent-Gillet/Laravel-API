<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductApiController extends Controller
{
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

    public function show ($id) {
        $product = Product::find($id);
        return response()->json($product);
    }

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

    public function destroy ($id) {
        $product = Product::find($id);
        $product -> delete();
        return response()->json(null, 204);
    }
}

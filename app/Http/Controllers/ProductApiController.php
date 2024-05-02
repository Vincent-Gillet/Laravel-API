<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductApiController extends Controller
{
    public function index () {
        $products = Product::all();
        return response()->json([$products]);
    }

    public function store (Request $request) {
        $request->validate([
            'name' => 'required|max:255',
            'description' => 'required',
            'price' => 'required',
            'stock' => 'required',
          ]);
          $product = Product::create($request->all());      
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
          ]);

        $product = Product::find($id);
        $product->update($request->all());
        return response()->json([$product]);
    }

    public function destroy ($id) {
        $product = Product::find($id);
        $product -> delete();
        return response()->json(null, 204);
    }
}

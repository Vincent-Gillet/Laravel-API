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

class CategoryController extends Controller
{
    public function index () {
        $categories = Category::all();
        return response()->json([$categories]);
    }

    public function store (Request $request) {
        $request->validate([
            'title' => 'required|max:255',
            'description' => 'required',
          ]);
          $categorie = Category::create($request->all());      
          return response()->json([$categorie]);
    }

    public function show ($id) {
        $categorie = Category::find($id);
        return response()->json($categorie);
    }

    public function update (Request $request, $id) {
        $request->validate([
            'title' => 'required|max:255',
            'description' => 'required',
          ]);

        $categorie = Category::find($id);
        $categorie->update($request->all());
        return response()->json([$categorie]);
    }

    public function destroy ($id) {
        $categorie = Category::find($id);
        $categorie -> delete();
        return response()->json(null, 204);
    }
}

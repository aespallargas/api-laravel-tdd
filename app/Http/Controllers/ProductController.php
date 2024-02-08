<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{

    public function index() {
        $products = Product::get();
        return response()->json($products);
    }

    public function store(){

        $this->validate(request(), [
            "name" => "required|string|max:100|unique:products",
            "description" => "required|string|max:1000",
            "stock" => "required|numeric",
            "available" => "required|boolean",
        ]);

        Product::create([
            "name" => request("name"),
            "description" => request("description"),
            "stock" => request("stock"),
            "available" => request("available"),
        ]);
        return response()->json([], 201);
    }

    public function show(int $id) {
        $product = Product::findOrFail($id);
        return response()->json($product);
    }

    public function findByName(string $name) {
        $product = Product::whereName($name)->firstOrFail();
        return response()->json($product);
    }

    public function update(int $id) {
        $product = Product::findOrFail($id);
        $product->update(request()->all());
        return response()->json($product);
    }

    public function destroy(int $id) {
        $product = Product::findOrFail($id);
        $product->delete();
        return response()->json([], 201);
    }
}

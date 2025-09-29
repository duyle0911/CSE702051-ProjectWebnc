<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Get all categories with products
        $categories = Category::withCount('products')
            ->having('products_count', '>', 0)
            ->orderBy('category_name')
            ->get();

        // Get latest products for each category (10 products per category)
        $categoriesWithProducts = [];

        foreach ($categories as $category) {
            $latestProducts = Product::where('category_id', $category->category_id)
                ->where('stock_quantity', '>', 0)
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get();

            if ($latestProducts->count() > 0) {
                $categoriesWithProducts[] = [
                    'category' => $category,
                    'products' => $latestProducts
                ];
            }
        }

        return view('home', compact('categories', 'categoriesWithProducts'));
    }

    public function search(Request $request)
    {
        $query = $request->get('q');

        if (empty($query)) {
            return redirect()->route('home');
        }

        $products = Product::with('category')
            ->where('name', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->where('stock_quantity', '>', 0)
            ->paginate(20);

        return view('products.search', compact('products', 'query'));
    }
}

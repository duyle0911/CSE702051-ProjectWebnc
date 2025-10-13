<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index(Request $request)
    {
        $query = Product::with('category');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        $products = $query->orderBy('created_at', 'desc')->paginate(15);
        $categories = Category::all();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,category_id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'variants.*.variant_name' => 'required|string|max:100',
            'variants.*.additional_price' => 'required|numeric|min:0',
            'variants.*.stock_quantity' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();

        try {
            $imageUrl = null;
            if ($request->hasFile('image')) {
                $imageUrl = $request->file('image')->store('products', 'public');
            }

            $product = Product::create([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'stock_quantity' => $request->stock_quantity,
                'category_id' => $request->category_id,
                'image_url' => $imageUrl,
            ]);

            // Create variants if provided
            if ($request->has('variants')) {
                foreach ($request->variants as $variantData) {
                    ProductVariant::create([
                        'product_id' => $product->product_id,
                        'variant_name' => $variantData['variant_name'],
                        'additional_price' => $variantData['additional_price'],
                        'stock_quantity' => $variantData['stock_quantity'],
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('admin.products.index')->with('success', 'Product created successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error creating product: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Product $product)
    {
        $product->load(['category', 'variants']);
        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        $product->load('variants');
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,category_id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'variants' => 'nullable|array',
            'variants.*.variant_id' => 'nullable|integer|exists:product_variants,variant_id',
            'variants.*.variant_name' => 'required|string|max:255',
            'variants.*.additional_price' => 'required|numeric|min:0',
            'variants.*.stock_quantity' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();

        try {
            // Update main product info
            $updateData = [
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'stock_quantity' => $request->stock_quantity,
                'category_id' => $request->category_id,
            ];

            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image
                if ($product->image_url) {
                    Storage::disk('public')->delete($product->image_url);
                }
                $updateData['image_url'] = $request->file('image')->store('products', 'public');
            }

            $product->update($updateData);

            // Handle variants
            $this->updateProductVariants($product, $request->variants ?? []);

            DB::commit();
            return redirect()->route('admin.products.index')->with('success', 'Cập nhật sản phẩm thành công!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Lỗi khi cập nhật sản phẩm: ' . $e->getMessage())->withInput();
        }
    }

    private function updateProductVariants(Product $product, array $variants)
    {
        // Get existing variant IDs
        $existingVariantIds = $product->variants->pluck('variant_id')->toArray();

        // Get variant IDs from request
        $requestVariantIds = collect($variants)
            ->filter(function ($variant) {
                return !empty($variant['variant_id']);
            })
            ->pluck('variant_id')
            ->toArray();

        // Delete variants that are not in the request
        $variantsToDelete = array_diff($existingVariantIds, $requestVariantIds);
        if (!empty($variantsToDelete)) {
            ProductVariant::whereIn('variant_id', $variantsToDelete)->delete();
        }

        // Process each variant from request
        foreach ($variants as $variantData) {
            if (!empty($variantData['variant_id'])) {
                // Update existing variant
                $variant = ProductVariant::find($variantData['variant_id']);
                if ($variant && $variant->product_id == $product->product_id) {
                    $variant->update([
                        'variant_name' => $variantData['variant_name'],
                        'additional_price' => $variantData['additional_price'],
                        'stock_quantity' => $variantData['stock_quantity'],
                    ]);
                }
            } else {
                // Create new variant
                ProductVariant::create([
                    'product_id' => $product->product_id,
                    'variant_name' => $variantData['variant_name'],
                    'additional_price' => $variantData['additional_price'],
                    'stock_quantity' => $variantData['stock_quantity'],
                ]);
            }
        }
    }

    public function destroy(Product $product)
    {
        DB::beginTransaction();

        try {
            // Delete image
            if ($product->image_url) {
                Storage::disk('public')->delete($product->image_url);
            }

            // Delete variants (will cascade due to foreign key)
            $product->variants()->delete();

            // Delete product
            $product->delete();

            DB::commit();
            return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error deleting product: ' . $e->getMessage());
        }
    }
}

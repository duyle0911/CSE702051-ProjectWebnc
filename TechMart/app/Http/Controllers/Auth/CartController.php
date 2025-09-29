<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the cart page
     *
     * @return View
     */
    public function index(): View
    {
        /** @var User $user */
        $user = Auth::user();
        $cartItems = $user->cartItems()->with(['product', 'productVariant'])->get();

        $total = $cartItems->sum(function ($item) {
            return $item->quantity * $item->price;
        });

        return view('cart.index', compact('cartItems', 'total'));
    }

    /**
     * Get cart count for AJAX
     */
    public function getCount(): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $count = $user->cartItems()->sum('quantity');

        return response()->json(['count' => $count]);
    }

    /**
     * Add product to cart (renamed from add to store)
     *
     * @param Request $request
     * @return RedirectResponse|JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,product_id',
            'quantity' => 'required|integer|min:1',
            'variant_id' => 'nullable|exists:product_variants,variant_id'
        ]);

        $product = Product::findOrFail($request->product_id);

        // Check stock
        if ($product->stock_quantity < $request->quantity) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không đủ hàng trong kho'
                ]);
            }
            return back()->with('error', 'Không đủ hàng trong kho');
        }

        /** @var User $user */
        $user = Auth::user();

        // Check if item already exists in cart
        $existingItem = CartItem::where('user_id', $user->id)
            ->where('product_id', $request->product_id)
            ->where('variant_id', $request->variant_id)
            ->first();

        if ($existingItem) {
            // Update quantity
            $newQuantity = $existingItem->quantity + $request->quantity;

            if ($product->stock_quantity < $newQuantity) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Không đủ hàng trong kho'
                    ]);
                }
                return back()->with('error', 'Không đủ hàng trong kho');
            }

            $existingItem->update(['quantity' => $newQuantity]);
        } else {
            // Create new cart item
            $price = $product->price;

            // Add variant price if applicable
            if ($request->variant_id) {
                $variant = ProductVariant::findOrFail($request->variant_id);
                $price += $variant->additional_price;
            }

            CartItem::create([
                'user_id' => $user->id,
                'product_id' => $request->product_id,
                'variant_id' => $request->variant_id,
                'quantity' => $request->quantity,
                'price' => $price
            ]);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã thêm sản phẩm vào giỏ hàng'
            ]);
        }

        return back()->with('success', 'Đã thêm sản phẩm vào giỏ hàng');
    }

    /**
     * Update cart item quantity
     *
     * @param Request $request
     * @param CartItem $cartItem
     * @return RedirectResponse
     */
    public function update(Request $request, CartItem $cartItem): RedirectResponse
    {
        // Check if cart item belongs to current user
        if ($cartItem->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        // Check stock
        if ($cartItem->product->stock_quantity < $request->quantity) {
            return back()->with('error', 'Không đủ hàng trong kho');
        }

        $cartItem->update(['quantity' => $request->quantity]);

        return back()->with('success', 'Đã cập nhật giỏ hàng');
    }

    /**
     * Remove item from cart
     *
     * @param CartItem $cartItem
     * @return RedirectResponse
     */
    public function remove(CartItem $cartItem): RedirectResponse
    {
        // Check if cart item belongs to current user
        if ($cartItem->user_id !== Auth::id()) {
            abort(403);
        }

        $cartItem->delete();

        return back()->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng');
    }

    /**
     * Clear all items from cart
     *
     * @return RedirectResponse
     */
    public function clear(): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $user->cartItems()->delete();

        return back()->with('success', 'Đã xóa tất cả sản phẩm khỏi giỏ hàng');
    }
}

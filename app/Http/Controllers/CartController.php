<?php
namespace App\Http\Controllers;

use App\Models\{Order, Product, ProductVariant};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    private function lines()
    {
        return collect(session('cart', []))->map(function ($i, $key) {
            $p = Product::with('media')->find($i['product_id']);
            $v = $i['variant_id'] ? ProductVariant::find($i['variant_id']) : null;
            if (!$p) return null;
            $price = $v?->price ?? $p->price;
            return (object) [
                'key' => $key, 'product' => $p, 'variant' => $v,
                'qty' => $i['qty'], 'price' => $price, 'total' => $price * $i['qty'],
            ];
        })->filter();
    }

    private function totals($lines, string $zone = 'dhaka'): array
    {
        $sub = $lines->sum('total');
        $ship = $sub >= config('shop.free_shipping_over') || $sub == 0 ? 0
            : config($zone === 'dhaka' ? 'shop.shipping_dhaka' : 'shop.shipping_outside');
        return [$sub, $ship, $sub + $ship];
    }

    public function index() { $lines = $this->lines(); [$sub] = $this->totals($lines); return view('cart.index', compact('lines', 'sub')); }

    public function add(Request $r)
    {
        $d = $r->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'nullable|exists:product_variants,id',
            'qty'        => 'nullable|integer|min:1|max:10',
        ]);
        $key  = $d['product_id'].'-'.($d['variant_id'] ?? 0);
        $cart = session('cart', []);
        $cart[$key] = [
            'product_id' => $d['product_id'], 'variant_id' => $d['variant_id'] ?? null,
            'qty' => min(10, ($cart[$key]['qty'] ?? 0) + ($d['qty'] ?? 1)),
        ];
        session(['cart' => $cart]);
        return back()->with('ok', 'Added to cart');
    }

    public function update(Request $r, string $key)
    {
        $cart = session('cart', []);
        if (isset($cart[$key])) { $cart[$key]['qty'] = max(1, min(10, (int) $r->qty)); session(['cart' => $cart]); }
        return back();
    }

    public function remove(string $key)
    {
        $cart = session('cart', []); unset($cart[$key]); session(['cart' => $cart]);
        return back();
    }

    public function checkout()
    {
        $lines = $this->lines();
        abort_if($lines->isEmpty(), 404);
        return view('cart.checkout', ['lines' => $lines, 'totals' => $this->totals($lines)]);
    }

    public function placeOrder(Request $r)
    {
        $d = $r->validate([
            'name' => 'required|string|max:120', 'phone' => ['required', 'regex:/^(?:\+?88)?01[3-9]\d{8}$/'],
            'address' => 'required|string|max:500', 'zone' => 'required|in:dhaka,outside',
            'payment_method' => 'required|in:cod,bkash,nagad,sslcommerz',
        ]);
        $lines = $this->lines();
        abort_if($lines->isEmpty(), 404);
        [$sub, $ship, $total] = $this->totals($lines, $d['zone']);

        $order = DB::transaction(function () use ($d, $lines, $sub, $ship, $total) {
            $order = Order::create($d + [
                'user_id' => auth()->id(), 'subtotal' => $sub, 'shipping' => $ship, 'total' => $total,
            ]);
            foreach ($lines as $l) {
                $order->items()->create([
                    'product_id' => $l->product->id, 'variant_id' => $l->variant?->id,
                    'name' => $l->product->name.($l->variant ? " ({$l->variant->name})" : ''),
                    'price' => $l->price, 'qty' => $l->qty,
                ]);
            }
            return $order;
        });

        session()->forget('cart');
        // bkash / nagad / sslcommerz: redirect to gateway here, COD ends below
        return redirect()->route('home')->with('ok', "Order #{$order->id} placed!");
    }
}
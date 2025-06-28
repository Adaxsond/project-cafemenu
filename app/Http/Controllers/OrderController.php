<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Enums\OrderStatus;
use App\Events\OrderPlaced;

class OrderController extends Controller
{
    /**
     * Menampilkan halaman menu utama.
     * Logika di sini diperbarui untuk memisahkan pesanan aktif dan selesai.
     */
    public function index(Request $request)
    {
        $tableNumber = $request->query('tableNumber');
        if (!$tableNumber) {
            return 'URL tidak valid. Mohon scan ulang QR Code di meja Anda.';
        }

        $categories = Category::whereHas('products')->with('products')->get();
        
        $allOrders = Order::where('table_number', $tableNumber)
                                ->with('items.product')
                                ->latest()
                                ->get();

        // Pisahkan pesanan menjadi dua grup: selesai/dibatalkan dan yang lainnya (aktif)
        [$finishedOrders, $activeOrders] = $allOrders->partition(function ($order) {
            return in_array($order->status, [OrderStatus::COMPLETED, OrderStatus::CANCELLED]);
        });

        // Kirim dua variabel terpisah ke view
        return view('menu', compact('categories', 'tableNumber', 'activeOrders', 'finishedOrders'));
    }

    /**
     * Menampilkan halaman keranjang belanja.
     */
    public function cart(Request $request)
    {
        $tableNumber = $request->query('tableNumber');
        return view('cart', compact('tableNumber'));
    }
    
    /**
     * Menampilkan halaman checkout/pembayaran.
     */
    public function checkout(Request $request)
    {
        $tableNumber = $request->query('tableNumber');
        $cart = json_decode($request->query('cart'), true);
        if (!$tableNumber || empty($cart)) {
            return redirect()->route('order.index', ['tableNumber' => $tableNumber])->with('error', 'Keranjang kosong atau link tidak valid.');
        }
        return view('checkout', compact('tableNumber', 'cart'));
    }

    /**
     * Menyimpan pesanan akhir dari keranjang ke database.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'table_number' => 'required|string',
            'cart' => 'required|json',
        ]);
        if ($validator->fails()) { return response()->json(['success' => false, 'message' => 'Data tidak valid.'], 400); }
        $cartItems = json_decode($request->cart, true);
        if (empty($cartItems)) { return response()->json(['success' => false, 'message' => 'Keranjang kosong.'], 400); }

        try {
            $order = DB::transaction(function () use ($cartItems, $request) {
                $totalPrice = array_reduce($cartItems, fn($carry, $item) => $carry + ($item['price'] * $item['quantity']), 0);
                $order = Order::create([
                    'table_number' => $request->table_number,
                    'total_price' => $totalPrice,
                    'status' => OrderStatus::PENDING,
                ]);
                $orderItemsData = array_map(fn($item) => ['product_id' => $item['id'], 'quantity' => $item['quantity'], 'price' => $item['price'], 'notes' => $item['notes'] ?? null,], $cartItems);
                $order->items()->createMany($orderItemsData);
                return $order;
            });
            
            OrderPlaced::dispatch($order->load('items.product'));
            
            return response()->json(['success' => true, 'order_id' => $order->id]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal membuat pesanan.'], 500);
        }
    }

    /**
     * Menampilkan halaman status untuk satu pesanan spesifik.
     */
    public function status(Order $order)
    {
        $order->load('items.product');
        return view('status', compact('order'));
    }
}
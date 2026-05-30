<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Order;
use App\Traits\ImageUploadTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    use ImageUploadTrait;

    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'Admin') {
            $orders = Order::with(['user:id,name,email', 'items.menu:id,name,price'])
                ->latest()
                ->get();
        } else {
            $orders = Order::with(['items.menu:id,name,price'])
                ->where('user_id', $user->id)
                ->latest()
                ->get();
        }

        return response()->json($orders);
    }

    public function show(Request $request, $id)
    {
        $user  = $request->user();
        $order = Order::with(['user:id,name,email', 'items.menu:id,name,price'])->find($id);

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        if ($user->role !== 'Admin' && $order->user_id !== $user->id) {
            return response()->json(['message' => 'Forbidden. You can only view your own orders.'], 403);
        }

        return response()->json($order);
    }


    public function store(Request $request)
    {
        $request->validate([
            'notes'                  => 'nullable|string',
            'payment_proof'          => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'items'                  => 'required|array|min:1',
            'items.*.menu_id'        => 'required|integer|exists:menus,id',
            'items.*.quantity'       => 'required|integer|min:1',
        ]);

        $paymentProofUrl = null;
        if ($request->hasFile('payment_proof')) {
            $paymentProofUrl = $this->uploadImage($request, 'payment_proof', 'payment_proofs');
        }

        $order = DB::transaction(function () use ($request, $paymentProofUrl) {
            $order = Order::create([
                'user_id'           => $request->user()->id,
                'status'            => 'Pending',
                'total_price'       => 0,
                'payment_proof_url' => $paymentProofUrl,
                'notes'             => $request->notes,
            ]);

            $totalPrice = 0;

            foreach ($request->items as $item) {
                $menu = Menu::find($item['menu_id']);

                $order->items()->create([
                    'menu_id'  => $menu->id,
                    'quantity' => $item['quantity'],
                    'price'    => $menu->price, 
                ]);

                $totalPrice += $menu->price * $item['quantity'];
            }

            $order->update(['total_price' => $totalPrice]);

            return $order;
        });

        return response()->json([
            'message' => 'Order placed successfully',
            'order'   => $order->load('items.menu:id,name,price'),
        ], 201);
    }
    public function uploadProof(Request $request, $id)
    {
        $request->validate([
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $order = Order::find($id);

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        if ($order->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden. You can only update your own orders.'], 403);
        }

        $lockedStatuses = ['Cooking', 'Ready', 'Completed', 'Cancelled'];
        if (in_array($order->status, $lockedStatuses)) {
            return response()->json([
                'message' => "Cannot upload proof. Order is already {$order->status}.",
            ], 422);
        }

        // Delete old proof if exists
        if ($order->payment_proof_url) {
            $oldPath = str_replace('/storage/', '', $order->payment_proof_url);
            Storage::disk('public')->delete($oldPath);
        }

        $order->payment_proof_url = $this->uploadImage($request, 'payment_proof', 'payment_proofs');
        $order->save();

        return response()->json([
            'message' => 'Payment proof uploaded successfully',
            'order'   => $order,
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        if ($order->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden. You can only cancel your own orders.'], 403);
        }

        if ($order->status !== 'Pending') {
            return response()->json([
                'message' => "Cannot cancel order. Current status is '{$order->status}'. Only Pending orders can be cancelled.",
            ], 422);
        }

        if ($order->payment_proof_url) {
            $oldPath = str_replace('/storage/', '', $order->payment_proof_url);
            Storage::disk('public')->delete($oldPath);
        }

        $order->delete();

        return response()->json(['message' => 'Order cancelled successfully']);
    }

    public function updateStatus(Request $request, $id)
    {
        $allowedStatuses = ['Confirmed', 'Cooking', 'Ready', 'Completed', 'Cancelled'];

        $request->validate([
            'status' => 'required|in:' . implode(',', $allowedStatuses),
        ]);

        $order = Order::find($id);

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        if ($order->status === 'Completed') {
            return response()->json(['message' => 'Order is already completed and cannot be changed.'], 422);
        }

        if ($order->status === 'Cancelled') {
            return response()->json(['message' => 'Order is already cancelled and cannot be changed.'], 422);
        }

        $order->status = $request->status;
        $order->save();

        return response()->json([
            'message' => "Order status updated to '{$order->status}'",
            'order'   => $order->load('items.menu:id,name,price'),
        ]);
    }
}

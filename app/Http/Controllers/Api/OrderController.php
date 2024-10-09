<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\ordersResource;

class OrderController extends Controller
{
    use ApiResponseTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user_id=Auth::user()->id;
        $orders =Order::with('product')->where('user_id', $user_id)->get();
        return $this->apiResponse( ordersResource::collection($orders),'this is all orders',200);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
             'product_id' => 'required|integer',
              'quantity' => 'required|integer',
            ]);
            $user_id=Auth::user()->id;
            $order=Order::create([
                'user_id' =>$user_id,
                'product_id' => $validatedData['product_id'],
                'quantity' => $validatedData['quantity']
            ]);
            return $this->apiResponse(new ordersResource($order),'order created',200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        return $this->apiResponse(new ordersResource($order),'this is  order',200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        // Validate the incoming request data
        $request->validate([
            'product_id' => 'nullable|exists:products,id', // Validate product_id if provided
            'quantity' => 'required|integer|min:1', // Assuming quantity must be a positive integer
        ]);

        // Update the order with the provided values or keep existing ones
        $order->update([
            'product_id' => $request->product_id ?? $order->product_id, // Use product_id from order if not provided
            // Uncomment the following line if you want to update user_id
            // 'user_id' => $request->user_id ?? $order->user_id,
            'quantity' => $request->quantity ?? $order->quantity,
        ]);

        // Return a response with the updated order
        return $this->apiResponse(new OrdersResource($order), 'Order updated successfully', 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        try {
            $user_id = Auth::id(); // Get the authenticated user's ID

            // Check if the order belongs to the authenticated user
            if ($order->user_id !== $user_id) {
                return $this->apiResponse([], 'You do not have permission to delete this order', 403);
            }

            // Delete the order
            $order->delete();

            return $this->apiResponse([], 'The order has been deleted successfully', 200);
        } catch (\Throwable $th) {
            Log::error($th);
            return $this->apiResponse([], 'An error occurred while deleting the order', 500);
        }

    }
}

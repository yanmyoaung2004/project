<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\PreorderRequest;
use App\Models\Preorder;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;

class PreorderController extends Controller
{
    // get all orders' list from the customer
    public function getPreorders($id)
    {
        $perPage = 12;
        $pageCount = ceil(count(Preorder::all()) / $perPage);
        $orders = Preorder::where('customer_id', $id)->latest()->paginate(12);

        return response()->json([
            'orders' => $orders,
            'pageCount' => $pageCount,
        ]);
    }

    // create order
    public function createPreorder(Request $request)
    {
        $pId = request('product_id');
        if ($pId) {
            $cleandata = [];
            if(request('is_urgent')){
                $isUrgentData = request()->validate([
                    'user_id' => 'required',
                    'order_quantity' => 'required', 
                    'date'=>'required',
                    'is_urgent'=>'required',
                    'truck_number'=>'required',
                    'capacity'=>['required','numeric','min:1'],
                    'driver_nrc'=>'required',
                    'total_price'=>'required|numeric|min:1'
                ]);
                $cleandata = $isUrgentData;
            }else{
                $preorderCleanData = $request->validate([
                    'user_id' => ['required',Rule::exists('users','id')],
                    'order_quantity' => 'required',
                    'latitude' => 'required',
                    'longitude' => 'required',
                    'full_location' => 'required',
                    'deliver_price'=>'required',
                    'total_price'=>'required'
                ]);
                $cleandata = $preorderCleanData;
            }
            $orderedItem = $request -> validate([
                "order_items" => "required|array",
            ]);

            $preorder = Preorder::create($cleandata);
            for ($i = 0; $i < count($pId); $i++) {
                $productId = $pId[$i];
                $quantity = $orderedItem['order_items'][$productId];
                $preorder->products()->attach($productId, ['quantity' => $quantity]);
            }
        }
    }

    // edit order list
    public function update(Preorder $preorder, PreorderRequest $request)
    {
        $cleandata = $request->validated();
        $preorder->update($cleandata);
        return response()->json([
            'message' => 'Your order has been updated successfully.'
        ]);

    }

}

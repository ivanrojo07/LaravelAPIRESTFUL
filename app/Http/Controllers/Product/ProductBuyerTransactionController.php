<?php

namespace App\Http\Controllers\Product;


use App\User;
use App\Product;
use App\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;

class ProductBuyerTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Product $product, User $buyer)
    {
        $data['quantity'] = (int)$request->input('quantity');
        $rules =['quantity'=>'required|integer|min:1'];
        $this->validate($request, $rules);

        if ($buyer->id == $product->seller_id) {
            return $this->errorResponse('El comprador debe ser diferente al vendedor', 409);
        }
        if(!$buyer->esVerificado){
            return $this->errorResponse('El comprador debe ser un usuario verificado', 409);
        }
        if(!$product->seller->esVerificado()){
            return $this->errorResponse('El vendedor debe ser un usuario verificado', 409);
        }
        if(!$product->estaDisponible()){
            return $this->errorResponse('El producto para esta transacción no esta disponible', 409);
        }
        if ($product->quantity < $request->quantity) {
            return $this->errorResponse('El producto no tiene la cantidad disponible requerida para esta transacción', 409);
        }
        
        return DB::transaction(function () use ($request, $product, $buyer) {
            $product->quantity -= $request->input('quantity');
            $product->save();
            $transaction = Transaction::create([
                'quantity' => $request->quantity,
                'buyer_id'=> $buyer->id,
                'product_id'=>$product->id,
                ]);
            return $this->showOne($transaction);
        });
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        //
    }
}

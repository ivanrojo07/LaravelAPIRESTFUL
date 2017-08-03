<?php

namespace App\Http\Controllers\Seller;

use App\User;
use App\Seller;
use App\Product as Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class SellerProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Seller $seller)
    {
        $products = $seller->products;
        return $this->showAll($products);
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
    public function store(Request $request, User $seller)
    {

        $rules =[
            'name' => 'required',
            'description' => 'required',
            'quantity' => 'required|integer|min:1',
            'image' => 'required|image'
        ];
        //$this->validate($request,$rules);
        // $data = $request->all();
        $data['name'] = $request->input('name');
        $data['description'] = $request->input('description');
        $data['quantity'] = (int)$request->input('quantity');
        $data['status'] = Product::PRODUCTO_NO_DISPONIBLE;
        // $data['image'] = '1.jpg';
        $data['image'] = $request->image->store('');
        $data['seller_id'] = $seller->id;
        $product = new Product($data);
        $product->save();   

        
        if ($product){
            return response()->json(['data'=>$product], 201);
        }
        else{
            return response()->json(['message'=>'Error al crear usuario'], 400);
        }
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Seller  $seller
     * @return \Illuminate\Http\Response
     */
    public function show(Seller $seller)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Seller  $seller
     * @return \Illuminate\Http\Response
     */
    public function edit(Seller $seller)
    {
        //

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Seller  $seller
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Seller $seller,Product $product)
    {
        $product = $product->findOrfail($product->id);
        $rules =[
            'quantity' =>'integer|min:1',
            'status' => 'in: ' . Product::PRODUCTO_DISPONIBLE . ',' . Product::PRODUCTO_NO_DISPONIBLE,
            'image' => 'image'
        ];
        $this->validate($request,$rules);
        $this->verificarVendedor($product,$seller);
        $product->fill($request->intersect([
            'name',
            'description',
            'quantity'
            ]));
        if ($request->has('name')) {
            $product->name = $request->name;
        }
        if ($request->has('description')) {
            $product->description = $request->description;

        }
        if ($request->has('quantity')) {
            $product->quantity = $request->quantity;
        }
        if($request->has('status')){
            $product->status = $request->status;

            if ($product->estaDisponible() && $product->categories()->count() == 0) {
                return $this->errorResponse('Un producto activo debe tener al menos una categoria', 409);
            }
        }
        if($product->isClean()){
            return $this->errorResponse('Se debe especificar al menos un valor diferente para actualizar', 422);
        }
        // var_dump($product);
        $product->save();
        return $this->showOne($product);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Seller  $seller
     * @return \Illuminate\Http\Response
     */
    public function destroy(Seller $seller, Product $product)
    {
        $product = $product->findOrfail($product->id);
        // var_dump($product);
        $this->verificarVendedor($product, $seller);
        Storage::delete($product->image);
        $product->delete($product);

        return $this->showOne($product);
    }
    protected function verificarVendedor(Product $product,Seller $seller){
        if($seller->id != $product->seller_id){
            throw new HttpException("Error Processing Request", 422);
            
            return $this->errorResponse('El vendedor especificado no es el vendedor del producto', 422);
        }
    }
}

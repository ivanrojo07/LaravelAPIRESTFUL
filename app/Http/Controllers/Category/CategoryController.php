<?php

namespace App\Http\Controllers\Category;

use App\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $categories = Category::all();
        return $this->showAll($categories); 
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
    public function store(Request $request)
    {
        //
        $rules=['name' => 'required'];
        $this->validate($request,$rules);
        $campos = $request->all();
        $category = Category::create($campos);
        if($category){
            return $this->showOne($category);
        }
        else{
            return $this->errorResponse('Error al guardar categoria',  500);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        //
        return $this->showOne($category);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
        //
        // $category = Category::findOrFail($category->id);
        $category->fill($request->intersect(['name','description']));
        if ($category->isClean()) {
            return $this->errorResponse('Debe especificar al menos un valor para actualizar', 422);
        }
        if ($request->has('name')) {
            $category->name = $request->name;
        }
        if ($request->has('description')) {
            # code...
            $category->description =$request->description;
        }
        $category->save();
        return $this->showOne($category);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        //
        $category = Category::findOrFail($category->id);
        $eliminar = $category->delete($category);
        if ($eliminar) {
            # code...
            return $this->showOne($category);
        }
        else{
            return $this->errorResponse('Error al eliminar usuario', 500);
        }

    }
}

<?php

namespace App\Http\Controllers;

use App\Filters\ProductFilter;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filter = new ProductFilter();
        $queryProducts = $filter->transform($request); // [[column, operator, value]]
    
        $perPage = $request->input('limit');
        $search = $request->input('search');
        $order = $request->input('sort');
    
        $products = Product::query();
    
        // Ricerca tramite titolo
        if (!empty($search)) {
            $products->where('title', 'like', "%$search%");
        }
    
        // Ordinare in base al prezzo
        if (isset($order)) {
            $products = $products->orderBy('price', $order);
        }
    
        // Se ci sono aggiunte alla query
        if (count($queryProducts) > 0) {
            $products->where($queryProducts);
        }
    
        // Applica la paginazione solo se è stato specificato un limite
        if (isset($perPage)) {
            $products = $products->paginate($perPage)->appends($request->query());
        } else {
            $products = $products->get(); // Altrimenti ottieni tutti i prodotti
        }
    
        return $products;
    }
    


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {

        return response()->json($product, 200);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        //
    }

}

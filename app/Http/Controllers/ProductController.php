<?php

namespace App\Http\Controllers;

use App\Product;
use Axiom\Rules\Decimal;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|Response|View
     */
    public function index()
    {
        $products = Product::latest()->paginate(5);

        return view('products.index',compact('products'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|Response|View
     */
    public function create()
    {
        return view('products.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'=>['required', 'string'],
            'sku' =>['string', 'unique:products'],
            'quantity'=>['required', 'integer'],
            'description'=>['string'],
            'image'=>['image', 'max:1014'],
            'price'=>['required', new Decimal(8,2)],
        ]);

        $url = '';

        if ($request->hasFile('image')) {
            //  Let's do everything here
            if ($request->file('image')->isValid()) {
                $image =  $request->file('image');
                $extension = $image->extension();
                $name =  $image->getClientOriginalName();
                $image->storeAs('/public', $name.".".$extension);
                $url = Storage::url($name.".".$extension);
            }else{
                return redirect()->route('products.create')->withErrors(['message1'=>'Error with image upload']);
            }
        }

        $product = new Product([
            'name'=> $request->input('name'),
            'sku'=>$request->input('sku'),
            'quantity'=>$request->input('quantity'),
            'description'=>$request->input('description'),
            'price'=>$request->input('price'),
            'image'=>$url,
        ]);

        $product->save();

        return redirect()->route('products.index')
            ->with('success','Product created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param Product $product
     * @return Application|Factory|Response|View
     */
    public function show(Product $product)
    {
        return view('products.show',compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Product $product
     * @return Application|Factory|Response|View
     */
    public function edit(Product $product)
    {
        return view('products.edit',compact('product'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Product $product
     * @return RedirectResponse
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name'=>['required', 'string'],
            'sku' =>['string', Rule::unique('products')->ignore($product->id),],
            'quantity'=>['required', 'integer'],
            'description'=>['string'],
            'image'=>['image'],
            'price'=>['required', new Decimal(8,2)],
        ]);

        $product->update($request->all());

        return redirect()->route('products.index')
            ->with('success','Product updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Product $product
     * @return RedirectResponse
     * @throws Exception
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')
            ->with('success','Product deleted successfully');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //passing to view product
        $title = 'Product';
        $product = Product::all();
        return view('product.index', compact('title','product'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('product.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //validasi
        $this->validate($request,[
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'image' => 'nullable|image',
            'barcode' => 'required|string|max:255',
            'price' => 'required|string|max:255',
            'quantity' => 'required|string|max:255',
            'status' => 'required|string|max:255',
        ]);
        //deklarasi image path
        $image_path = '';
        //cek apakah ada file gambar yang diupload
        if ($request->hasFile('image')) {
            $image_path = $request->file('image')->store('product', 'public');
        }
        //insert data
        $product = Product::create([
            'name'=>$request->input('name'),
            'description'=>$request->input('description'),
            'image'=>$image_path,
            'barcode'=>$request->input('barcode'),
            'price'=>$request->input('price'),
            'quantity'=>$request->input('quantity'),
            'status'=>$request->input('status'),
        ]);
        //redirect
        if (!$product) {
            return redirect()->back()->with(['error' => 'error page create']);
        } else {
            return redirect()->route('product.index')->with(['error' => '<strong>' . $product->name . '</strong> Tidak Ditambahkan']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        //redirect to edit page
        return view('product.edit')->with('product', $product);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        //validasi
        $this->validate($request,[
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'image' => 'nullable|image',
            'barcode' => 'required|string|max:255',
            'price' => 'required|string|max:255',
            'quantity' => 'required|string|max:255',
            'status' => 'required|string|max:255',
        ]);
        //deklarasi from
        $product->name = $request->input('name');
        $product->description = $request->input('description');
        $product->barcode = $request->input('barcode');
        $product->price = $request->input('price');
        $product->quantity = $request->input('quantity');
        $product->status = $request->input('status');
        //cek apakah ada gambar file yang di upload
        if($request->hasFile('image')){
            //delete old image
            Storage::delete($product->image);
        }
        //Storage image
        $image_path = $request->file('image')->store('product', 'public');
        //save to database image
        $product->image = $image_path;
        //save
        if(!$product->save()){
            return redirect()->back()->with('error', 'error page update');
        }
        return redirect()->route('product.index')->with('success', 'update data');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $product = Product::find($id);
        //delete image
        if($product->image){
            Storage::delete($product->image);
        }
        $product->delete();
        return redirect()->route('product.index')->with('success dihapus');
    }
}

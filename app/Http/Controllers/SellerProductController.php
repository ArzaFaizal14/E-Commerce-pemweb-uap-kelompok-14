<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SellerProductController extends Controller
{
    /**
     * List produk seller → pakai view store_dasboard.blade.php
     */
    public function index()
    {
        $user  = Auth::user();
        $store = $user->store;

        if (! $store || ! $store->is_verified) {
            abort(403, 'Toko Anda belum terverifikasi.');
        }

        $products = Product::with(['productCategory', 'productImages'])
            ->where('store_id', $store->id)
            ->latest()
            ->paginate(10);

        return view('store_dasboard', compact('store', 'products'));
    }

    /**
     * Form tambah produk → views/seller/products/create.blade.php
     */
    public function create()
    {
        $user  = Auth::user();
        $store = $user->store;

        if (! $store || ! $store->is_verified) {
            abort(403, 'Toko Anda belum terverifikasi.');
        }

        $categories = ProductCategory::all();

        return view('seller.products.create', compact('store', 'categories'));
    }

    /**
     * Simpan produk baru
     */
    public function store(Request $request)
    {
        $user  = Auth::user();
        $store = $user->store;

        if (! $store || ! $store->is_verified) {
            abort(403, 'Toko Anda belum terverifikasi.');
        }

        $validated = $request->validate([
            'name'                => ['required', 'string', 'max:255'],
            'product_category_id' => ['required', 'exists:product_categories,id'],
            'description'         => ['nullable', 'string'],
            'condition'           => ['required', 'in:new,second'],
            'price'               => ['required', 'numeric', 'min:0'],
            'stock'               => ['required', 'integer', 'min:0'],
            'weight'              => ['required', 'integer', 'min:0'],
            'image'               => ['nullable', 'image', 'max:2048'],
        ]);

        $validated['store_id'] = $store->id;
        $validated['slug']     = Str::slug($validated['name']) . '-' . Str::random(6);

        $product = Product::create($validated);

        // Jika ada gambar utama saat create, simpan sebagai thumbnail
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');

            ProductImage::create([
                'product_id'   => $product->id,
                'image'        => $path,
                'is_thumbnail' => true,
            ]);
        }

        return redirect()
            ->route('seller.products.index')
            ->with('success', 'Produk berhasil dibuat.');
    }

    /**
     * Form edit produk → views/seller/products/edit.blade.php
     */
    public function edit(Product $product)
    {
        $user  = Auth::user();
        $store = $user->store;

        if (! $store || ! $store->is_verified || $product->store_id !== $store->id) {
            abort(403, 'Anda tidak berhak mengedit produk ini.');
        }

        $categories = ProductCategory::all();

        return view('seller.products.edit', compact('product', 'categories', 'store'));
    }

    /**
     * Update produk (tanpa mengubah gambar).
     */
    public function update(Request $request, Product $product)
    {
        $user  = Auth::user();
        $store = $user->store;

        if (! $store || ! $store->is_verified || $product->store_id !== $store->id) {
            abort(403, 'Anda tidak berhak mengedit produk ini.');
        }

        $validated = $request->validate([
            'name'                => ['required', 'string', 'max:255'],
            'product_category_id' => ['required', 'exists:product_categories,id'],
            'description'         => ['nullable', 'string'],
            'condition'           => ['required', 'in:new,second'],
            'price'               => ['required', 'numeric', 'min:0'],
            'stock'               => ['required', 'integer', 'min:0'],
            'weight'              => ['required', 'integer', 'min:0'],
        ]);

        $product->update($validated);

        return redirect()
            ->route('seller.products.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    /**
     * Hapus produk
     */
    public function destroy(Product $product)
    {
        $user  = Auth::user();
        $store = $user->store;

        if (! $store || ! $store->is_verified || $product->store_id !== $store->id) {
            abort(403, 'Anda tidak berhak menghapus produk ini.');
        }

        // Hapus semua gambar fisik
        foreach ($product->productImages as $img) {
            if ($img->image) {
                Storage::disk('public')->delete($img->image);
            }
        }

        $product->delete();

        return redirect()
            ->route('seller.products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }

    /**
     * Halaman kelola gambar produk → views/seller/products/images.blade.php
     */
    public function images(Product $product)
    {
        $user  = Auth::user();
        $store = $user->store;

        if (! $store || ! $store->is_verified || $product->store_id !== $store->id) {
            abort(403, 'Anda tidak berhak mengelola gambar produk ini.');
        }

        $images = $product->productImages;

        return view('seller.products.images', compact('product', 'images'));
    }

    /**
     * Tambah gambar produk (create Product Image).
     */
    public function storeImage(Request $request, Product $product)
    {
        $user  = Auth::user();
        $store = $user->store;

        if (! $store || ! $store->is_verified || $product->store_id !== $store->id) {
            abort(403, 'Anda tidak berhak menambah gambar untuk produk ini.');
        }

        $data = $request->validate([
            'image'        => ['required', 'image', 'max:2048'],
            'is_thumbnail' => ['nullable', 'boolean'],
        ]);

        $path = $request->file('image')->store('products', 'public');

        ProductImage::create([
            'product_id'   => $product->id,
            'image'        => $path,
            'is_thumbnail' => $request->boolean('is_thumbnail'),
        ]);

        return redirect()
            ->route('seller.products.images', $product->id)
            ->with('success', 'Gambar produk berhasil ditambahkan.');
    }

    /**
     * Hapus 1 gambar produk (delete Product Image).
     */
    public function destroyImage(Product $product, ProductImage $image)
    {
        $user  = Auth::user();
        $store = $user->store;

        if (
            ! $store ||
            ! $store->is_verified ||
            $product->store_id !== $store->id ||
            $image->product_id !== $product->id
        ) {
            abort(403, 'Anda tidak berhak menghapus gambar produk ini.');
        }

        if ($image->image) {
            Storage::disk('public')->delete($image->image);
        }

        $image->delete();

        return redirect()
            ->route('seller.products.images', $product->id)
            ->with('success', 'Gambar produk berhasil dihapus.');
    }
}

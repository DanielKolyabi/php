<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /** @GET /api/product */
    public function index(): array
    {
        try {
            $data = Product::all();
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }
        return ['data' => $data ?? null, 'error' => $error ?? null];
    }

    /** @GET /api/product/{id} */
    public function show(int $id): array
    {
        try {
            $data = Product::query()->find($id);
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }
        return ['data' => $data ?? null, 'error' => $error ?? null];
    }

    /** @POST /api/product */
    public function store(Request $request): array
    {
        try {
            $request->validate([
                'sku' => 'required|max:255',
                'name' => 'required|max:255',
                'price' => 'required|decimal:0,3',
            ]);
            $data = Product::query()->create($request->all());
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }
        return ['data' => $data ?? null, 'error' => $error ?? null];
    }

    /** @PUT|@PATCH /api/product/{$id} */
    public function update(Request $request, $id): array
    {
        try {
            $request->validate([
                'sku' => 'max:255',
                'name' => 'max:255',
                'price' => 'decimal:0,3',
            ]);
            $data = (bool)Product::query()->find($id)->update($request->all());
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }
        return ['data' => $data ?? null, 'error' => $error ?? null];
    }

    /** @DELETE /api/product/{$id} */
    public function destroy(int $id): array
    {
        try {
            $data = (bool)Product::destroy($id);
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }
        return ['data' => $data ?? null, 'error' => $error ?? null];
    }
}

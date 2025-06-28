<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use App\Http\Resources\ProductResource;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProductController extends BaseController
{
    public function index(Request $request)
    {
        try {
            $this->authorize('viewAny', Product::class);

            $perPage = $request->input('per_page', 15);
            $products = Product::paginate($perPage);

            return $this->successResponse(
                ProductResource::collection($products),
                'Products retrieved successfully'
            );

        } catch (AuthorizationException $e) {
            return $this->forbiddenResponse('You do not have permission to view products');
        } catch (Exception $e) {
            return $this->serverErrorResponse('An error occurred while retrieving products');
        }
    }

    public function show($id)
    {
        try {
            $product = Product::findOrFail($id);
            $this->authorize('view', $product);

            return $this->successResponse(
                new ProductResource($product),
                'Product retrieved successfully'
            );

        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Product not found');
        } catch (AuthorizationException $e) {
            return $this->forbiddenResponse('You do not have permission to view this product');
        } catch (Exception $e) {
            return $this->serverErrorResponse('An error occurred while retrieving the product');
        }
    }

    public function store(ProductStoreRequest $request)
    {
        try {
            $this->authorize('create', Product::class);

            $product = Product::create($request->validated());

            return $this->successResponse(
                new ProductResource($product),
                'Product created successfully',
                201
            );

        } catch (AuthorizationException $e) {
            return $this->forbiddenResponse('You do not have permission to create products');
        } catch (Exception $e) {
            return $this->serverErrorResponse('An error occurred while creating the product');
        }
    }

    public function update(ProductUpdateRequest $request, $id)
    {
        try {
            $product = Product::findOrFail($id);
            $this->authorize('update', $product);

            $product->update($request->validated());

            return $this->successResponse(
                new ProductResource($product),
                'Product updated successfully'
            );

        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Product not found');
        } catch (AuthorizationException $e) {
            return $this->forbiddenResponse('You do not have permission to update this product');
        } catch (Exception $e) {
            return $this->serverErrorResponse('An error occurred while updating the product');
        }
    }

    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);
            $this->authorize('delete', $product);

            $product->delete();

            return $this->successResponse(
                null,
                'Product deleted successfully'
            );

        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Product not found');
        } catch (AuthorizationException $e) {
            return $this->forbiddenResponse('You do not have permission to delete this product');
        } catch (Exception $e) {
            return $this->serverErrorResponse('An error occurred while deleting the product');
        }
    }
}

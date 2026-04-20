<?php

namespace App\Http\Controllers\Api;

use App\DTO\Product\ProductData;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    private const PER_PAGE = 12;

    public function __construct(
        private readonly ProductService $products,
    ) {}

    public function index(Request $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $categoryId = $request->filled('category_id') ? $request->integer('category_id') : null;
        $search = $request->query('q');

        $paginator = $this->products->paginatePublic(
            self::PER_PAGE,
            $categoryId,
            is_string($search) && $search !== '' ? $search : null,
        );

        return ProductResource::collection($paginator);
    }

    public function show(int $id): ProductResource
    {
        $product = $this->products->find($id);

        return new ProductResource($product);
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = $this->products->create(ProductData::fromValidated($request->validated()));

        return (new ProductResource($product))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function update(UpdateProductRequest $request, Product $product): ProductResource
    {
        $product = $this->products->update($product, ProductData::fromValidated($request->validated()));

        return new ProductResource($product);
    }

    public function destroy(Product $product): Response
    {
        $this->products->delete($product);

        return response()->noContent();
    }
}

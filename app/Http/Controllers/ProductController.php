<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\View as ViewFactory;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $categories = Category::orderBy('name')->get();
        return view('products.index', compact('categories'));
    }

    public function partial(Request $request)
    {
        [$query, $page, $perPage] = $this->buildQuery($request);

        $products = $query->paginate($perPage, ['*'], 'page', $page);

        $html = ViewFactory::make('products.partials.list', compact('products'))->render();
        return response($html, 200)->header('Content-Type', 'text/html');
    }

    public function store(Request $request)
    {
        $data = $this->validateProduct($request);
        $product = Product::create($data);
        return response()->json(['ok' => true, 'product' => $product->load('category')], 201);
    }

    public function update(Request $request, Product $product)
    {
        $data = $this->validateProduct($request, $product->id);
        $product->update($data);
        return response()->json(['ok' => true, 'product' => $product->fresh()->load('category')]);
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return response()->json(['ok' => true]);
    }

    private function validateProduct(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'name' => ['required','string','max:255'],
            'price' => ['required','numeric','min:0'],
            'description' => ['nullable','string'],
            'category_id' => ['required','exists:categories,id'],
            'stock' => ['required','integer','min:0'],
            'status' => ['required','in:Active,Inactive'],
        ]);
    }

    private function buildQuery(Request $request): array
    {
        $search = $request->string('search')->toString();
        $categoryId = $request->integer('category_id') ?: null;
        $sort = $request->string('sort')->toString(); // name|price
        $dir = $request->string('dir')->toString() === 'desc' ? 'desc' : 'asc';
        $page = max(1, (int)$request->query('page', 1));
        $perPage = min(50, max(5, (int)$request->query('perPage', 10)));

        $q = Product::with('category');

        if ($search !== '') {
            $q->where('name', 'like', "%{$search}%");
        }
        if ($categoryId) {
            $q->where('category_id', $categoryId);
        }
        if (in_array($sort, ['name','price'])) {
            $q->orderBy($sort, $dir);
        } else {
            $q->latest('id');
        }

        return [$q, $page, $perPage];
    }
}
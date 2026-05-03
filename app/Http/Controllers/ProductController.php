<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\BusinessLocation;
use App\Models\Category;
use App\Models\Product;
use App\Models\TaxRate;
use App\Models\Unit;
use App\Models\Variation;
use App\Models\VariationLocationDetail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::where('business_id', auth()->user()->business_id)
            ->with(['category', 'brand', 'unit', 'variations']);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%')
                    ->orWhere('sku', 'like', '%'.$request->search.'%');
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->latest()->paginate(20);
        $categories = Category::where('business_id', auth()->user()->business_id)->orderBy('name')->get();

        return view('product.index', compact('products', 'categories'));
    }

    public function create(): View
    {
        $categories = Category::where('business_id', auth()->user()->business_id)->orderBy('name')->get();
        $brands = Brand::where('business_id', auth()->user()->business_id)->orderBy('name')->get();
        $units = Unit::where('business_id', auth()->user()->business_id)->orderBy('actual_name')->get();
        $taxRates = TaxRate::where('business_id', auth()->user()->business_id)->orderBy('name')->get();

        return view('product.create', compact('categories', 'brands', 'units', 'taxRates'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:single,variable',
            'unit_id' => 'nullable|exists:units,id',
            'brand_id' => 'nullable|exists:brands,id',
            'category_id' => 'nullable|exists:categories,id',
            'tax_id' => 'nullable|exists:tax_rates,id',
            'tax_type' => 'required|in:inclusive,exclusive',
            'enable_stock' => 'boolean',
            'alert_quantity' => 'nullable|numeric',
            'sku' => 'nullable|string|unique:products,sku',
            'image' => 'nullable|string',
            'product_description' => 'nullable|string',
            'purchase_price' => 'required|numeric',
            'sell_price' => 'required|numeric',
            'profit_percent' => 'nullable|numeric',
        ]);

        $product = Product::create(array_merge($data, [
            'business_id' => auth()->user()->business_id,
            'created_by' => auth()->id(),
        ]));

        $taxAmount = 0;
        if ($data['tax_id']) {
            $taxRate = TaxRate::find($data['tax_id']);
            if ($data['tax_type'] === 'inclusive') {
                $taxAmount = $data['sell_price'] - ($data['sell_price'] / (1 + ($taxRate->amount / 100)));
            } else {
                $taxAmount = $data['sell_price'] * ($taxRate->amount / 100);
            }
        }

        $variation = Variation::create([
            'name' => 'DUMMY',
            'product_id' => $product->id,
            'sub_sku' => $data['sku'],
            'default_purchase_price' => $data['purchase_price'],
            'dpp_inc_tax' => $data['purchase_price'],
            'profit_percent' => $data['profit_percent'] ?? 0,
            'default_sell_price' => $data['sell_price'],
            'sell_price_inc_tax' => $data['sell_price'] + $taxAmount,
        ]);

        if ($data['enable_stock']) {
            $location = BusinessLocation::where('business_id', auth()->user()->business_id)->first();
            if ($location) {
                VariationLocationDetail::create([
                    'product_id' => $product->id,
                    'variation_id' => $variation->id,
                    'location_id' => $location->id,
                    'qty_available' => 0,
                ]);
            }
        }

        return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product): View
    {
        $categories = Category::where('business_id', auth()->user()->business_id)->orderBy('name')->get();
        $brands = Brand::where('business_id', auth()->user()->business_id)->orderBy('name')->get();
        $units = Unit::where('business_id', auth()->user()->business_id)->orderBy('actual_name')->get();
        $taxRates = TaxRate::where('business_id', auth()->user()->business_id)->orderBy('name')->get();
        $product->load('variations');

        return view('product.edit', compact('product', 'categories', 'brands', 'units', 'taxRates'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'unit_id' => 'nullable|exists:units,id',
            'brand_id' => 'nullable|exists:brands,id',
            'category_id' => 'nullable|exists:categories,id',
            'tax_id' => 'nullable|exists:tax_rates,id',
            'tax_type' => 'required|in:inclusive,exclusive',
            'enable_stock' => 'boolean',
            'alert_quantity' => 'nullable|numeric',
            'sku' => 'nullable|string|unique:products,sku,'.$product->id,
            'product_description' => 'nullable|string',
            'is_inactive' => 'boolean',
            'not_for_selling' => 'boolean',
        ]);

        $product->update($data);

        return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->variations()->delete();
        $product->delete();

        return back()->with('success', 'Produk berhasil dihapus.');
    }

    public function search(Request $request): JsonResponse
    {
        $query = $request->get('q', '');
        $products = Product::where('business_id', auth()->user()->business_id)
            ->with(['variations', 'tax'])
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('sku', 'like', "%{$query}%");
            })
            ->where('is_inactive', false)
            ->where('not_for_selling', false)
            ->limit(20)
            ->get();

        return response()->json($products->map(function ($p) {
            $v = $p->variations->first();

            return [
                'id' => $p->id,
                'name' => $p->name,
                'sku' => $p->sku,
                'image' => $p->image,
                'variation_id' => $v?->id,
                'sell_price' => $v?->default_sell_price,
                'sell_price_inc_tax' => $v?->sell_price_inc_tax,
                'tax_type' => $p->tax_type,
                'tax_amount' => $p->tax?->amount,
            ];
        }));
    }
}

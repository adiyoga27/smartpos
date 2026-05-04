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
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $businessId = auth()->user()->business_id;

        $query = Product::where('business_id', $businessId)
            ->with(['category', 'brand', 'unit', 'variations.locationDetails']);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%')
                    ->orWhere('sku', 'like', '%'.$request->search.'%')
                    ->orWhereHas('variations', function ($vq) use ($request) {
                        $vq->where('sub_sku', 'like', '%'.$request->search.'%');
                    });
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->latest()->paginate(20);
        $categories = Category::where('business_id', $businessId)->orderBy('name')->get();

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
            'enable_stock' => 'nullable|boolean',
            'alert_quantity' => 'nullable|numeric',
            'sku' => 'nullable|string|unique:products,sku',
            'barcode' => 'nullable|string',
            'barcode_type' => 'nullable|in:C39,C128,EAN13,EAN8,UPCA,UPCE',
            'image' => 'nullable|image|max:5120',
            'product_description' => 'nullable|string',
            'purchase_price' => 'required|numeric',
            'sell_price' => 'required|numeric',
            'profit_percent' => 'nullable|numeric',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create(array_merge($data, [
            'business_id' => auth()->user()->business_id,
            'created_by' => auth()->id(),
        ]));

        $taxAmount = $this->calculateTaxAmount($data['sell_price'], $data['tax_id'] ?? null, $data['tax_type']);

        $variation = Variation::create([
            'name' => 'DUMMY',
            'product_id' => $product->id,
            'sub_sku' => $request->barcode ?: $data['sku'],
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
        $taxRates = TaxRate::where('business_id', auth()->user()->business_id)->with('subTaxes')->orderBy('name')->get();
        $product->load('variations', 'tax.subTaxes');
        $defaultVariation = $product->variations->first();

        return view('product.edit', compact('product', 'defaultVariation', 'categories', 'brands', 'units', 'taxRates'));
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
            'enable_stock' => 'nullable|boolean',
            'alert_quantity' => 'nullable|numeric',
            'sku' => 'nullable|string|unique:products,sku,'.$product->id,
            'barcode' => 'nullable|string',
            'barcode_type' => 'nullable|in:C39,C128,EAN13,EAN8,UPCA,UPCE',
            'image' => 'nullable|image|max:5120',
            'product_description' => 'nullable|string',
            'is_inactive' => 'boolean',
            'not_for_selling' => 'boolean',
            'purchase_price' => 'nullable|numeric|min:0',
            'sell_price' => 'nullable|numeric|min:0',
            'profit_percent' => 'nullable|numeric|min:0',
        ]);

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        if ($request->filled('barcode')) {
            $product->variations()->first()?->update(['sub_sku' => $request->barcode]);
        }

        $variation = $product->variations()->first();
        if ($variation) {
            $variation->update([
                'default_purchase_price' => $data['purchase_price'] ?? $variation->default_purchase_price,
                'dpp_inc_tax' => $data['purchase_price'] ?? $variation->dpp_inc_tax,
                'profit_percent' => $data['profit_percent'] ?? $variation->profit_percent,
                'default_sell_price' => $data['sell_price'] ?? $variation->default_sell_price,
                'sell_price_inc_tax' => $data['sell_price'] ?? $variation->sell_price_inc_tax,
            ]);
        }

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
        $page = $request->get('page', 1);
        $perPage = 24;

        $locationId = $request->get('location_id')
            ?? BusinessLocation::where('business_id', auth()->user()->business_id)->first()?->id;

        $baseQuery = Product::where('business_id', auth()->user()->business_id)
            ->with(['variations.locationDetails', 'tax.subTaxes'])
            ->where('is_inactive', false)
            ->where('not_for_selling', false);

        if ($query !== '') {
            $baseQuery->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('sku', 'like', "%{$query}%")
                    ->orWhereHas('variations', function ($vq) use ($query) {
                        $vq->where('sub_sku', 'like', "%{$query}%");
                    });
            });
            $products = $baseQuery->latest()->limit(20)->get();
        } else {
            $products = $baseQuery
                ->orderByRaw("(SELECT COALESCE(SUM(vld.qty_available), 0) FROM variation_location_details vld JOIN variations v ON v.id = vld.variation_id WHERE v.product_id = products.id AND vld.location_id = {$locationId}) > 0 DESC")
                ->orderBy('products.name')
                ->paginate($perPage, ['*'], 'page', $page);
        }

        $mapped = $products->map(function ($p) use ($locationId) {
            $v = $p->variations->first();
            $stockDetail = $v?->locationDetails?->where('location_id', $locationId)->first();

            return [
                'id' => $p->id,
                'name' => $p->name,
                'sku' => $p->sku,
                'barcode' => $v?->sub_sku,
                'image' => $p->image ? Storage::url($p->image) : null,
                'variation_id' => $v?->id,
                'sell_price' => $v?->default_sell_price,
                'sell_price_inc_tax' => $v?->sell_price_inc_tax,
                'purchase_price' => $v?->default_purchase_price,
                'tax_type' => $p->tax_type,
                'tax_id' => $p->tax_id,
                'tax_name' => $p->tax?->name,
                'tax_amount' => $p->tax?->amount,
                'tax_is_group' => $p->tax?->is_tax_group ?? false,
                'tax_sub_taxes' => $p->tax?->subTaxes?->map(fn ($t) => [
                    'id' => $t->id,
                    'name' => $t->name,
                    'amount' => $t->amount,
                ]),
                'enable_stock' => $p->enable_stock,
                'qty_available' => (float) ($stockDetail?->qty_available ?? 0),
            ];
        });

        if ($query !== '') {
            return response()->json($mapped);
        }

        return response()->json([
            'data' => $mapped,
            'current_page' => $products->currentPage(),
            'last_page' => $products->lastPage(),
        ]);
    }

    private function calculateTaxAmount(float $sellPrice, ?int $taxId, string $taxType): float
    {
        if (! $taxId) {
            return 0;
        }

        $taxRate = TaxRate::with('subTaxes')->find($taxId);
        if (! $taxRate) {
            return 0;
        }

        $taxAmount = $this->calcSingleTax($sellPrice, $taxRate->amount, $taxType);

        if ($taxRate->is_tax_group) {
            foreach ($taxRate->subTaxes as $subTax) {
                $taxAmount += $this->calcSingleTax($sellPrice, $subTax->amount, $taxType);
            }
        }

        return $taxAmount;
    }

    private function calcSingleTax(float $price, float $rate, string $type): float
    {
        if ($type === 'inclusive') {
            return $price - ($price / (1 + ($rate / 100)));
        }

        return $price * ($rate / 100);
    }
}

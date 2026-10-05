<?php

namespace App\Domain\Reconditionnement\Http\Controllers;

use App\Domain\Produits\Models\Product;
use App\Domain\Reconditionnement\Http\Requests\StoreRepackagingRequest;
use App\Domain\Reconditionnement\Models\Repackaging;
use App\Domain\Reconditionnement\Services\RepackagingService;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use InvalidArgumentException;

class RepackagingController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Repackaging::class);

        $query = Repackaging::with(['product', 'sourceStockUnit', 'targetStockUnit', 'createdBy'])->latest('repackaging_date')->latest('id');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('repackaging_number', 'like', "%{$search}%")
                  ->orWhereHas('product', function ($pq) use ($search) {
                      $pq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $repackagings = $query->paginate(15)->withQueryString();

        return view('reconditionnement.index', compact('repackagings'));
    }

    public function create(): View
    {
        $this->authorize('create', Repackaging::class);

        $products = Product::with('activeUnits')->where('is_active', true)->get();

        return view('reconditionnement.create', compact('products'));
    }

    public function store(StoreRepackagingRequest $request, RepackagingService $service): RedirectResponse
    {
        try {
            $repackaging = $service->executeRepackaging($request->validated(), $request->user());

            return redirect()->route('reconditionnement.show', $repackaging)
                ->with('success', "Le reconditionnement N° {$repackaging->repackaging_number} a été exécuté avec succès.");
        } catch (InvalidArgumentException $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function show(Repackaging $repackaging): View
    {
        $this->authorize('view', $repackaging);

        $repackaging->load(['product', 'sourceStockUnit', 'targetStockUnit', 'createdBy']);

        return view('reconditionnement.show', compact('repackaging'));
    }
}

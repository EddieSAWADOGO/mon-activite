<?php

namespace App\Domain\Pertes\Http\Controllers;

use App\Domain\Pertes\Http\Requests\StoreLossRequest;
use App\Domain\Pertes\Models\Loss;
use App\Domain\Pertes\Services\LossService;
use App\Domain\Produits\Models\Product;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LossController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Loss::class);

        $query = Loss::with(['product', 'stockUnit', 'createdBy'])->latest();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('loss_number', 'like', "%{$search}%")
                  ->orWhere('reason', 'like', "%{$search}%")
                  ->orWhereHas('product', function ($pq) use ($search) {
                      $pq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $losses = $query->paginate(15)->withQueryString();

        return view('pertes.index', compact('losses'));
    }

    public function create(): View
    {
        $this->authorize('create', Loss::class);

        $products = Product::with('activeUnits')->where('is_active', true)->get();

        return view('pertes.create', compact('products'));
    }

    public function store(StoreLossRequest $request, LossService $service): RedirectResponse
    {
        $loss = $service->recordLoss($request->validated(), $request->user());

        return redirect()->route('pertes.show', $loss)
            ->with('success', "La perte N° {$loss->loss_number} a été enregistrée et le stock décrémenté.");
    }

    public function show(Loss $loss): View
    {
        $this->authorize('view', $loss);

        $loss->load(['product', 'stockUnit', 'createdBy', 'customerReturn']);

        return view('pertes.show', compact('loss'));
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Services\OfferServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Offer\StoreOfferRequest;
use App\Http\Requests\Offer\UpdateOfferRequest;
use App\Models\Offer;

class OfferController extends Controller
{
    public function __construct(
        protected OfferServiceInterface $offerService
    ) {}

    public function index()
    {
        $this->authorize('viewAny', Offer::class);
        $offers = $this->offerService->getAllOffers();
        return view('admin.offers.index', compact('offers'));
    }

    public function create()
    {
        $this->authorize('create', Offer::class);
        $menuItemsByCategory = $this->offerService->getMenuItemsForSelector();
        return view('admin.offers.create', compact('menuItemsByCategory'));
    }

    public function store(StoreOfferRequest $request)
    {
        $this->authorize('create', Offer::class);
        $data = $request->validated();
        $data['is_active']       = $request->boolean('is_active');
        $data['applicable_days'] = $request->input('applicable_days', []);

        $this->offerService->createOffer($data);

        return redirect()->route('admin.offers.index')
            ->with('success', 'Offer created successfully.');
    }

    public function show(Offer $offer)
    {
        $this->authorize('view', $offer);
        $offer->load('menuItems.category');
        return view('admin.offers.show', compact('offer'));
    }

    public function edit(Offer $offer)
    {
        $this->authorize('update', $offer);
        $offer->load('menuItems');
        $menuItemsByCategory = $this->offerService->getMenuItemsForSelector();
        $selectedIds = $offer->menuItems->pluck('id')->toArray();
        return view('admin.offers.edit', compact('offer', 'menuItemsByCategory', 'selectedIds'));
    }

    public function update(UpdateOfferRequest $request, Offer $offer)
    {
        $this->authorize('update', $offer);
        $data = $request->validated();
        $data['is_active']       = $request->boolean('is_active');
        $data['applicable_days'] = $request->input('applicable_days', []);

        $this->offerService->updateOffer($offer->id, $data);

        return redirect()->route('admin.offers.index')
            ->with('success', 'Offer updated successfully.');
    }

    public function destroy(Offer $offer)
    {
        $this->authorize('delete', $offer);
        $this->offerService->deleteOffer($offer->id);
        return redirect()->route('admin.offers.index')
            ->with('success', 'Offer deleted successfully.');
    }

    public function toggleStatus(Offer $offer)
    {
        $this->authorize('update', $offer);
        $this->offerService->toggleStatus($offer->id);
        return back()->with('success', 'Offer status updated.');
    }
}

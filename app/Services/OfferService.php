<?php

namespace App\Services;

use App\Contracts\Services\OfferServiceInterface;
use App\Models\MenuItem;
use App\Models\Offer;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

class OfferService implements OfferServiceInterface
{
    public function getAllOffers(): Collection
    {
        return Offer::with('menuItems')->orderByDesc('created_at')->get();
    }

    public function getActiveOffers(): Collection
    {
        return Offer::where('is_active', true)
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now())
            ->get();
    }

    public function findOffer(int $id): Offer
    {
        return Offer::with('menuItems')->findOrFail($id);
    }

    public function createOffer(array $data): Offer
    {
        $menuItemIds = $data['menu_item_ids'] ?? [];
        unset($data['menu_item_ids']);

        $offer = Offer::create($data);

        if (!empty($menuItemIds)) {
            $offer->menuItems()->sync($menuItemIds);
        }

        return $offer;
    }

    public function updateOffer(int $id, array $data): Offer
    {
        $offer = Offer::findOrFail($id);

        $menuItemIds = $data['menu_item_ids'] ?? [];
        unset($data['menu_item_ids']);

        $offer->update($data);
        $offer->menuItems()->sync($menuItemIds);

        return $offer;
    }

    public function deleteOffer(int $id): void
    {
        $offer = Offer::findOrFail($id);
        $offer->menuItems()->detach();
        $offer->delete();
    }

    public function toggleStatus(int $id): Offer
    {
        $offer = Offer::findOrFail($id);
        $offer->update(['is_active' => !$offer->is_active]);
        return $offer;
    }

    public function getMenuItemsForSelector(): SupportCollection
    {
        return MenuItem::with('category.section')
            ->where('is_available', true)
            ->orderBy('name')
            ->get()
            ->groupBy(fn($item) => optional($item->category)->name ?? 'Uncategorized');
    }
}

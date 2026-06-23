<?php

namespace App\Contracts\Services;

use App\Models\Offer;
use Illuminate\Database\Eloquent\Collection;

interface OfferServiceInterface
{
    public function getAllOffers(): Collection;

    public function getActiveOffers(): Collection;

    public function findOffer(int $id): Offer;

    public function createOffer(array $data): Offer;

    public function updateOffer(int $id, array $data): Offer;

    public function deleteOffer(int $id): void;

    public function toggleStatus(int $id): Offer;

    /**
     * Return all menu items grouped by category for the pivot selector.
     */
    public function getMenuItemsForSelector(): \Illuminate\Support\Collection;
}

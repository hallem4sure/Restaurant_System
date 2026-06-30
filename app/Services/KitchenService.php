<?php

namespace App\Services;

use App\Contracts\Services\KitchenServiceInterface;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Collection;

class KitchenService implements KitchenServiceInterface
{
    public function getActiveOrders(): Collection
    {
        return Order::with(['items.menuItem', 'waiter', 'table'])
            ->whereIn('status', ['pending', 'confirmed', 'preparing'])
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function updateItemStatus(int $itemId, string $status): void
    {
        $item = OrderItem::findOrFail($itemId);

        // Enforce workflow: Do not allow Pending -> Ready directly
        if ($item->kitchen_status === 'pending' && $status === 'ready') {
            throw new \Exception('Cannot transition directly from pending to ready.');
        }

        $item->update(['kitchen_status' => $status]);

        $this->syncOrderStatus($item->order_id);
    }

    public function updateOrderStatus(int $orderId, string $status): void
    {
        $order = Order::findOrFail($orderId);

        if ($order->status === 'pending' && $status === 'ready') {
            throw new \Exception('Cannot transition directly from pending to ready.');
        }

        // If any item is still Pending or Preparing, the parent Order must not become Ready.
        if ($status === 'ready' && $order->items()->whereIn('kitchen_status', ['pending', 'preparing'])->exists()) {
            throw new \Exception('Cannot mark order as ready while items are still pending or preparing.');
        }

        $order->update(['status' => $status]);

        if (in_array($status, ['ready', 'served', 'completed'])) {
            $order->items()->update(['kitchen_status' => 'ready']);
        }
    }

    /**
     * Automatically update the parent order status based on its items.
     */
    protected function syncOrderStatus(int $orderId): void
    {
        $order = Order::with('items')->findOrFail($orderId);
        
        $totalItems = $order->items->count();
        if ($totalItems === 0) return;

        $readyItems = $order->items->where('kitchen_status', 'ready')->count();
        
        // When EVERY OrderItem becomes Ready, automatically update parent Order to Ready
        if ($readyItems === $totalItems) {
            $order->update(['status' => 'ready']);
        } else {
            // If any item is still Pending or Preparing, the parent Order must not become Ready.
            // But if some items are preparing, the order should be preparing.
            if ($order->items->where('kitchen_status', 'preparing')->count() > 0 || $readyItems > 0) {
                if ($order->status !== 'preparing') {
                    $order->update(['status' => 'preparing']);
                }
            } else {
                // All items are pending
                if ($order->status === 'ready') {
                    $order->update(['status' => 'preparing']); // Revert from ready if needed, or keep as is.
                }
            }
        }
    }
}

<?php

namespace App\Observers;

use App\Models\InventoryMovement;

class InventoryMovementObserver
{
    public function created(InventoryMovement $movement): void
    {
        $item = $movement->inventoryItem;
        $qty = (float) $movement->quantity;

        match ($movement->type) {
            'purchase' => $item->quantity_on_hand = (float) $item->quantity_on_hand + $qty,
            'issue' => $item->quantity_on_hand = (float) $item->quantity_on_hand - $qty,
            'adjustment' => $item->quantity_on_hand = (float) $item->quantity_on_hand + $qty,
            default => null,
        };

        $item->save();
    }
}

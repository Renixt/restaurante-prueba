<?php

namespace App\Policies;

use App\Models\InventoryItem;
use App\Models\User;

class InventoryItemPolicy
{
    public function viewAny(?User $user): bool { return true; }
    public function view(?User $user, InventoryItem $item): bool { return true; }
    public function create(?User $user): bool { return true; }
    public function update(?User $user, InventoryItem $item): bool { return true; }

    public function delete(?User $user, InventoryItem $item): bool
    {
        return !$item->recipes()->exists() && !$item->purchaseOrderItems()->exists();
    }
}

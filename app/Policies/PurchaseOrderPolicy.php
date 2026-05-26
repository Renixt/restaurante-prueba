<?php

namespace App\Policies;

use App\Models\PurchaseOrder;
use App\Models\User;

class PurchaseOrderPolicy
{
    public function viewAny(?User $user): bool { return true; }
    public function view(?User $user, PurchaseOrder $po): bool { return true; }
    public function create(?User $user): bool { return true; }
    public function update(?User $user, PurchaseOrder $po): bool { return $po->canEdit(); }

    public function delete(?User $user, PurchaseOrder $po): bool
    {
        return in_array($po->status, ['pendiente', 'cancelado']);
    }
}

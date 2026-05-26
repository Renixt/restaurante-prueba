<?php

namespace App\Policies;

use App\Models\Mesa;
use App\Models\User;

class MesaPolicy
{
    public function viewAny(?User $user): bool { return true; }
    public function view(?User $user, Mesa $mesa): bool { return true; }
    public function create(?User $user): bool { return true; }
    public function update(?User $user, Mesa $mesa): bool { return true; }

    public function delete(?User $user, Mesa $mesa): bool
    {
        return !$mesa->orders()->whereNotIn('status', ['pagado'])->exists();
    }
}

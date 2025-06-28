<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProductPolicy
{
    public function viewAny(User $user)
    {
        return $user->can('view products');
    }

    public function view(User $user, Product $product)
    {
        return $user->can('view products');
    }

    public function create(User $user)
    {
        return $user->can('create products');
    }

    public function update(User $user, Product $product)
    {
        return $user->can('edit products');
    }

    public function delete(User $user, Product $product)
    {
        return $user->can('delete products');
    }
}

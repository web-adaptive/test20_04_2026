<?php

namespace App\Repositories;

use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Support\Collection;

final class EloquentCategoryRepository implements CategoryRepositoryInterface
{
    public function allOrderedByName(): Collection
    {
        return Category::query()
            ->orderBy('name')
            ->get();
    }
}

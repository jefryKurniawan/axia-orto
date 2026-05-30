<?php

namespace App\Observers;

use App\Helpers\CacheHelper;
use App\Models\InventoryItem;

class InventoryObserver
{
    public function created(InventoryItem $item): void
    {
        CacheHelper::bumpVersion('inventory');
    }

    public function updated(InventoryItem $item): void
    {
        CacheHelper::bumpVersion('inventory');
    }

    public function deleted(InventoryItem $item): void
    {
        CacheHelper::bumpVersion('inventory');
    }
}

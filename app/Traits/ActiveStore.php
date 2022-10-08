<?php
namespace App\Traits;

use Auth;
use Illuminate\Database\Eloquent\Builder;

trait ActiveStore {

    public function scopeActiveStore(Builder $query, string $storeId = null): Builder
    {
        if (!$storeId) {
            $query->whereHas('store.users', function (Builder $query) {
                return $query->where('users.id', Auth::id());
            });
        } else {
            $query->where('store_id', $storeId);
        }

        return $query;
    }

}

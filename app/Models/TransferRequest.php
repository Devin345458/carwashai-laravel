<?php

namespace App\Models;

use App\Traits\WhoDidIt;
use Cake\ORM\Query;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\TransferRequest
 *
 * @property int $id
 * @property string $to_store_id
 * @property string $from_store_id
 * @property int $transfer_status_id
 * @property int $approved_by_id
 * @property int $order_item_id
 * @property string $denial_reason
 * @property int $created_by_id
 * @property int $updated_by_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User|null $approved_by
 * @property-read Store|null $from_store
 * @property-read OrderItem|null $order_item
 * @property-read Store|null $to_store
 * @method static Builder|TransferRequest newModelQuery()
 * @method static Builder|TransferRequest newQuery()
 * @method static Builder|TransferRequest query()
 * @method static Builder|TransferRequest whereApprovedById($value)
 * @method static Builder|TransferRequest whereCreatedAt($value)
 * @method static Builder|TransferRequest whereCreatedById($value)
 * @method static Builder|TransferRequest whereDenialReason($value)
 * @method static Builder|TransferRequest whereFromStoreId($value)
 * @method static Builder|TransferRequest whereId($value)
 * @method static Builder|TransferRequest whereOrderItemId($value)
 * @method static Builder|TransferRequest whereToStoreId($value)
 * @method static Builder|TransferRequest whereTransferStatusId($value)
 * @method static Builder|TransferRequest whereUpdatedAt($value)
 * @method static Builder|TransferRequest whereUpdatedById($value)
 * @mixin Eloquent
 * @property-read User|null $created_by
 * @property-read User|null $updated_by
 */
class TransferRequest extends Model
{
    use HasFactory;
    use WhoDidIt;

    public const TRANSFER_REQUEST = 1;
    public const TRANSFER_APPROVED_FOR_PICKUP = 2;
    public const TRANSFER_APPROVED_FOR_DELIVERY = 3;
    public const TRANSFER_COMPLETED = 4;
    public const TRANSFER_DENIED = 5;

    public function to_store(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'to_store_id');
    }

    public function from_store(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'from_store_id');
    }

    public function order_item(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function approved_by(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }

    public static function itemCount(int $transferStatusId, string $userId, $storeId = null): int
    {
        $q = static::where(['transfer_requests.transfer_status_id' => $transferStatusId]);

        if ($storeId) {
            $q->where(['transfer_requests.from_store_id' => $storeId]);
        } else {
            $q->whereHas('from_store.users', function (Builder $query) use ($userId) {
                return $query->where(['users.id' => $userId]);
            });
        }

        return $q->count();
    }

    public static function getTransfersByStatus(array $statusIds, string $userId, string $storeId = null): Builder
    {
        $transfers = static::whereIn('transfer_requests.transfer_status_id', $statusIds)
            ->with([
                'order_item.inventory.item',
                'from_store',
                'to_store',
                'approved_by'
            ]);

        if ($storeId) {
            $transfers->where(['transfer_requests.from_store_id' => $storeId]);
        } else {
            $transfers->whereHas('from_store.users', function (Builder $query) use ($userId) {
                return $query->where(['users.id' => $userId]);
            });
        }
        return $transfers;
    }
}

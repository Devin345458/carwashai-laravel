<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\RepairReminder
 *
 * @property int $id
 * @property string $reminder
 * @property int $repair_id
 * @property int $user_id
 * @property int $sent
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|RepairReminder newModelQuery()
 * @method static Builder|RepairReminder newQuery()
 * @method static Builder|RepairReminder query()
 * @method static Builder|RepairReminder whereCreatedAt($value)
 * @method static Builder|RepairReminder whereId($value)
 * @method static Builder|RepairReminder whereReminder($value)
 * @method static Builder|RepairReminder whereRepairId($value)
 * @method static Builder|RepairReminder whereSent($value)
 * @method static Builder|RepairReminder whereUpdatedAt($value)
 * @method static Builder|RepairReminder whereUserId($value)
 * @mixin Eloquent
 * @property-read Repair|null $repair
 * @property-read User|null $user
 */
class RepairReminder extends Model
{
    use HasFactory;

    public function repair(): BelongsTo
    {
        return $this->belongsTo(Repair::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

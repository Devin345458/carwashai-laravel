<?php

namespace App\Models;

use App\Traits\WhoDidIt;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\CompletedProcedureStep
 *
 * @property int $id
 * @property int $step_id
 * @property string $date
 * @property string|null $note
 * @property string $created_by_id
 * @property string $updated_by_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $completed
 * @property string|null $completed_by_id
 * @property string|null $completed_at
 * @property-read User|null $created_by
 * @property-read User|null $updated_by
 * @property-read User|null $completed_by
 * @method static Builder|CompletedProcedureStep newModelQuery()
 * @method static Builder|CompletedProcedureStep newQuery()
 * @method static Builder|CompletedProcedureStep query()
 * @method static Builder|CompletedProcedureStep whereCreatedAt($value)
 * @method static Builder|CompletedProcedureStep whereCreatedById($value)
 * @method static Builder|CompletedProcedureStep whereDate($value)
 * @method static Builder|CompletedProcedureStep whereId($value)
 * @method static Builder|CompletedProcedureStep whereNote($value)
 * @method static Builder|CompletedProcedureStep whereStepId($value)
 * @method static Builder|CompletedProcedureStep whereUpdatedAt($value)
 * @method static Builder|CompletedProcedureStep whereUpdatedById($value)
 * @method static Builder|CompletedProcedureStep whereCompleted($value)
 * @method static Builder|CompletedProcedureStep whereCompletedAt($value)
 * @method static Builder|CompletedProcedureStep whereCompletedById($value)
 * @mixin Eloquent
 */
class CompletedProcedureStep extends Model
{
    use HasFactory;
    use WhoDidIt;

    protected $casts = [
        'completed' => 'boolean'
    ];

    public function completed_by(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by_id');
    }
}

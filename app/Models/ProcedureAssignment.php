<?php

namespace App\Models;

use App\Traits\WhoDidIt;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\ProcedureAssignment
 *
 * @property int $id
 * @property int $procedure_id
 * @property string $date
 * @property string $assignment_id
 * @property string $created_by_id
 * @property string $updated_by_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|ProcedureAssignment newModelQuery()
 * @method static Builder|ProcedureAssignment newQuery()
 * @method static Builder|ProcedureAssignment query()
 * @method static Builder|ProcedureAssignment whereAssignmentId($value)
 * @method static Builder|ProcedureAssignment whereCreatedAt($value)
 * @method static Builder|ProcedureAssignment whereCreatedById($value)
 * @method static Builder|ProcedureAssignment whereDate($value)
 * @method static Builder|ProcedureAssignment whereId($value)
 * @method static Builder|ProcedureAssignment whereModifiedById($value)
 * @method static Builder|ProcedureAssignment whereProcedureId($value)
 * @method static Builder|ProcedureAssignment whereUpdatedAt($value)
 * @mixin Eloquent
 * @property-read \App\Models\User|null $created_by
 * @property-read \App\Models\Procedure|null $procedure
 * @property-read \App\Models\User|null $updated_by
 * @method static Builder|ProcedureAssignment whereUpdatedById($value)
 */
class ProcedureAssignment extends Model
{
    use HasFactory;
    use WhoDidIt;

    protected $casts = [
        'date' => 'date:Y-m-d'
    ];

    public function procedure(): BelongsTo
    {
        return $this->belongsTo(Procedure::class);
    }
}

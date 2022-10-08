<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;

/**
 * App\Models\ProcedureDay
 *
 * @property int $id
 * @property int $procedure_id
 * @property int $day_of_week
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|ProcedureDay newModelQuery()
 * @method static Builder|ProcedureDay newQuery()
 * @method static Builder|ProcedureDay query()
 * @method static Builder|ProcedureDay whereCreatedAt($value)
 * @method static Builder|ProcedureDay whereDayOfWeek($value)
 * @method static Builder|ProcedureDay whereId($value)
 * @method static Builder|ProcedureDay whereProcedureId($value)
 * @method static Builder|ProcedureDay whereUpdatedAt($value)
 * @mixin Eloquent
 */
class ProcedureDay extends Model
{
    use HasFactory;
}

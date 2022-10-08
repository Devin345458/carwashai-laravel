<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;

/**
 * App\Models\ProcedureStep
 *
 * @property int $id
 * @property int $procedure_id
 * @property string $instructions
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|ProcedureStep newModelQuery()
 * @method static Builder|ProcedureStep newQuery()
 * @method static Builder|ProcedureStep query()
 * @method static Builder|ProcedureStep whereCreatedAt($value)
 * @method static Builder|ProcedureStep whereId($value)
 * @method static Builder|ProcedureStep whereInstructions($value)
 * @method static Builder|ProcedureStep whereProcedureId($value)
 * @method static Builder|ProcedureStep whereUpdatedAt($value)
 * @mixin Eloquent
 */
class ProcedureStep extends Model
{
    use HasFactory;
}

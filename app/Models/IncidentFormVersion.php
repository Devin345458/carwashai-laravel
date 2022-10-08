<?php

namespace App\Models;

use App\Traits\WhoDidIt;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\IncidentFormVersion
 *
 * @property int $id
 * @property int $incident_form_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $version
 * @property array $data
 * @property string $created_by_id
 * @property string $updated_by_id
 * @property-read IncidentForm|null $incident_form
 * @property-read User|null $created_by
 * @property-read User|null $updated_by
 * @method static Builder|IncidentFormVersion newModelQuery()
 * @method static Builder|IncidentFormVersion newQuery()
 * @method static Builder|IncidentFormVersion query()
 * @method static Builder|IncidentFormVersion whereCreatedAt($value)
 * @method static Builder|IncidentFormVersion whereId($value)
 * @method static Builder|IncidentFormVersion whereIncidentFormId($value)
 * @method static Builder|IncidentFormVersion whereName($value)
 * @method static Builder|IncidentFormVersion whereStoreId($value)
 * @method static Builder|IncidentFormVersion whereUpdatedAt($value)
 * @method static Builder|IncidentFormVersion whereCreatedById($value)
 * @method static Builder|IncidentFormVersion whereData($value)
 * @method static Builder|IncidentFormVersion whereUpdatedById($value)
 * @method static Builder|IncidentFormVersion whereVersion($value)
 * @mixin Eloquent
 */
class IncidentFormVersion extends Model
{
    use HasFactory;
    use WhoDidIt;

    protected $casts = [
        'data' => 'array'
    ];

    public function incident_form(): BelongsTo
    {
        return $this->belongsTo(IncidentForm::class);
    }

    public function active_form(): HasMany
    {
        return $this->hasMany(IncidentForm::class, 'incident_form_version_id');
    }
}

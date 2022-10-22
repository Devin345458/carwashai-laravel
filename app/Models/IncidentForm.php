<?php

namespace App\Models;

use App\Traits\ActiveStore;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\IncidentForm
 *
 * @property int $id
 * @property string $name
 * @property int $incident_form_version_id
 * @property string $store_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property IncidentFormVersion|null $incident_form_version
 * @property IncidentFormVersion|null $current_version
 * @property-read Store|null $store
 * @property-read Collection|IncidentFormVersion[] $incident_form_versions
 * @property-read int|null $incident_form_versions_count
 * @method static Builder|IncidentForm newModelQuery()
 * @method static Builder|IncidentForm newQuery()
 * @method static Builder|IncidentForm query()
 * @method static Builder|IncidentForm whereCreatedAt($value)
 * @method static Builder|IncidentForm whereId($value)
 * @method static Builder|IncidentForm whereIncidentFormVersionId($value)
 * @method static Builder|IncidentForm whereName($value)
 * @method static Builder|IncidentForm whereStoreId($value)
 * @method static Builder|IncidentForm whereUpdatedAt($value)
 * @method static Builder|IncidentFormSubmission activeStore(?string $storeId = null)
 * @mixin Eloquent
 * @property-read Collection|\App\Models\IncidentFormSubmission[] $submissions
 * @property-read int|null $submissions_count
 */
class IncidentForm extends Model
{
    use HasFactory;
    use ActiveStore;

    protected $fillable = [
        'name',
        'incident_form_version_id',
        'store_id',
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function current_version(): BelongsTo
    {
        return $this->belongsTo(IncidentFormVersion::class, 'incident_form_version_id');
    }

    public function incident_form_versions(): HasMany
    {
        return $this->hasMany(IncidentFormVersion::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(IncidentFormSubmission::class);
    }
}

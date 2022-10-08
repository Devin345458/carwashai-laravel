<?php

namespace App\Models;

use App\Traits\ActiveStore;
use Auth;
use BeyondCode\Comments\Traits\HasComments;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * App\Models\IncidentFormSubmission
 *
 * @property int $id
 * @property array $data
 * @property int $incident_form_version_id
 * @property string $user_id
 * @property string $status
 * @property string $store_id
 * @property int $progress
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|ContactLog[] $contact_logs
 * @property-read int|null $contact_logs_count
 * @property-read IncidentFormVersion|null $incident_form_version
 * @property-read Collection|Recording[] $recordings
 * @property-read int|null $recordings_count
 * @property-read Store|null $store
 * @property-read User|null $user
 * @method static Builder|IncidentFormSubmission newModelQuery()
 * @method static Builder|IncidentFormSubmission newQuery()
 * @method static Builder|IncidentFormSubmission query()
 * @method static Builder|IncidentFormSubmission whereCreatedAt($value)
 * @method static Builder|IncidentFormSubmission whereData($value)
 * @method static Builder|IncidentFormSubmission whereId($value)
 * @method static Builder|IncidentFormSubmission whereIncidentFormVersionId($value)
 * @method static Builder|IncidentFormSubmission whereProgress($value)
 * @method static Builder|IncidentFormSubmission whereStatus($value)
 * @method static Builder|IncidentFormSubmission whereStoreId($value)
 * @method static Builder|IncidentFormSubmission whereUpdatedAt($value)
 * @method static Builder|IncidentFormSubmission whereUserId($value)
 * @mixin Eloquent
 * @property-read Collection|\BeyondCode\Comments\Comment[] $comments
 * @property-read int|null $comments_count
 * @method static Builder|IncidentFormSubmission activeStore(?string $storeId = null)
 */
class IncidentFormSubmission extends Model
{
    use HasFactory;
    use HasComments;
    use ActiveStore;
    use LogsActivity;

    const STATUS_RECEIVED = 'received';
    const STATUS_REVIEWING = 'reviewing';
    const STATUS_CONTACTING = 'contacting_client';
    const STATUS_QUOTE = 'getting_quote';
    const STATUS_DENIED = 'denied';
    const STATUS_ACCEPTED = 'accepted';

    protected $casts = [
        'data' => 'json'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function (IncidentFormSubmission $incidentFormSubmission) {
           $incidentFormSubmission->user_id = Auth::id();
        });
    }

    public function incident_form_version(): BelongsTo
    {
        return $this->belongsTo(IncidentFormVersion::class);
    }

    public function incident_form(): BelongsTo
    {
        return $this->belongsTo(IncidentForm::class);
    }


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function recordings(): HasMany
    {
        return $this->hasMany(Recording::class);
    }

    public function contact_logs(): HasMany
    {
        return $this->hasMany(ContactLog::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNotIn('status', [IncidentFormSubmission::STATUS_ACCEPTED, IncidentFormSubmission::STATUS_DENIED]);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logUnguarded()
        ->logOnlyDirty()
        ->setDescriptionForEvent(function ($eventName) {
            return match ($eventName) {
                'created' => Auth::user()->full_name . ' submitted an incident report for ' . $this->data['first_name'] . ' ' . $this->data['first_name'],
                default => Auth::user()->full_name . ' ' . $eventName . ' an incident report for ' . $this->data['first_name'] . ' ' . $this->data['first_name'],
            };
        })
        ->dontSubmitEmptyLogs()
        ->dontLogIfAttributesChangedOnly(['updated_at']);
    }
}

<?php

namespace App\Models;

use App\Traits\WhoDidIt;
use Auth;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * App\Models\ContactLog
 *
 * @property-read IncidentFormSubmission|null $incident_form_submission
 * @method static Builder|ContactLog newModelQuery()
 * @method static Builder|ContactLog newQuery()
 * @method static Builder|ContactLog query()
 * @mixin Eloquent
 * @property-read User|null $created_by
 * @property-read User|null $updated_by
 * @property int $id
 * @property string|null $when
 * @property string|null $spoke_to
 * @property string|null $details
 * @property int $incident_form_submission_id
 * @property string $created_by_id
 * @property string $updated_by_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Activitylog\Models\Activity[] $activities
 * @property-read int|null $activities_count
 * @method static Builder|ContactLog whereCreatedAt($value)
 * @method static Builder|ContactLog whereCreatedById($value)
 * @method static Builder|ContactLog whereDetails($value)
 * @method static Builder|ContactLog whereId($value)
 * @method static Builder|ContactLog whereIncidentFormSubmissionId($value)
 * @method static Builder|ContactLog whereSpokeTo($value)
 * @method static Builder|ContactLog whereUpdatedAt($value)
 * @method static Builder|ContactLog whereUpdatedById($value)
 * @method static Builder|ContactLog whereWhen($value)
 */
class ContactLog extends Model
{
    use HasFactory;
    use WhoDidIt;
    use LogsActivity;

    public function incident_form_submission(): BelongsTo
    {
        return $this->belongsTo(IncidentFormSubmission::class);
    }


    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logUnguarded()
            ->logOnlyDirty()
            ->setDescriptionForEvent(function () {
                return Auth::user()->full_name . ' contacted ' . $this->incident_form_submission->data['first_name'] . ' ' . $this->incident_form_submission->data['first_name'] . ' about their incident report';
            })
            ->dontSubmitEmptyLogs()
            ->dontLogIfAttributesChangedOnly(['updated_at']);
    }
}

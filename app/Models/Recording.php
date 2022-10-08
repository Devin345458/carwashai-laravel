<?php

namespace App\Models;

use Auth;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * App\Models\Recording
 *
 * @property int $id
 * @property string $camera
 * @property string $start_time
 * @property string $end_time
 * @property int $incident_form_submission_id
 * @property int $created_by_id
 * @property int $updated_by_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read IncidentFormSubmission|null $incident_form_submission
 * @method static Builder|Recording newModelQuery()
 * @method static Builder|Recording newQuery()
 * @method static Builder|Recording query()
 * @method static Builder|Recording whereCamera($value)
 * @method static Builder|Recording whereCreatedAt($value)
 * @method static Builder|Recording whereCreatedById($value)
 * @method static Builder|Recording whereEndTime($value)
 * @method static Builder|Recording whereId($value)
 * @method static Builder|Recording whereIncidentFormSubmissionId($value)
 * @method static Builder|Recording whereStartTime($value)
 * @method static Builder|Recording whereUpdatedAt($value)
 * @method static Builder|Recording whereUpdatedById($value)
 * @mixin Eloquent
 */
class Recording extends Model
{
    use HasFactory;
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
                return Auth::user()->full_name . ' added recording for ' . $this->incident_form_submission->data['first_name'] . ' ' . $this->incident_form_submission->data['first_name'] . ' incident report';
            })
            ->dontSubmitEmptyLogs()
            ->dontLogIfAttributesChangedOnly(['updated_at']);
    }
}

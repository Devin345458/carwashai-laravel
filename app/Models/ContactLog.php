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

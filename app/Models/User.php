<?php

namespace App\Models;

use App\Traits\ActiveStore;
use App\Traits\HasUniqueIdentifier;
use Auth;
use BeyondCode\Comments\Contracts\Commentator;
use Database\Factories\UserFactory;
use DB;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Throwable;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * App\Models\User
 *
 * @property int $id
 * @property string $email
 * @property string $password
 * @property string $first_name
 * @property string $last_name
 * @property int $active
 * @property string $role
 * @property string $company_id
 * @property string $active_store_id
 * @property int $file_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read DatabaseNotificationCollection|DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read int|null $tokens_count
 * @method static UserFactory factory(...$parameters)
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User query()
 * @method static Builder|User whereAbout($value)
 * @method static Builder|User whereActivationDate($value)
 * @method static Builder|User whereActive($value)
 * @method static Builder|User whereActiveStoreId($value)
 * @method static Builder|User whereCompanyId($value)
 * @method static Builder|User whereCreatedAt($value)
 * @method static Builder|User whereEmail($value)
 * @method static Builder|User whereFileId($value)
 * @method static Builder|User whereFirstName($value)
 * @method static Builder|User whereId($value)
 * @method static Builder|User whereLastName($value)
 * @method static Builder|User wherePassword($value)
 * @method static Builder|User whereRole($value)
 * @method static Builder|User whereTimeZone($value)
 * @method static Builder|User whereTosDate($value)
 * @method static Builder|User whereUpdatedAt($value)
 * @mixin Eloquent
 * @property-read Company|null $company
 * @property-read File|null $profile_image
 * @property-read Collection|Store[] $stores
 * @property-read int|null $stores_count
 * @property-read Store|null $active_store
 * @method static Builder|User activeStore(?string $storeId = null)
 * @property-read mixed $full_name
 */
class User extends Authenticatable implements JWTSubject, Commentator
{
    use HasFactory;
    use Notifiable;
    use HasUniqueIdentifier;

    public const ROLE_OWNER = 'owner';
    public const ROLE_MANAGER = 'manager';
    public const ROLE_WASH_ATTENDANT = 'wash_attendant';


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'password',
        'first_name',
        'last_name',
        'active',
        'role',
        'company_id',
        'active_store_id',
        'file_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    protected $appends = [
        'full_name'
    ];

    protected static function boot()
    {
        parent::boot();
        self::creating(function (User $user) {
            if ($user->password) {
                $user->password = Hash::make($user->password);
            }
        });

        self::saved(function (User $user) {
            if ($user->role === User::ROLE_OWNER) {
                $user->stores()->sync(Store::whereCompanyId($user->company_id)->pluck('id')->toArray());
            }
        });

        self::updating(function (User $user) {
            if (Hash::needsRehash($user->password)) {
                $user->password = Hash::make($user->password);
            }
        });
    }

    public function company() {
        return $this->belongsTo(Company::class);
    }

    public function stores() {
        return $this->belongsToMany(Store::class);
    }

    public function active_store() {
        return $this->belongsTo(Store::class, 'active_store_id');
    }

    public function profile_image() {
        return $this->belongsTo(File::class);
    }

    public function getFullNameAttribute() {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getJWTIdentifier(): string
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [];
    }

    /**
     * @param array $data The request data
     * @return User
     * @throws Throwable
     */
    public static function register(array $data): User
    {
        DB::beginTransaction();

        $company = Company::create([
            'name' => $data['company_name'],
            'email' => $data['email'],
            'billing_last_name' => $data['first_name'],
            'billing_first_name' => $data['last_name'],
            'allow_car_count' => false,
        ]);

        $user = User::create([
            'email' => $data['email'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'role' => 'owner',
            'password' => $data['password'],
            'company_id' => $company->id,
        ]);

        $store = Store::create([
            'name' => 'Store 1',
            'plan_id' => $data['plan_id'],
            'store_type_id' => 1,
            'company_id' => $company->id,
        ]);

        $store->users()->attach($user->id);

        DB::commit();

        return $user;
    }

    public function needsCommentApproval($model): bool
    {
        return false;
    }


    public function scopeActiveStore(Builder $query, string $storeId = null): Builder
    {
        if (!$storeId) {
            return $query->where('company_id', Auth::user()->company_id);
        } else {
            $query->whereHas('stores', function (Builder $query) use ($storeId) {
                $query->where('store_id', '=', $storeId);
            });
        }

        return $query;
    }
}

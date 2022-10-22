<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Auth;
use DB;
use Hash;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException;
use Throwable;

class UsersController extends Controller
{
    public function index(string $storeId = null): JsonResponse
    {
        $users = User::activeStore($storeId)->withCount('stores');

        if (filter_var(request('excludeOwners'), FILTER_VALIDATE_BOOL)) {
            $users->where('role', '<>', User::ROLE_OWNER);
        }

        $users = $users->get();

        return response()->json(compact('users'));
    }

    public function all(): JsonResponse
    {
        $users = User::activeStore()->withCount('stores')->paginate();

        return response()->json($users);
    }

    /**
     * Login a user
     * @throws ValidationException
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = $this->validate($request, [
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $token = Auth::attempt($credentials);
        if (!$token) {
            throw new UnauthorizedException('Invalid Login Credentials');
        }

        return $this->respondWithToken($token);
    }

    /**
     * Returns the logged in user
     */
    public function loggedInUser(): JsonResponse
    {
        return response()->json(['user' => Auth::user()]);
    }

    /**
     * Logout user
     *
     * @return void
     */
    public function logout(): void
    {
        Auth::logout();
    }

    /**
     * Initial Registration of a new customer and their first store and admin user
     *
     * @throws Throwable
     */
    public function register(): JsonResponse
    {
        $user = User::register(request()->input());

        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $token = Auth::login($user);

        return $this->respondWithToken($token);
    }

    public function edit(): JsonResponse
    {
        if (request('id') !== Auth::id()) {
            throw new UnauthorizedException('You may only edit your own profile');
        }
        $user = User::find(request('id'))->fill(request()->input());
        $user->save();

        return response()->json(compact('user'));
    }

    public function resetPassword(): JsonResponse
    {
        if (!Hash::check(request('current_password'), Auth::user()->password)) {
            throw new UnauthorizedException('Incorrect Current Password');
        }
        Auth::user()->password = request('password');
        Auth::user()->save();

        return $this->loggedInUser();
    }

    public function add(): JsonResponse
    {
        $user = User::create(request()->input());

        return response()->json(compact('user'));
    }

    public function store(string $storeId): JsonResponse
    {
            $users = User::whereHas('stores', function (Builder $query) use ($storeId) {
                return $query->where(['stores.id' => $storeId]);
            })
                ->withCount(['stores'])
                ->paginate();

        return response()->json($users);
    }

    public function upsert($storeId = null)
    {
        if (request('id')) {
            $user = User::find(request('id'))->load('stores');
            $user->fill(request()->input());
            $user->save();
        } else {
            $user = new User(request()->input());
            $user->company_id = Auth::user()->company_id;
            $user->save();
            $user->stores()->attach($storeId);
        }
    }

    public function addStore(User $user, $storeId)
    {
        $user->stores()->attach($storeId);
    }

    public function view(User $user): JsonResponse
    {
        return response()->json(compact('user'));
    }

    public function removeStore($userId, $storeId)
    {
        $user = User::find($userId);
        $user->stores()->detach($storeId);
    }

    public function checkEmail(string $email): JsonResponse
    {
        $user = User::where('email', $email)->first();
        return response()->json(compact('user'));
    }

    public function saveDeviceToken(): JsonResponse
    {
        Auth::user()->device_token = request()->input('token');
        Auth::user()->save();
        return response()->json(['success' => true]);
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     *
     * @return JsonResponse
     */
    protected function respondWithToken(string $token): JsonResponse
    {
        $user = Auth::user();

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'user' => $user,
            'expires_in' => Auth::factory()->getTTL() * 60 * 24
        ]);
    }
}

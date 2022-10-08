<?php

namespace App\Models;

use Closure;
use DB;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model as BaseModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Throwable;
use Validator;

/**
 * Class Model
 * @package App
 * @method $this|EloquentBuilder|null first($columns = ['*']) Execute the query and get the first result.
 * @method $this|EloquentBuilder firstOrFail($columns = ['*']) Execute the query and get the first result or throw an exception.
 * @method Collection|EloquentBuilder[] get($columns = ['*']) Execute the query as a "select" statement.
 * @method mixed value($column) Get a single column's value from the first result of a query.
 * @method mixed pluck($column) Get a single column's value from the first result of a query.
 * @method void chunk($count, callable $callback) Chunk the results of the query.
 * @method \Illuminate\Support\Collection lists($column, $key = null) Get an array with the values of a given column.
 * @method LengthAwarePaginator paginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null) Paginate the given query.
 * @method Paginator simplePaginate($perPage = null, $columns = ['*'], $pageName = 'page') Paginate the given query into a simple paginator.
 * @method int increment($column, $amount = 1, array $extra = []) Increment a column's value by a given amount.
 * @method int decrement($column, $amount = 1, array $extra = []) Decrement a column's value by a given amount.
 * @method void onDelete(Closure $callback) Register a replacement for the default delete function.
 * @method $this[] getModels($columns = ['*']) Get the hydrated models without eager loading.
 * @method array eagerLoadRelations(array $models) Eager load the relationships for the models.
 * @method array loadRelation(array $models, $name, Closure $constraints) Eagerly load the relationship on a set of models.
 * @method static EloquentBuilder where($column, $operator = null, $value = null, $boolean = 'and') Add a basic where clause to the query.
 * @method EloquentBuilder orWhere($column, $operator = null, $value = null) Add an "or where" clause to the query.
 * @method EloquentBuilder has($relation, $operator = '>=', $count = 1, $boolean = 'and', Closure $callback = null) Add a relationship count condition to the query.
 * @method static EloquentBuilder|$this find($value)
 * @method static EloquentBuilder|$this orderBy($column, $direction = 'asc')
 * @method static EloquentBuilder|$this select($columns = ['*'])
 * @method static EloquentBuilder|$this create(array $data) create
 * @method static EloquentBuilder|$this update(int $id, array $data) update
 * @method static EloquentBuilder|$this findOrFail(int|string $id)
 * @method static EloquentBuilder|$this whereRaw($sql, array $bindings = [])
 * @method static EloquentBuilder|$this whereBetween($column, array $values)
 * @method static EloquentBuilder|$this whereNotBetween($column, array $values)
 * @method static EloquentBuilder|$this whereNested(Closure $callback)
 * @method static EloquentBuilder|$this addNestedWhereQuery($query)
 * @method static EloquentBuilder|$this whereExists(Closure $callback)
 * @method static EloquentBuilder|$this whereNotExists(Closure $callback)
 * @method static EloquentBuilder|$this whereIn($column, $values)
 * @method static EloquentBuilder|$this whereNotIn($column, $values)
 * @method static EloquentBuilder|$this whereNull($column)
 * @method static EloquentBuilder|$this whereNotNull($column)
 * @method static EloquentBuilder|$this orWhereRaw($sql, array $bindings = [])
 * @method static EloquentBuilder|$this orWhereBetween($column, array $values)
 * @method static EloquentBuilder|$this orWhereNotBetween($column, array $values)
 * @method static EloquentBuilder|$this orWhereExists(Closure $callback)
 * @method static EloquentBuilder|$this orWhereNotExists(Closure $callback)
 * @method static EloquentBuilder|$this orWhereIn($column, $values)
 * @method static EloquentBuilder|$this orWhereNotIn($column, $values)
 * @method static EloquentBuilder|$this orWhereNull($column)
 * @method static EloquentBuilder|$this orWhereNotNull($column)
 * @method static EloquentBuilder|$this whereDate($column, $operator, $value)
 * @method static EloquentBuilder|$this whereDay($column, $operator, $value)
 * @method static EloquentBuilder|$this whereMonth($column, $operator, $value)
 * @method static EloquentBuilder|$this whereYear($column, $operator, $value)
 */
abstract class Model extends BaseModel
{

    protected static array $rules = [];

    protected $guarded = [
        'id',
        'created_by_id',
        'updated_by_id',
        'created_at',
        'updated_at',
    ];


    public static function booted()
    {
        parent::booted();
        static::saving(function (BaseModel $model) {
            if (count(static::$rules)) {
                $model->makeVisible($model->hidden);
                $validator = Validator::make($model->toArray(), static::$rules);
                $model->makeHidden($model->hidden);
                if ($validator->fails()) {
                    throw new ValidationException($validator, new JsonResponse($validator->errors()->getMessages(), 422));
                }
            }
        });
    }

    /**
     * @param array[] $data
     * @return $this[]
     * @throws Throwable
     */
    public static function createMany(array $data): array
    {
        try {
            DB::beginTransaction();
            $saved = [];
            foreach ($data as $entity) {
                $saved[] = static::create($entity);
            }
            DB::commit();
            return $saved;
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        } catch (Throwable $throwable) {
            DB::rollBack();
            throw $throwable;
        }
    }

    /**
     * @param iterable $data
     * @return $this[]
     * @throws Throwable
     */
    public static function saveMany(iterable $data): array
    {
        try {
            DB::beginTransaction();
            $saved = [];
            foreach ($data as $entity) {
                $saved[] = $entity->save();
            }
            DB::commit();
            return $saved;
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        } catch (Throwable $throwable) {
            DB::rollBack();
            throw $throwable;
        }
    }

    /**
     * @param iterable $data
     * @return $this[]
     * @throws Throwable
     */
    public static function pushMany(iterable $data): array
    {
        try {
            DB::beginTransaction();
            $saved = [];
            foreach ($data as $entity) {
                $saved[] = $entity->push();
            }
            DB::commit();
            return $saved;
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        } catch (Throwable $throwable) {
            DB::rollBack();
            throw $throwable;
        }
    }
}

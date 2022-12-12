<?php

namespace App\Models;

use App\Traits\WhoDidIt;
use Auth;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Imagine\Image\Box;
use Imagine\Imagick\Imagine;
use Spatie\ImageOptimizer\OptimizerChainFactory;
use Storage;

/**
 * App\Models\File
 *
 * @property int $id
 * @property string $name
 * @property string $file_path
 * @property string $type
 * @property int $size
 * @property int $width
 * @property int $height
 * @property int $company_id
 * @property int $created_by_id
 * @property int $updated_by_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|File newModelQuery()
 * @method static Builder|File newQuery()
 * @method static Builder|File query()
 * @method static Builder|File search(?string $search = null)
 * @method static Builder|File whereCompanyId($value)
 * @method static Builder|File whereCreatedAt($value)
 * @method static Builder|File whereCreatedById($value)
 * @method static Builder|File whereFilePath($value)
 * @method static Builder|File whereHeight($value)
 * @method static Builder|File whereId($value)
 * @method static Builder|File whereName($value)
 * @method static Builder|File whereSize($value)
 * @method static Builder|File whereType($value)
 * @method static Builder|File whereUpdatedAt($value)
 * @method static Builder|File whereUpdatedById($value)
 * @method static Builder|File whereWidth($value)
 * @mixin Eloquent
 * @property-read User|null $created_by
 * @property-read User|null $updated_by
 * @property-read Collection|Equipment[] $equipments
 * @property-read int|null $equipments_count
 * @property-read array $responsive_images
 * @property-read string $url
 */
class File extends Model
{
    use HasFactory;
    use WhoDidIt;

    public const DIMENSIONS = [
        'thumbnail' => [
            'width' => 150,
            'height' => 150,
        ],
        'medium' => [
            'width' => 300,
            'height' => 300,
        ],
        'medium_large' => [
            'width' => 768,
            'height' => 768,
        ],
        'large' => [
            'width' => 1024,
            'height' => 1024,
        ],
    ];

    protected $appends = ['responsive_images', 'url'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function (File $file) {
            $file->company_id = $file->company_id ?: Auth::user()->company_id;
        });
    }

    public function scopeSearch(Builder $query, string $search = null) {
        return $query->when($search, function (Builder $query, $search) {
            return $query->where('name', 'LIKE', '%' . $search . '%');
        });
    }

    protected function responsiveImages(): Attribute
    {
        return Attribute::get(function ($value, $attribute) {
            $responsive = [];
            if (str_contains($attribute['type'], 'image')) {

                $extension = pathinfo($this->file_path, PATHINFO_EXTENSION);
                foreach (File::DIMENSIONS as $name => $dimensions) {
                    $responsive[$name] = Storage::url(str_replace('.' . $extension, '', $attribute['file_path']) . '_' . $name . '.' . $extension);
                }

                return $responsive;
            } else {
                foreach (File::DIMENSIONS as $name => $dimensions) {
                    $responsive[$name] = Storage::url($attribute['file_path']);
                }
            }

            return $responsive;
        });
    }

    public function getUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }

    public function equipments(): BelongsToMany
    {
        return $this->belongsToMany(Equipment::class);
    }

    /**
     * @throws Exception
     */
    public static function upload(string|UploadedFile $file, ?string $name = null): File
    {
        if (is_string($file)) {
            $file = new UploadedFile($file, basename($file), filetype($file));
        }
        $dims = getimagesize($file->getRealPath());
        $uniqueNumber = time();

        $path = Storage::putFileAs('/uploads', $file, strtolower(str_replace('.' . $file->getClientOriginalExtension(), '', $file->getClientOriginalName())) . '_' . $uniqueNumber . '.' . $file->getClientOriginalExtension());
        if (!$path) {
            throw new Exception('Unable to upload file');
        }
        /** @var File $media */
        $media = File::create([
            'name' => $name ?: $file->getClientOriginalName(),
            'file_path' => $path,
            'type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'width' => $dims[0],
            'height' => $dims[1],
            'company_id' => Auth::user()->company_id,
        ]);

        if (str_contains($file->getMimeType(), 'image')) {
            $optimizerChain = OptimizerChainFactory::create();
            $optimizerChain->optimize($file->getRealPath());
            foreach (File::DIMENSIONS as $name => $dims) {
                $tmp = tempnam(sys_get_temp_dir(), $name) . '.' . $file->getClientOriginalExtension();
                $size = new Box($dims['width'], $dims['height']);
                $imagine = new Imagine();

                // Save that modified file to our temp file
                $imagine = $imagine->open($file->getRealPath());
                if ($file->getClientOriginalExtension() !== 'svg') {
                    $imagine = $imagine->thumbnail($size);
                }
                $imagine->save($tmp);


                $optimizerChain->optimize($tmp);
                $thumbnail = new UploadedFile($tmp, $file->getClientOriginalName());
                Storage::putFileAs('/uploads', $thumbnail, strtolower(str_replace('.' . $file->getClientOriginalExtension(), '', $file->getClientOriginalName())) . '_' . $uniqueNumber . '_' . $name . '.' . $file->getClientOriginalExtension());
                unlink($tmp);
            }
        }

        return $media;
    }
}

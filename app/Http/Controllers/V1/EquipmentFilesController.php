<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\File;
use Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Imagine\Image\Box;
use Imagine\Image\ManipulatorInterface;
use Imagine\Imagick\Imagine;
use Spatie\ImageOptimizer\OptimizerChainFactory;
use Storage;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EquipmentFilesController extends Controller
{
    public function media(): JsonResponse
    {
        $media = File::search(request()->input('search'))
            ->where(['created_by_id' => Auth::id()])
            ->whereHas('equipments', function (Builder $query) {
                return $query->where('equipments.id', request('equipment_id'));
            })
            ->latest()
            ->paginate(request()->input('pcount'));

        return response()->json($media);
    }

    public function file($id): JsonResponse
    {
        $file = File::findOrFail($id);

        return response()->json(compact('file'));
    }

    public function save(): JsonResponse
    {
        $file = File::findOrFail(request()->input('id'));
        $file->company_id = Auth::user()->company_id;
        $file->save();

        return response()->json(['message' => 'The files information has been updated.']);
    }

    /**
     * Delete a file
     */
    public function delete(): JsonResponse
    {
        $items = request()->input('items');
        if (!$items) {
            throw new NotFoundHttpException();
        }
        $medias = File::whereIn('id', $items)->get();
        $equipment = Equipment::findOrFail(request('equipment_id'));

        $equipment->files()->detach($medias);


        return response()->json(['message' => 'The selected file(s) have been deleted.']);
    }

    /**
     * Upload a file
     *
     */
    public function upload(): JsonResponse
    {
        $file = request()->file('file');
        $dims = getimagesize($file->getRealPath());
        $uniqueNumber = time();

        $equipment = Equipment::findOrFail(request('equipment_id'));

        $path = Storage::putFileAs('/uploads/' . Auth::id(), $file, strtolower(str_replace('.' . $file->getClientOriginalExtension(), '', $file->getClientOriginalName())) . '_' . $uniqueNumber . '.' . $file->getClientOriginalExtension());
        /** @var File $media */
        $media = $equipment->files()->create([
            'name' => $file->getClientOriginalName(),
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
                Storage::putFileAs('/uploads/' . Auth::id(), $thumbnail, strtolower(str_replace('.' . $file->getClientOriginalExtension(), '', $file->getClientOriginalName())) . '_' . $uniqueNumber . '_' . $name . '.' . $file->getClientOriginalExtension());
                unlink($tmp);
            }
        }

        return response()->json([
            'message' => 'The ' . $media->name . ' file has been uploaded.',
            'file_name' => $media->name,
            'file' => $media,
        ]);
    }

    public function thumbnail(int $id): RedirectResponse
    {
        $file = File::findOrFail($id);
        return response()->redirectTo($file->responive_images['thumbnail']);
    }

    public function image($id, $size = 'thumbnail'): RedirectResponse
    {
        $file = File::findOrFail($id);
        return response()->redirectTo($file->responsive_images[$size]);
    }
}

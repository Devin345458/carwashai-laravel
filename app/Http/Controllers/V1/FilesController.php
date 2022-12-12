<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\File;
use Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\ManipulatorInterface;
use Imagine\Imagick\Imagine;
use Spatie\ImageOptimizer\OptimizerChainFactory;
use Storage;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FilesController extends Controller
{
    public function media(): JsonResponse
    {
        $media = File::search(request()->input('search'))
            ->where(['created_by_id' => Auth::id()])
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
        $medias = File::whereIn('id', $items);

        $medias->delete();

        return response()->json(['message' => 'The selected file(s) have been deleted.']);
    }

    /**
     * Upload a file
     *
     */
    public function upload(): JsonResponse
    {
        $file = request()->file('file');
        $media = File::upload($file);

        return response()->json([
            'message' => 'The ' . $media->name . ' file has been uploaded.',
            'file_name' => $media->name,
            'file' => $media,
        ]);
    }

    public function thumbnail(int $id): RedirectResponse
    {
        $file = File::findOrFail($id);
        return response()->redirectTo($file->responsive_images['thumbnail']);
    }

    public function image($id, $size = 'thumbnail'): RedirectResponse
    {
        $file = File::findOrFail($id);
        return response()->redirectTo($file->responsive_images[$size]);
    }
}

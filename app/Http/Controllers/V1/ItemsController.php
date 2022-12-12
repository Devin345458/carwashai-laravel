<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\File;
use App\Models\Inventory;
use App\Models\Item;
use Auth;
use DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Imagine\Image\Box;
use Imagine\Imagick\Imagine;
use Spatie\ImageOptimizer\OptimizerChainFactory;
use Storage;

class ItemsController extends Controller
{
    public function search(): JsonResponse
    {
        $items = Item::where('company_id', Auth::user()->company_id)
            ->when(request('search'), function (Builder $query, string $search) {
                $query->where(function (Builder $query) use ($search) {
                    $query->orWhere('items.name', 'LIKE', '%' . $search . '%');
                    $query->orWhere('items.description', 'LIKE', '%' . $search . '%');
                });
            })
            ->when(request('excludeInventory'), function (Builder $query, string $storeId) {
                $query->whereDoesntHave('inventories', function (Builder $query) use ($storeId) {
                    $query->where('inventories.store_id', $storeId);
                });
            })
            ->when(request()->input('type_id'), function (Builder $query, int $typeId) {
              return $query->where('item_type_id', $typeId);
            })
            ->latest()
            ->limit(10)
            ->with([ 'active_store_inventory', 'file'])
            ->get();

        if (request()->input('selected') && !$items->where('id', request()->input('selected'))->first()) {
            $items->push(Item::find(request()->input('selected'))->load(['active_store_inventory', 'file']));
        }

        return response()->json(compact('items'));
    }

    /**
     * Add method
     */
    public function upsert(): JsonResponse
    {
        $data = request()->input();
        if ($data['id']) {
            $item = Item::find($data['id'])->load('active_store_inventory');
        } else {
            $item = new Item;
        }
        $item->fill($data);
        $item->company_id = Auth::user()->company_id;
        $item->save();
        $item->load('file');
        return response()->json(compact('item'));
    }

    public function importVendorProducts(): JsonResponse
    {
        $products = request('products');
        foreach ($products as $product) {
            $search = ['item_type_id' => 1, 'name' => $product['name']];
            $item = Item::firstOrNew($search, $search);
            $item->description = $product['description'];
            if (!$item->exists) {
                $file = tmpfile();
                file_put_contents($file, file_get_contents($product['image']));
                $file = new UploadedFile($file, $product['image']);
                $dims = getimagesize($file->getRealPath());
                $uniqueNumber = time();

                $path = Storage::putFileAs('/uploads/88473e17-2c39-4a88-800c-2dfce45eca07', $file, strtolower(str_replace('.' . $file->getClientOriginalExtension(), '', $file->getClientOriginalName())) . '_' . $uniqueNumber . '.' . $file->getClientOriginalExtension());
                /** @var File $media */
                $media = File::create([
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
                        Storage::putFileAs('/uploads/88473e17-2c39-4a88-800c-2dfce45eca07', $thumbnail, strtolower(str_replace('.' . $file->getClientOriginalExtension(), '', $file->getClientOriginalName())) . '_' . $uniqueNumber . '_' . $name . '.' . $file->getClientOriginalExtension());
                        unlink($tmp);
                    }
                }
                $item->file_id = $media->id;
                $item->save();

                $item->inventories()->create([
                    'store_id' => 'b95a2471-51e9-4648-ab9b-99dfd2eb195c',
                    'supplier_id' => 1,
                    'cost' => $product['price'],
                    'current_stock' => 0,
                    'desired_stock' => 0,
                    'created_by_id' => '88473e17-2c39-4a88-800c-2dfce45eca07',
                    'updated_by_id' => '88473e17-2c39-4a88-800c-2dfce45eca07',
                ]);
            }
        }

        return response()->json(['success' => true]);
    }
}

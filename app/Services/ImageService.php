<?php

namespace App\Services;

use App\Models\ProductImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageService
{
    public function uploadProductImage(UploadedFile $file, string $folder = 'products'): string
    {
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs($folder, $filename, 'public');
        return $path;
    }

    public function deleteImage(string $path): void
    {
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    public function attachImagesToProduct(int $productId, array $files, bool $isPrimary = false): void
    {
        foreach ($files as $index => $file) {
            $path = $this->uploadProductImage($file);
            ProductImage::create([
                'product_id'  => $productId,
                'image_path'  => $path,
                'is_primary'  => $isPrimary && $index === 0,
                'sort_order'  => $index,
            ]);
        }
    }
}

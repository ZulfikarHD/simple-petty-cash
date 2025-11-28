<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ReceiptService
{
    private const MAX_WIDTH = 1200;

    private const JPEG_QUALITY = 80;

    private const STORAGE_DISK = 'public';

    private const RECEIPTS_FOLDER = 'receipts';

    /**
     * Store and compress a receipt image.
     * Compresses image to max 1200px width and optimizes file size.
     */
    public function storeReceipt(UploadedFile $file, int $userId): string
    {
        $image = $this->createImageFromFile($file);

        if ($image === null) {
            return $this->storeWithoutCompression($file, $userId);
        }

        $resizedImage = $this->resizeImage($image);

        $filename = $this->generateFilename($userId);
        $path = self::RECEIPTS_FOLDER.'/'.$filename;

        ob_start();
        imagejpeg($resizedImage, null, self::JPEG_QUALITY);
        $imageData = ob_get_clean();

        Storage::disk(self::STORAGE_DISK)->put($path, $imageData);

        imagedestroy($image);
        imagedestroy($resizedImage);

        return $path;
    }

    /**
     * Delete a receipt from storage.
     */
    public function deleteReceipt(?string $path): bool
    {
        if (empty($path)) {
            return false;
        }

        if (Storage::disk(self::STORAGE_DISK)->exists($path)) {
            return Storage::disk(self::STORAGE_DISK)->delete($path);
        }

        return false;
    }

    /**
     * Replace an existing receipt with a new one.
     */
    public function replaceReceipt(?string $oldPath, UploadedFile $newFile, int $userId): string
    {
        $this->deleteReceipt($oldPath);

        return $this->storeReceipt($newFile, $userId);
    }

    /**
     * Get the full URL for a receipt path.
     */
    public function getReceiptUrl(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        return Storage::disk(self::STORAGE_DISK)->url($path);
    }

    /**
     * Check if a receipt exists.
     */
    public function receiptExists(?string $path): bool
    {
        if (empty($path)) {
            return false;
        }

        return Storage::disk(self::STORAGE_DISK)->exists($path);
    }

    /**
     * Create GD image resource from uploaded file.
     */
    private function createImageFromFile(UploadedFile $file): ?\GdImage
    {
        $mimeType = $file->getMimeType();
        $filePath = $file->getPathname();

        return match ($mimeType) {
            'image/jpeg', 'image/jpg' => @imagecreatefromjpeg($filePath),
            'image/png' => @imagecreatefrompng($filePath),
            'image/gif' => @imagecreatefromgif($filePath),
            'image/webp' => @imagecreatefromwebp($filePath),
            default => null,
        };
    }

    /**
     * Resize image if it exceeds max width while maintaining aspect ratio.
     */
    private function resizeImage(\GdImage $image): \GdImage
    {
        $originalWidth = imagesx($image);
        $originalHeight = imagesy($image);

        if ($originalWidth <= self::MAX_WIDTH) {
            $newImage = imagecreatetruecolor($originalWidth, $originalHeight);
            imagecopy($newImage, $image, 0, 0, 0, 0, $originalWidth, $originalHeight);

            return $newImage;
        }

        $ratio = self::MAX_WIDTH / $originalWidth;
        $newWidth = self::MAX_WIDTH;
        $newHeight = (int) ($originalHeight * $ratio);

        $resized = imagecreatetruecolor($newWidth, $newHeight);

        imagecopyresampled(
            $resized,
            $image,
            0,
            0,
            0,
            0,
            $newWidth,
            $newHeight,
            $originalWidth,
            $originalHeight
        );

        return $resized;
    }

    /**
     * Store file without compression (fallback for unsupported formats).
     */
    private function storeWithoutCompression(UploadedFile $file, int $userId): string
    {
        $extension = $file->getClientOriginalExtension() ?: 'jpg';
        $filename = $this->generateFilename($userId, $extension);
        $path = self::RECEIPTS_FOLDER.'/'.$filename;

        Storage::disk(self::STORAGE_DISK)->putFileAs(
            self::RECEIPTS_FOLDER,
            $file,
            $filename
        );

        return $path;
    }

    /**
     * Generate a unique filename for the receipt.
     */
    private function generateFilename(int $userId, string $extension = 'jpg'): string
    {
        $timestamp = now()->format('Ymd_His');
        $random = Str::random(8);

        return "receipt_{$userId}_{$timestamp}_{$random}.{$extension}";
    }
}

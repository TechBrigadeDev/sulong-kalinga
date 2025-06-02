<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadService
{
    /**
     * Upload a file to the specified disk and path.
     * 
     * @param UploadedFile $file
     * @param string $disk 'spaces' or 'spaces-private'
     * @param string $directory
     * @param array $options
     * @return string File path
     */
    public function upload(UploadedFile $file, string $disk = 'spaces', string $directory = 'uploads', array $options = []): string
    {
        $extension = $file->getClientOriginalExtension();
        $filename = $options['filename'] ?? (Str::uuid() . '.' . $extension);
        $path = $file->storeAs($directory, $filename, $disk);

        if (!$path) {
            throw new \Exception('File upload failed at storage layer.');
        }

        return $path;
    }

    /**
     * Get a public URL for a file.
     */
    public function getPublicUrl(string $path): string
    {
        return Storage::disk('spaces')->url($path);
    }

    /**
     * Get a temporary URL for a private file.
     */
    public function getTemporaryPrivateUrl(string $path, int $minutes = 15): string
    {
        return Storage::disk('spaces-private')->temporaryUrl($path, now()->addMinutes($minutes));
    }

    /**
     * Delete a file from the specified disk.
     */
    public function delete(string $path, string $disk = 'spaces'): bool
    {
        return Storage::disk($disk)->delete($path);
    }
}
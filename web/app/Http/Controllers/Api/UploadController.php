<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\UploadService;

class UploadController extends Controller
{
    protected $uploadService;

    public function __construct(UploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    public function upload(Request $request)
    {
        try {
            $allowedTypes = $request->input('allowed_types', 'jpg,jpeg,png,pdf,doc,docx,txt');
            $allowedTypesArray = explode(',', $allowedTypes);

            $validator = \Validator::make($request->all(), [
                'file' => [
                    'required',
                    'file',
                    'max:10240', // 10MB
                    'mimes:' . implode(',', $allowedTypesArray),
                ],
                'visibility' => 'nullable|in:public,private',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $file = $request->file('file');
            $visibility = $request->input('visibility', 'public');
            $disk = $visibility === 'private' ? 'spaces-private' : 'spaces';
            $directory = $request->input('directory', $visibility === 'private' ? 'private' : 'public');

            $path = $this->uploadService->upload($file, $disk, $directory);

            return response()->json([
                'success' => true,
                'file_id' => $path
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'File upload failed.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
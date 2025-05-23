<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadController extends Controller
{
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
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            $filename = \Illuminate\Support\Str::uuid() . '.' . $extension;
            // No table to associate uploads with, so we just store the file
            $path = $file->storeAs('uploads/mobile', $filename, 'public');

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
<?php

namespace App\Http\Controllers;

use App\Mail\SignatureMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Services\UploadService;

class SignatureController extends Controller
{
    protected $uploadService;

    public function __construct(UploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    public function sendSignature(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string'],
            'signature' => ['required']
        ]);

        $base64Image = preg_replace('/^data:image\/\w+;base64,/', '', $request->signature);
        $imageData = base64_decode($base64Image);

        $filename = 'image_' . time() . '.png';
        $directory = 'uploads/signatures';
        $filePath = $directory . '/' . $filename;

        // Store the file using UploadService (private)
        $this->uploadService->upload(
            new \Illuminate\Http\File(
                tap(tempnam(sys_get_temp_dir(), 'sig'), function ($tmpFile) use ($imageData) {
                    file_put_contents($tmpFile, $imageData);
                })
            ),
            'spaces-private',
            $directory,
            ['filename' => $filename]
        );

        // Get a temporary private URL for the signature
        $tempFileUrl = $this->uploadService->getTemporaryPrivateUrl($filePath, 10);

        Mail::to('jonipk28@gmail.com')->send(new SignatureMail($tempFileUrl));

        // Delete the file from storage after sending
        $this->uploadService->delete($filePath, 'spaces-private');

        session()->flash('alert-success', 'Signature mail sent successfully');

        return redirect('/');
    }
}
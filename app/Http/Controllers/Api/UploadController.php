<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Requests\PinUploadRequest;
use App\Http\Requests\UploadRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use App\PinCode;
use App\User;
use App\Gallery;
use Validator;
use Image;

class UploadController extends ApiController
{
    // upload fonksiyonu standart phpdir.
    // doğru dosya yoluna istenilen dosyayı yükler ve response olarak bilgileri döner.
    public function upload(UploadRequest $request)
    {
        if ($request->file('uploadFile')->isValid()) {
            $file = $request->uploadFile;
            $fileExtension = $request->uploadFile->extension();
            $filePath = $request->filePath;
            $fileParams = Str::slug($request->params, '-');
            $fileNewName = $fileParams . '-' . time() . '.' . $fileExtension;

            if ($file->move(public_path('/'.$filePath), $fileNewName)) {
                $fileUrl = url('/'.$filePath.$fileNewName);

                return response()->json([
                    'url'=>$fileUrl,
                    'path'=>$filePath,
                    'name'=>$fileNewName
                ]);
            }

        }
    }
    public function storage(UploadRequest $request)
    {
        // kullanıcıların kullanması gereken upload komutu storagedır.
        // izin verilen tek konuma yükleme yapar.
        if ($request->file('uploadFile')->isValid()) {
            $file = $request->uploadFile;
            $fileExtension = $request->uploadFile->extension();
            $fileParams = Str::slug($request->params, '-');
            $fileNewName = time() . '.' . $fileExtension;

            $path = $request->uploadFile->storeAs('/', $fileNewName, 'public');
            $fileUrl = url('/storage/'.$fileNewName);
            return response()->json([
                'url'=>$fileUrl,
                'name'=>$fileNewName
            ]);
        }
    }
    public function uploadExcel(UploadRequest $request)
    {
        if ($request->file('uploadFile')->isValid()) {
            $file = $request->uploadFile;
            $fileExtension = $request->uploadFile->extension();
            $fileNewName = 'lastUploadedPinsList.' . $fileExtension;

            $path = $request->uploadFile->storeAs('/', $fileNewName, 'public');
            $fileUrl = url('/storage/'.$fileNewName);
            $data = Storage::disk('public')->get($path);

            if ($data) {
                return $this->apiResponse(ResaultType::Success, $data, 'File Uploaded', 201);
            } else {
                return $this->apiResponse(ResaultType::Error, null, 'File can\'t uploaded', 500);
            }
        }
    }

}

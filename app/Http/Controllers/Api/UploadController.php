<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Requests\UploadRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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
            $fileNewName = $fileParams . '-' . time() . '.' . $fileExtension;

            $path = $request->uploadFile->storeAs('/', $fileNewName, 'public');
            $fileUrl = url('/storage/'.$fileNewName);
            return response()->json([
                'url'=>$fileUrl,
                'name'=>$fileNewName
            ]);
        }
    }
    public function s3(UploadRequest $request)
    {
        if ($request->file('uploadFile')->isValid()) {
            $auth = $request->header('Authorization');
            $token = str_replace('Bearer ', '', $auth);
            $user = User::where('api_token', $token)->select('id')->first();
            $website = $request->website;
            $file = $request->uploadFile;
            $fileExtension = $request->uploadFile->extension();
            $fileParams = Str::slug($request->params, '-');

            $fileNewName = $website.'-'.$user->id.'-'.$fileParams . '-' . time() . '.' . $fileExtension;
            $fileName = $request->uploadFile->getClientOriginalName();
            $small = Image::make($file)->resize(468, 468, function($constraint) { $constraint->aspectRatio(); })->encode($fileExtension);
            $normal = Image::make($file)->resize(768, 768, function($constraint) { $constraint->aspectRatio(); })->encode($fileExtension);
            $medium = Image::make($file)->resize(1024, 1024, function($constraint) { $constraint->aspectRatio(); })->encode($fileExtension);
            $large = Image::make($file)->resize(1920, 1920, function($constraint) { $constraint->aspectRatio(); })->encode($fileExtension);
            $img = '/uploads/'.$fileNewName;
            $imgmd = '/uploads/md/'.$fileNewName;
            $imgsm = '/uploads/sm/'.$fileNewName;
            $imglg = '/uploads/lg/'.$fileNewName;
            Storage::disk('s3')->put($img, (string)$normal, 'public');
            Storage::disk('s3')->put($imgmd, (string)$medium, 'public');
            Storage::disk('s3')->put($imgsm, (string)$small, 'public');
            Storage::disk('s3')->put($imglg, (string)$large, 'public');
            $data = new Gallery();
            $data->website = $website;
            $data->user = $user->id;
            $data->title = $fileParams;
            $data->order = 1;
            $data->photo = $fileNewName;
            $data->store = env('AWS_URL').'/uploads/';
            $data->save();
            return response()->json([
                'user'=>$user->id,
                'file'=>$fileNewName,
                'img'=>env('AWS_URL').$img,
                'imgmd'=>env('AWS_URL').$imgmd,
                'imgsm'=>env('AWS_URL').$imgsm,
                'imglg'=>env('AWS_URL').$imglg,
            ]);
        }
    }

    public function removeS3($filename)
    {
        $data = Gallery::where('photo','=',$filename)->first();
        if (count($data) >= 1) {
            $data->delete();
            Storage::disk('s3')->delete('uploads/'.$filename);
            Storage::disk('s3')->delete('uploads/lg/'.$filename);
            Storage::disk('s3')->delete('uploads/md/'.$filename);
            Storage::disk('s3')->delete('uploads/sm/'.$filename);
            return $this->apiResponse(ResaultType::Success, $data, 'Content Deleted', 200);
        } else {
            return $this->apiResponse(ResaultType::Error, $data, 'Deleted Error', 500);
        }
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
class UploadRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::check();
    }

    public function rules()
    {
        return [
            'uploadFile'  => 'required|image|mimes:jpeg,png,gif,jpg|max:10485760'
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rules\File;
use Illuminate\Foundation\Http\FormRequest;

class PostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'image' => ['required','array'],
            'image.*'=>[File::image()->max(2048)],
            'title' => ['required','unique:posts,title', 'max:255'],
            'body' => ['required'],
            'categories' => ['required', 'array'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'image.*.image' => 'The file type must only be jpg or jpeg or png!',
        ];
    }
}

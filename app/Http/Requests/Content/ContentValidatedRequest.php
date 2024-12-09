<?php

namespace App\Http\Requests\Content;

use Illuminate\Foundation\Http\FormRequest;

class ContentValidatedRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; 
    }
    /**

     *
     * @return array<string, 
     */
    public function rules(): array
    {
        return [
            'name' => ['required' , 'string'],
            "matter_id" => [ 'int']
        ];
    }

}

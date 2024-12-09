<?php

namespace App\Http\Requests\Matter;

use Illuminate\Foundation\Http\FormRequest;

class MatterValidatedRequest extends FormRequest
{
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
            'name' => ['required' , 'string']

        ];
    }
}

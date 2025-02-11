<?php
namespace App\Http\Requests\User;


use App\Enums\NotebookErrorEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserQuestionAnsweredRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'alternative' => ['nullable', 'in:A,B,C,D,E', 'string'],
            'error_notebook' => ['nullable', Rule::enum(NotebookErrorEnum::class)]
        ];
    }
}

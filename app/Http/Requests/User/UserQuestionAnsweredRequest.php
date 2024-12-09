<?php
namespace App\Http\Requests\User;

use App\Enums\NotebookErrorEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator;

class UserQuestionAnsweredRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation()
    {
        // Se os campos não forem enviados, define-os como null
        $this->merge([
            'error_notebook' => $this->input('error_notebook', null)
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => ['required', 'numeric'],
            'question_id' => ['required', 'numeric'],
            'alternative' => ['required', 'in:A,B,C,D,e'],
            'error_notebook' => ['nullable', Rule::enum(NotebookErrorEnum::class)]
        ];
    }

}

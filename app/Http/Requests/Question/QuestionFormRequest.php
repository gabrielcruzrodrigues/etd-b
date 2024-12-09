<?php
namespace App\Http\Requests\Question;

use App\Enums\Difficulty;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class QuestionFormRequest extends FormRequest
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
            'content_id' => $this->input('content_id', null),
            'topic_id' => $this->input('topic_id', null),
            'subtopic_id' => $this->input('subtopic_id', null),
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
            'institution' => ['required', 'string'],
            'query' => ['required', 'string'],
            'alternative_a' => ['string', 'required'],
            'alternative_b' => ['string', 'required'],
            'alternative_c' => ['string', 'required'],
            'alternative_d' => ['string', 'required'],
            'alternative_e' => ['string'],
            'answer' => ['string', 'required'],
            'matter_id' => ['string', 'required'],
            'content_id',
            'topic_id',
            'subtopic_id',
            'difficulty' => [Rule::enum(Difficulty::class)],
            'year' => ['required', 'string'],
        ];
    }

}

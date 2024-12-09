<?php
namespace App\Http\Requests\Question;

use App\Enums\Difficulty;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class QuestionUpdateRequest extends FormRequest
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
            'institution_id' => ['numeric'],
            'query' => ['string'],
            'alternative_a' => ['string'],
            'alternative_b' => ['string'],
            'alternative_c' => ['string'],
            'alternative_d' => ['string'],
            'alternative_e' => ['string'],
            'answer' => ['string'],
            'matter_id' => ['numeric'],
            'content_id' => ['numeric'],
            'topic_id' => ['numeric'],
            'subtopic_id' => ['numeric'],
            'difficulty' => [Rule::enum(Difficulty::class)],
            'year_id' => ['numeric'],
        ];
    }
}

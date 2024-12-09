<?php

namespace App\Http\Requests\Subtopic;

use Illuminate\Foundation\Http\FormRequest;

class SubtopicRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
      return [
        'name' => 'required|string|min:1|max:80|unique:topics,name,'.$this->id,
        'topic_id' => 'required|integer',
      ];
    }
}

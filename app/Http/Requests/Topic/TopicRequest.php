<?php

namespace App\Http\Requests\Topic;

use Illuminate\Foundation\Http\FormRequest;

class TopicRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|min:1|max:80|unique:topics,name,'.$this->id,
            'content_id' => 'required|integer',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class sendPasswordResetLinkRequest extends FormRequest
{
  public function authorize()
  {
    return true;
  }

  public function rules()
  {
    return [
      'email' => 'required|email',
    ];
  }

  public function messages()
  {
    return [
      'email.required' => 'The email field is required.',
      'email.email' => 'Please provide a valid email address.',
    ];
  }
}

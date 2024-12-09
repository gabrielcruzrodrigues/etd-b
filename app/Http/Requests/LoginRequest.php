<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
   * @return array
   */
  public function rules()
  {
    return [
      'email' => 'required|email|max:255',
      'password' => 'required|string|min:8',
    ];
  }

  /**
   * Custom messages for validation errors.
   *
   * @return array
   */
  public function messages()
  {
    return [
      'email.required' => 'The email field is required.',
      'email.email' => 'Please enter a valid email address.',
      'password.required' => 'The password field is required.',
      'password.min' => 'The password must be at least 8 characters long.',
    ];
  }
}

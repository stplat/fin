<?php

namespace App\Http\Requests\Application;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ApplicationUpload extends FormRequest
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
      'file' => ['required'],
      'periods' => ['required'],
      'article' => ['required'],
      'version' => ['required'],
      'version_budget' => ['required'],
      'version_involvement' => ['required'],
      'version_f22' => ['required'],
      'version_shipment' => ['required'],
    ];
  }

  /**
   * Get the error messages for the defined validation rules.
   *
   * @return array
   */
  public function messages()
  {
    return [
      'file.required' => 'Параметр <strong>Файл</strong> обязателен для заполнения',
      'periods.required' => 'Параметр <strong>Период</strong> обязателен для заполнения',
      'version.required' => 'Параметр <strong>Версия</strong> обязателен для заполнения',
      'version_involvement.required' => 'Параметр <strong>Версия вовлечения</strong> обязателен для заполнения',
    ];
  }

  /**
   * Handle a failed validation attempt.
   *
   * @param \Illuminate\Contracts\Validation\Validator $validator
   * @return void
   *
   * @throws \Illuminate\Validation\ValidationException
   */
  protected function failedValidation(Validator $validator)
  {
    throw new HttpResponseException(response()->json(['errors' => $validator->errors()]));
  }
}

<?php

namespace App\Http\Requests\Budget;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class BudgetUpdate extends FormRequest
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
      'period' => ['required', 'numeric'],
      'version' => ['required'],
      'region' => ['required'],
      'regions' => ['required'],
      'activity' => ['required'],
      'article' => ['required'],
      'param' => ['required'],
      'value' => ['required'],
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
      'period.required' => 'Параметр <strong>Период</strong> обязателен для заполнения',
      'period.numeric' => 'Параметр <strong>Период</strong> должен быть числом',
      'version.required' => 'Параметр <strong>Версия</strong> обязателен для заполнения',
      'region.required' => 'Параметр <strong>Регион</strong> обязателен для заполнения',
      'regions.required' => 'Параметр <strong>Регионы для выборки</strong> обязателен для заполнения',
      'activity.required' => 'Параметр <strong>Вид деятельности</strong> обязателен для заполнения',
      'article.required' => 'Параметр <strong>Статья</strong> обязателен для заполнения',
      'param.required' => 'Параметр <strong>Название параметра</strong> обязателен для заполнения',
      'value.required' => 'Параметр <strong>Значение</strong> обязателен для заполнения',

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

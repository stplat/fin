<?php

namespace App\Http\Requests\Application;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ApplicationUpdate extends FormRequest
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
      'periods' => ['required', 'array'],
      'article' => ['required'],
      'version' => ['required'],
      'version_budget' => ['required'],
      'version_involvement' => ['required'],
      'version_f22' => ['required'],
      'version_shipment' => ['required'],
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
      'periods.required' => 'Параметр <strong>Период</strong> обязателен для заполнения',
      'periods.array' => 'Параметр <strong>Период</strong> должен быть массивом',
      'article.required' => 'Параметр <strong>Статья</strong> обязателен для заполнения',
      'version.required' => 'Параметр <strong>Версия</strong> обязателен для заполнения',
      'version_budget.required' => 'Параметр <strong>Версия бюджета</strong> обязателен для заполнения',
      'version_involvement.required' => 'Параметр <strong>Версия вовлечения</strong> обязателен для заполнения',
      'version_f22.required' => 'Параметр <strong>Версия формы 22</strong> обязателен для заполнения',
      'version_shipment.required' => 'Параметр <strong>Версия плана поставки</strong> обязателен для заполнения',
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

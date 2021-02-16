<?php

namespace App\Http\Requests\Material;

use App\Models\Material;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class MaterialPull extends FormRequest
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
      'id' => ['required'],
      'value' => ['required', 'numeric', 'min:1', function ($attribute, $value, $fail) {
        if (!is_string($value)) {
          $material = Material::find($this->id);

          return $material->unused < $value ?:
            $fail('Количество материалов для передачи не может превышать общее количество невостребованных');
        }
      }],
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
      'id.required' => 'Поле <strong>id</strong> обязательно для заполнения',
      'value.required' => 'Поле <strong>со значением</strong> обязательно для заполнения',
      'value.numeric' => 'Поле <strong>со значением</strong> должно быть положительным числом',
      'value.min' => 'Поле <strong>со значением</strong> должно быть положительным числом',
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

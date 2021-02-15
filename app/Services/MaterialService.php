<?php

namespace App\Services;

use App\Models\Material;
use Illuminate\Support\Facades\Auth;

class MaterialService
{
  /**
   * Получаем все материалы относящиеся к ДКРЭ пользователя
   *
   * @param $periods array
   *
   * @return \Illuminate\Support\Collection
   */
  public function getMaterials()
  {
    $auth = Auth::user()->dkre_id;

    return Material::with('dkre')->whereHas('dkre', function ($query) use ($auth) {
      if ($auth === 1) {
        return $query;
      } else {
        return $query->where('id', $auth);
      }
    })->get();
  }

  /**
   * Получаем все неиспользуемые материалы не относящиеся к ДКРЭ пользователя
   *
   * @return \Illuminate\Support\Collection
   */
  public function getUnusedMaterials()
  {
    $auth = Auth::user()->dkre_id;

    return Material::with('dkre')->where('unused', '!=', 0)->whereHas('dkre', function ($query) use ($auth) {
      if ($auth === 1) {
        return $query;
      } else {
        return $query->where('id', '!=', $auth);
      }
    })->get();
  }

  /**
   * Переводим материал в статус неликвида, для передачи другим ДКРЭ
   *
   * @param $id integer
   * @param $value double
   *
   * @return \Illuminate\Support\Collection
   */
  public function toUnused($id, $value)
  {
    $material = Material::find($id);

    $changed = $material->update([
      'unused' => $value + $material->unused
    ]);

    return !$changed ?: $this->getMaterials();
  }
}

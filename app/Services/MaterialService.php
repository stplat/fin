<?php

namespace App\Services;

use App\Models\Material;
use App\Models\OrderMaterial;
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
      if ($auth === 16) {
        return $query;
      } else {
        return $query->where('id', $auth);
      }
    })->get()->sortBy('total')->values();
  }

  /**
   * Получаем все неиспользуемые материалы не относящиеся к ДКРЭ пользователя
   *
   * @return \Illuminate\Support\Collection
   */
  public function getUnusedMaterials()
  {
    $auth = Auth::user()->dkre_id;

    return Material::with(['dkre', 'order_materials'])
      ->where('unused', '!=', 0)
      ->whereHas('dkre', function ($query) use ($auth) {
        if ($auth === 16) {
          return $query;
        } else {
          return $query->where('id', '!=', $auth);
        }
      })->get()->map(function ($item) {
        return collect($item)->put('reserved', $item->order_materials->sum('quantity'));
      })->filter(function ($item) {
        return $item['unused'] - $item['reserved'] != 0;
      })->values();
  }

  /**
   * Получаем все заявленные на получение материалы
   *
   * @return \Illuminate\Support\Collection
   */
  public function getOrderMaterials()
  {
    $auth = Auth::user()->dkre_id;

    return OrderMaterial::with(['dkre', 'material' => function ($query) {
      return $query->with('dkre');
    }])
      ->whereHas('dkre', function ($query) use ($auth) {
        if ($auth === 16) {
          return $query;
        } else {
          return $query->where('id', '=', $auth);
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

  /**
   * Передаем невостребованный материал другой ДКРЭ
   *
   * @param $id integer
   * @param $value double
   *
   * @return \Illuminate\Support\Collection
   */
  public function toTransfer($id, $value)
  {
    $orderMaterial = new OrderMaterial();
    $orderMaterial->material_id = $id;
    $orderMaterial->dkre_id = Auth::user()->dkre_id;
    $orderMaterial->quantity = $value;
    $orderMaterial->save();

    $material = Material::find($id);

    $changed = $material->update([
      'reserved' => $value + $material->reserved
    ]);

    return !$changed ?: $this->getUnusedMaterials();
  }
}

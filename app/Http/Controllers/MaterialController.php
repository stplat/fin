<?php

namespace App\Http\Controllers;

use App\Http\Requests\Material\MaterialToUnused;
use App\Services\MaterialService;

class MaterialController extends Controller
{
  protected $materialService;

  public function __construct(MaterialService $materialService)
  {
    $this->materialService = $materialService;
  }

  /**
   * Отображаем представление с материалами
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    return view('material.warehouse')->with([
      'materials' => $this->materialService->getMaterials()
    ]);
  }

  /**
   * Отображаем представление с неликвидными материалами
   *
   * @return \Illuminate\Http\Response
   */
  public function unused()
  {
    return view('material.unused')->with([
      'materials' => $this->materialService->getUnusedMaterials()
    ]);
  }

  /**
   * Переводим материал в статус неликвида, для передачи другим ДКРЭ
   *
   * @return \Illuminate\Support\Collection
   */
  public function toUnused(MaterialToUnused $request)
  {
    $id = $request->input('id');
    $value = $request->input('value');

    return $this->materialService->toUnused($id, $value);
  }
}

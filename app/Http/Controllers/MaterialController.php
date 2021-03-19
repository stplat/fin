<?php

namespace App\Http\Controllers;

use App\Http\Requests\Material\MaterialPull;
use App\Http\Requests\Material\MaterialPush;
use App\Http\Requests\Material\MaterialAll;
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
    $materials = $this->materialService->getMaterials();

    return view('material.warehouse')->with([
      'data' => collect([
        'materials' => $materials,
        'articles' => $this->materialService->getArticles()
      ])
    ]);
  }

  /**
   * Передаем материалы
   *
   * @param MaterialAll $request
   * @return \Illuminate\Support\Collection
   */
  public function all(MaterialAll $request)
  {
    return $this->materialService->getMaterials($request->input('article_id'));
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
   * Отображаем заявки на перераспределение
   *
   * @return \Illuminate\Http\Response
   */
  public function orders()
  {
    return view('material.orders')->with([
      'materials' => $this->materialService->getOrderMaterials()
    ]);
  }

  /**
   * Переводим материал в статус неликвида, для передачи другим ДКРЭ
   *
   * @param MaterialPush $request
   * @return \Illuminate\Support\Collection
   */
  public function push(MaterialPush $request)
  {
    $id = $request->input('id');
    $value = $request->input('value');

    return $this->materialService->toUnused($id, $value);
  }

  /**
   * Забирает материал, который числится в неликвидах у других РДКРЭ
   *
   * @param MaterialPull $request
   * @return \Illuminate\Support\Collection
   */
  public function pull(MaterialPull $request)
  {
    $id = $request->input('id');
    $value = $request->input('value');

    return $this->materialService->toTransfer($id, $value);
  }
}

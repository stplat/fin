<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Involvement;
use App\Services\BudgetService;
use App\Http\Requests\Budget\BudgetAll;
use App\Http\Requests\Budget\BudgetUpdate;
use App\Http\Requests\Budget\BudgetUpload;
use App\Http\Requests\Budget\BudgetExport;

class BudgetController extends Controller
{
  protected $budgetService;

  public function __construct(BudgetService $budgetService)
  {
    $this->budgetService = $budgetService;
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
//    dd($this->budgetService->getBudget([2], 10, 1)->toArray());
    return view('budget')->with([
      'data' => collect([
        'dkres' => $this->budgetService->getDkres(),
        'regions' => $this->budgetService->getRegions(),
        'periods' => $this->budgetService->getPeriods(),
        'versions' => $this->budgetService->getVersions(),
      ])
    ]);
  }

  /**
   * Все данные бюджета
   *
   * @param \App\Http\Requests\Budget\BudgetAll
   * @return \Illuminate\Support\Collection
   */
  public function all(BudgetAll $request)
  {
    $regions = $request->input('regions');
    $periods = $request->input('periods');
    $version = $request->input('version') ?: null;
    $version_involvement = $request->input('version_involvement');

    return $this->budgetService->getBudget($periods, $version, $version_involvement, $regions);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param \App\Http\Requests\Budget\BudgetUpdate
   * @return \Illuminate\Support\Collection
   */
  public function update(BudgetUpdate $request)
  {
    $region = $request->input('region');
    $regions = $request->input('regions') ?: null;
    $period = $request->input('period');
    $periods = $request->input('periods');
    $version = $request->input('version');
    $version_involvement = $request->input('version_involvement');

    Involvement::updateOrCreate([
      'dkre_id' => $region,
      'period_id' => $period,
      'version_id' => $version_involvement,
      'activity_type_id' => $request->input('activity'),
      'payment_balance_article_general' => $request->input('article'),
    ],
      [
        $request->input('param') => $request->input('value')
      ]
    );

    return $this->budgetService->getBudget($periods, $version, $version_involvement, $regions);
  }

  /**
   * Обновляем данные из файла
   *
   * @param \App\Http\Requests\Budget\BudgetUpdate
   * @return \Illuminate\Support\Collection
   */
  public function upload(BudgetUpload $request)
  {
    $file = $request->file('file');
    $regions = $request->input('regions') ?: null;
    $periods = is_array($request->input('periods')) ? $request->input('periods') : [$request->input('periods')];
    $version = $request->input('version');
    $version_involvement = $request->input('version_involvement');
    $data = $this->budgetService->getUploadFile($file, $version);

    Budget::where('version_id', $version)->delete();
    Budget::insert($data);

    return $this->budgetService->getBudget($periods, $version, $version_involvement, $regions);
  }

  /**
   * Консолидируем квартал
   *
   * @param BudgetExport $request
   * @return \Illuminate\Support\Collection
   * @throws \PhpOffice\PhpSpreadsheet\Exception
   * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
   */
  public function export(BudgetExport $request)
  {
    $period = $request->input('period');
    $version = $request->input('version');
    return $this->budgetService->export($period, $version);
  }
}

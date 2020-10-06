<?php

namespace App\Http\Controllers;

use App\Services\FinanceService;
use App\Http\Requests\Finance\FinanceAll;
use App\Http\Requests\Finance\FinanceUpload;
use App\Models\Finance;

class FinanceController extends Controller
{
  protected $financeService;

  public function __construct(FinanceService $financeService)
  {
    $this->financeService = $financeService;
  }

  public function index()
  {
//    dd($this->financeService->getFinances([3], 11)->toArray());
    return view('finance')->with([
      'data' => collect([
        'periods' => $this->financeService->getPeriods(),
        'versions' => $this->financeService->getVersions(),
      ])
    ]);
  }

  public function all(FinanceAll $request)
  {
    $periods = $request->input('periods');
    $version = $request->input('version') ?: null;

    return $this->financeService->getFinances($periods, $version);
  }

  /**
   * Обновляем данные из файла
   *
   * @param \App\Http\Requests\Budget\BudgetUpdate
   * @return \Illuminate\Support\Collection
   */
  public function upload(FinanceUpload $request)
  {
    $file = $request->file('file');
    $periods = is_array($request->input('periods')) ? $request->input('periods') : [$request->input('periods')];
    $version = $request->input('version');
    $data = $this->financeService->getUploadFile($file, $version);

    Finance::where('version_id', $version)->delete();
    Finance::insert($data);

    return $this->financeService->getFinances($periods, $version);
  }
}

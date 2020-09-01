<?php

namespace App\Http\Controllers;

use App\Services\FinanceService;
use App\Http\Requests\Finance\FinanceAll;

class FinanceController extends Controller
{
  protected $financeService;

  public function __construct(FinanceService $financeService)
  {
    $this->financeService = $financeService;
  }

  public function index()
  {
//    dd($this->financeService->getFinances([1], 2)->toArray());
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
}

<?php

namespace App\Http\Controllers;

use App\Services\ApplicationService;
use App\Http\Requests\Finance\FinanceAll;

class ApplicationController extends Controller
{
  protected $applicationService;

  public function __construct(ApplicationService $applicationService)
  {
    $this->applicationService = $applicationService;
  }

  public function index()
  {
//    dd($this->applicationService->getApplications([1], 2)->toArray());
    return view('application')->with([
      'data' => collect([
        'periods' => $this->applicationService->getPeriods(),
        'versions' => $this->applicationService->getVersions(),
      ])
    ]);
  }

  public function all(FinanceAll $request)
  {
    $periods = $request->input('periods');
    $version = $request->input('version') ?: null;

    return $this->applicationService->getFinances($periods, $version);
  }
}
